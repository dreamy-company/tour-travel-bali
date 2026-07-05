<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', 'SB-Mid-server-defaultkey');
        $this->clientKey = config('services.midtrans.client_key', 'SB-Mid-client-defaultkey');
        $this->isProduction = (bool) config('services.midtrans.is_production', false);
        
        $this->baseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Create Snap transaction and return the token and redirect URL.
     *
     * @param Booking $booking
     * @return array{token: string, redirect_url: string}
     * @throws \RuntimeException
     */
    public function createSnapTransaction(Booking $booking): array
    {
        $orderId = 'BOOK-' . $booking->id . '-' . time();
        $grossAmount = (int) round((float) $booking->total_price);

        $customer = $booking->customer;

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone_number ?? '',
            ],
            'credit_card' => [
                'secure' => true,
            ],
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
        ->withBasicAuth($this->serverKey, '')
        ->post($this->baseUrl, $payload);

        if ($response->failed()) {
            Log::error('Midtrans Snap request failed', [
                'booking_id' => $booking->id,
                'response' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to generate secure payment from Midtrans: ' . $response->reason());
        }

        $data = $response->json();

        if (! isset($data['token']) || ! isset($data['redirect_url'])) {
            Log::error('Midtrans Snap response missing token or redirect_url', [
                'booking_id' => $booking->id,
                'response' => $data,
            ]);
            throw new \RuntimeException('Invalid response structure from Midtrans.');
        }

        return [
            'token' => $data['token'],
            'redirect_url' => $data['redirect_url'],
        ];
    }

    /**
     * Verify the webhook signature key.
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $receivedSignatureKey
     * @return bool
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $receivedSignatureKey): bool
    {
        $input = $orderId . $statusCode . $grossAmount . $this->serverKey;
        $hash = hash('sha512', $input);

        return hash_equals($hash, $receivedSignatureKey);
    }
}

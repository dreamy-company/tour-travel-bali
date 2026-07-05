<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\MidtransService;
use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Generate secure Snap Token and redirect URL for a booking.
     *
     * @param Request $request
     * @param Booking $booking
     * @return JsonResponse
     */
    public function checkout(Request $request, Booking $booking): JsonResponse
    {
        // Enforce authorization: only the customer who created the booking can checkout
        if ($booking->customer_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        // Only allow checkout if booking is pending confirmation
        if ($booking->status !== BookingStatus::PENDING_CONFIRMATION) {
            return response()->json(['error' => 'This booking cannot be checked out in its current state.'], 400);
        }

        try {
            $transaction = $this->midtransService->createSnapTransaction($booking);

            return response()->json([
                'success' => true,
                'token' => $transaction['token'],
                'redirect_url' => $transaction['redirect_url'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle incoming webhook notification from Midtrans.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signatureKey) {
            return response()->json(['error' => 'Invalid webhook payload.'], 400);
        }

        // Verify signature
        if (! $this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans Webhook Invalid Signature', ['payload' => $payload]);
            return response()->json(['error' => 'Invalid signature.'], 403);
        }

        // Extract booking_id from order_id (Format: BOOK-{booking_id}-{timestamp})
        $parts = explode('-', $orderId);
        if (count($parts) < 2 || $parts[0] !== 'BOOK') {
            return response()->json(['error' => 'Invalid order format.'], 400);
        }

        $bookingId = (int) $parts[1];
        $booking = Booking::with('escrowTransaction')->find($bookingId);

        if (! $booking) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }

        // Check if payment was successful
        $isSuccess = false;

        if ($transactionStatus === 'settlement') {
            $isSuccess = true;
        } elseif ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $isSuccess = true;
            }
        }

        if ($isSuccess) {
            DB::transaction(function () use ($booking): void {
                // Update booking status to confirmed so the guide can see it
                $booking->update([
                    'status' => BookingStatus::CONFIRMED,
                ]);

                // Update escrow transaction status to paid_in_escrow
                if ($booking->escrowTransaction) {
                    $booking->escrowTransaction->update([
                        'status' => EscrowStatus::PAID_IN_ESCROW,
                    ]);
                }
            });

            Log::info("Booking ID {$bookingId} successfully marked as PAID and CONFIRMED via webhook.");
        }

        return response()->json(['success' => true]);
    }
}

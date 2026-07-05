<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Models\Booking;
use App\Models\GuideWallet;
use Illuminate\Support\Facades\DB;

class EscrowReleaseService
{
    /**
     * Release the escrow transaction gross amount (minus 10% commission) 
     * to the guide's wallet upon completion of the tour booking.
     *
     * @param Booking $booking
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function release(Booking $booking): void
    {
        // Enforce the booking is completed
        if ($booking->status !== BookingStatus::COMPLETED) {
            throw new \InvalidArgumentException('Booking must be completed to release escrow.');
        }

        $escrow = $booking->escrowTransaction;

        if (! $escrow) {
            throw new \RuntimeException('No escrow transaction found associated with this booking.');
        }

        // Avoid double releasing
        if ($escrow->status === EscrowStatus::RELEASED_TO_GUIDE) {
            return;
        }

        DB::transaction(function () use ($booking, $escrow): void {
            $grossAmount = (float) $escrow->gross_amount;
            
            // Calculate a 10% platform commission fee
            $commission = $grossAmount * 0.10;
            $netAmount = $grossAmount - $commission;

            // 1. Update escrow_transactions status to released_to_guide, commission, and net amount
            $escrow->update([
                'status' => EscrowStatus::RELEASED_TO_GUIDE,
                'platform_commission' => $commission,
                'guide_net_amount' => $netAmount,
            ]);

            // 2. Find or create guide wallet
            $wallet = GuideWallet::firstOrCreate(
                ['guide_id' => $booking->guide_id],
                ['current_balance' => 0.00]
            );

            // 3. Increment current_balance in the guide's wallet by netAmount
            $wallet->increment('current_balance', $netAmount);
        });
    }

    /**
     * Administrative override: release escrow on a disputed booking to the guide.
     * Bypasses the COMPLETED status guard and marks the booking as completed after payout.
     *
     * @param Booking $booking
     * @return void
     * @throws \RuntimeException
     */
    public function releaseForDispute(Booking $booking): void
    {
        $escrow = $booking->escrowTransaction;

        if (! $escrow) {
            throw new \RuntimeException('No escrow transaction found associated with this booking.');
        }

        if ($escrow->status === EscrowStatus::RELEASED_TO_GUIDE) {
            return;
        }

        DB::transaction(function () use ($booking, $escrow): void {
            $grossAmount = (float) $escrow->gross_amount;
            $commission = $grossAmount * 0.10;
            $netAmount = $grossAmount - $commission;

            $escrow->update([
                'status' => EscrowStatus::RELEASED_TO_GUIDE,
                'platform_commission' => $commission,
                'guide_net_amount' => $netAmount,
            ]);

            $wallet = GuideWallet::firstOrCreate(
                ['guide_id' => $booking->guide_id],
                ['current_balance' => 0.00]
            );

            $wallet->increment('current_balance', $netAmount);

            // Resolve the dispute — mark booking as completed
            $booking->update(['status' => BookingStatus::COMPLETED]);
        });
    }
}

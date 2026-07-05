<?php

namespace App\Livewire\Guide;

use App\Enums\WithdrawalStatus;
use App\Models\GuideWallet;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Payout Management')]
class PayoutManagement extends Component
{
    // Withdrawal form fields
    #[Validate('required|numeric|min:1')]
    public string $amount = '';

    #[Validate('required|string|max:100')]
    public string $bankName = '';

    #[Validate('required|string|min:6|max:30')]
    public string $bankAccountNumber = '';

    #[Validate('required|string|min:3|max:100')]
    public string $bankAccountName = '';

    /**
     * Supported Indonesian banks for the dropdown.
     *
     * @return array<string>
     */
    public function banks(): array
    {
        return [
            'BCA', 'BNI', 'BRI', 'Mandiri', 'CIMB Niaga',
            'Danamon', 'Permata Bank', 'BTN', 'Bank Jago',
            'Bank Syariah Indonesia (BSI)', 'Sea Bank', 'Jenius (BTPN)',
        ];
    }

    /**
     * Get the guide's wallet record (or null if not yet created).
     */
    private function fetchWallet(): ?GuideWallet
    {
        return GuideWallet::where('guide_id', Auth::id())->first();
    }

    /**
     * Get the guide's withdrawal history (latest first).
     *
     * @return Collection<int, Withdrawal>
     */
    private function fetchWithdrawals(): Collection
    {
        return Withdrawal::where('guide_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Submit a payout request.
     */
    public function requestPayout(): void
    {
        $this->validate();

        $wallet = $this->fetchWallet();
        $currentBalance = $wallet ? (float) $wallet->current_balance : 0.0;
        $requested = (float) $this->amount;

        if ($requested > $currentBalance) {
            $this->addError(
                'amount',
                __('Withdrawal amount cannot exceed your available balance of Rp :balance.', [
                    'balance' => number_format($currentBalance, 0, ',', '.'),
                ])
            );
            return;
        }

        DB::transaction(function () use ($wallet, $requested): void {
            // 1. Deduct from wallet (create record if it somehow doesn't exist)
            if ($wallet) {
                $wallet->decrement('current_balance', $requested);
            } else {
                GuideWallet::create([
                    'guide_id' => Auth::id(),
                    'current_balance' => 0.00,
                ]);
            }

            // 2. Create a pending withdrawal record
            Withdrawal::create([
                'guide_id' => Auth::id(),
                'amount' => $requested,
                'bank_name' => $this->bankName,
                'bank_account_number' => $this->bankAccountNumber,
                'bank_account_name' => $this->bankAccountName,
                'status' => WithdrawalStatus::PENDING,
            ]);
        });

        // Reset form
        $this->amount = '';
        $this->bankName = '';
        $this->bankAccountNumber = '';
        $this->bankAccountName = '';

        session()->flash('success', __('Payout request submitted successfully. Processing within 1–2 business days.'));
    }

    /**
     * Render the component view.
     * Wallet and withdrawal history are passed directly to avoid
     * Eloquent Collections entering Livewire's serialization pipeline.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.guide.payout-management', [
            'wallet' => $this->fetchWallet(),
            'withdrawals' => $this->fetchWithdrawals(),
        ]);
    }
}

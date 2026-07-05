<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\GuideProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CheckExpiredLicenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guides:check-expired-licenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend all guide accounts whose KTPP or SKCK license has expired.';

    /**
     * Execute the console command.
     *
     * Queries guide_profiles for records where ktpp_expired_at OR skck_expired_at
     * is on or before today, whose linked user is currently active.
     * Each matching guide has their users.status flipped to suspended atomically.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        $this->info("[{$today->toDateString()}] Scanning for expired guide licenses...");

        // Eager-load the user so we can update status and display info without N+1 queries.
        $expiredProfiles = GuideProfile::with('user')
            ->whereHas('user', function ($query): void {
                $query->where('role', UserRole::GUIDE)
                      ->where('status', UserStatus::ACTIVE);
            })
            ->where(function ($query) use ($today): void {
                $query->whereDate('ktpp_expired_at', '<=', $today)
                      ->orWhereDate('skck_expired_at', '<=', $today);
            })
            ->get();

        if ($expiredProfiles->isEmpty()) {
            $this->line('  <fg=green>✓</> No expired licenses found. All guides are compliant.');
            return self::SUCCESS;
        }

        $this->warn("  Found {$expiredProfiles->count()} guide(s) with expired license(s). Suspending...");

        $suspendedCount = 0;

        foreach ($expiredProfiles as $profile) {
            $user = $profile->user;

            // Collect which documents are expired for the log output
            $reasons = [];
            if ($profile->ktpp_expired_at !== null && $profile->ktpp_expired_at->lte($today)) {
                $reasons[] = 'KTPP expired ' . $profile->ktpp_expired_at->toDateString();
            }
            if ($profile->skck_expired_at !== null && $profile->skck_expired_at->lte($today)) {
                $reasons[] = 'SKCK expired ' . $profile->skck_expired_at->toDateString();
            }

            DB::transaction(function () use ($user): void {
                $user->update(['status' => UserStatus::SUSPENDED]);
            });

            $suspendedCount++;

            $this->line(
                sprintf(
                    '  <fg=yellow>→</> Suspended guide <fg=white>%s</> (ID: %d) — %s',
                    $user->name,
                    $user->id,
                    implode(', ', $reasons)
                )
            );
        }

        $this->newLine();
        $this->info("  Done. {$suspendedCount} guide account(s) suspended.");

        return self::SUCCESS;
    }
}

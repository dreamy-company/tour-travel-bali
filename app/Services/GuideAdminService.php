<?php

namespace App\Services;

use App\Enums\EscrowStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Destructive admin operations on guide accounts:
 * delete the service repository (tour packages), delete KYC documents,
 * and permanently delete a guide account.
 *
 * Assumes the caller has already authorized the request (admin role).
 */
final class GuideAdminService
{
    /**
     * The document columns (KYC uploads + headshot) stored on guide_profiles.
     *
     * @var array<int, string>
     */
    private const DOCUMENT_FIELDS = [
        'ktp_photo',
        'headshot',
        'ktpp_file',
        'skck_file',
        'surat_sehat_file',
    ];

    /**
     * Delete every tour package owned by the guide (their service repository).
     *
     * Bookings keep their tour_package_id nulled by the foreign key.
     */
    public function deleteRepository(User $guide): int
    {
        return $this->guideOrFail($guide)->tourPackages()->delete();
    }

    /**
     * Delete the guide's uploaded documents from storage, wipe the columns,
     * and revoke verification until documents are re-submitted.
     *
     * Returns the number of files physically removed.
     */
    public function deleteDocuments(User $guide): int
    {
        $profile = $this->guideOrFail($guide)->guideProfile;

        if (! $profile) {
            return 0;
        }

        $deleted = 0;

        DB::transaction(function () use ($profile, &$deleted): void {
            foreach (self::DOCUMENT_FIELDS as $field) {
                $path = $profile->getAttribute($field);

                if (is_string($path) && $path !== '' && Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->delete($path);
                    $deleted++;
                }
            }

            $profile->update([
                'ktp_photo' => null,
                'headshot' => null,
                'ktpp_file' => null,
                'skck_file' => null,
                'surat_sehat_file' => null,
                'is_verified' => false,
                'rejection_reason' => null,
            ]);
        });

        return $deleted;
    }

    /**
     * Whether the guide still holds escrow funds for active bookings.
     * Permanently deleting such a guide would destroy those funds, so
     * deletion must be blocked until the bookings are resolved.
     */
    public function hasActiveFunds(User $guide): bool
    {
        return $this->guideOrFail($guide)
            ->guideBookings()
            ->whereHas('escrowTransaction', fn ($query) => $query->where('status', EscrowStatus::PAID_IN_ESCROW))
            ->exists();
    }

    /**
     * Permanently delete a guide account and every owned record.
     *
     * Document files are removed first; the user row delete cascades to
     * the guide profile, tour packages, wallet, withdrawals, bookings,
     * reviews, favorites, and chat messages.
     */
    public function deleteGuide(User $guide): void
    {
        $guide = $this->guideOrFail($guide);

        DB::transaction(function () use ($guide): void {
            $this->deleteDocuments($guide);

            $guide->delete();
        });
    }

    /**
     * Resolve the guide user or fail.
     */
    private function guideOrFail(User $guide): User
    {
        return User::where('role', UserRole::GUIDE)->findOrFail($guide->id);
    }
}

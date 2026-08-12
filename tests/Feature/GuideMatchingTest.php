<?php

namespace Tests\Feature;

use App\Enums\TariffMode;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Customer\GuideSearch;
use App\Models\GuideProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuideMatchingTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedGuide(string $name, string $style, array $specializations, float $rate): User
    {
        $guide = User::factory()->guide()->create([
            'name' => $name,
            'status' => UserStatus::ACTIVE,
        ]);

        GuideProfile::factory()->create([
            'user_id' => $guide->id,
            'communication_style' => $style,
            'specializations' => $specializations,
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => $rate,
            'is_verified' => true,
        ]);

        return $guide;
    }

    /**
     * FR-02-01: Guides are filtered by activity specialization (same interest).
     */
    public function test_guides_can_be_filtered_by_specialization(): void
    {
        $this->makeVerifiedGuide('Wayan', 'edukatif', ['culture_history'], 600000);
        $this->makeVerifiedGuide('Made', 'ekspresif', ['nightlife'], 500000);

        Livewire::test(GuideSearch::class)
            ->set('selectedSpecializations', ['nightlife'])
            ->assertSee('Made')
            ->assertDontSee('Wayan');
    }

    /**
     * FR-02-01: Guides are filtered by communication style (same vibe).
     */
    public function test_guides_can_be_filtered_by_communication_style(): void
    {
        $this->makeVerifiedGuide('Wayan', 'edukatif', ['culture_history'], 600000);
        $this->makeVerifiedGuide('Made', 'santai', ['nature'], 500000);

        Livewire::test(GuideSearch::class)
            ->set('communicationStyle', 'edukatif')
            ->assertSee('Wayan')
            ->assertDontSee('Made');
    }

    /**
     * FR-02-01: Combined specialization + communication style + price filters.
     */
    public function test_guides_can_be_filtered_by_combined_matching_parameters(): void
    {
        $this->makeVerifiedGuide('Wayan', 'edukatif', ['culture_history'], 600000);
        $this->makeVerifiedGuide('Nyoman', 'edukatif', ['photography'], 400000);

        Livewire::test(GuideSearch::class)
            ->set('communicationStyle', 'edukatif')
            ->set('selectedSpecializations', ['photography'])
            ->set('maxPrice', '450000')
            ->assertSee('Nyoman')
            ->assertDontSee('Wayan');
    }

    /**
     * FR-02-01: Unverified guides never appear in the matching results.
     */
    public function test_unverified_guides_are_excluded_from_matching(): void
    {
        $verified = $this->makeVerifiedGuide('Wayan', 'santai', ['nature'], 500000);

        $unverified = User::factory()->guide()->create(['name' => 'Hidden Guide']);
        GuideProfile::factory()->create([
            'user_id' => $unverified->id,
            'communication_style' => 'santai',
            'specializations' => ['nature'],
            'is_verified' => false,
        ]);

        Livewire::test(GuideSearch::class)
            ->set('communicationStyle', 'santai')
            ->assertSee($verified->name)
            ->assertDontSee('Hidden Guide');
    }
}

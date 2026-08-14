<?php

namespace Tests\Feature;

use App\Enums\TariffMode;
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

    private function makeVerifiedGuide(string $name, string $style, array $specializations, float $rate, ?string $birthDate = null): User
    {
        $guide = User::factory()->guide()->create([
            'name' => $name,
            'status' => UserStatus::ACTIVE,
            'birth_date' => $birthDate,
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

    /**
     * FR-02-01 extension: guides are filtered by zodiac sign.
     */
    public function test_guides_can_be_filtered_by_zodiac_sign(): void
    {
        // Leo: 1990-08-10, Virgo: 1990-09-10
        $this->makeVerifiedGuide('Wayan', 'edukatif', ['culture_history'], 600000, '1990-08-10');
        $this->makeVerifiedGuide('Made', 'ekspresif', ['nightlife'], 500000, '1990-09-10');

        Livewire::test(GuideSearch::class)
            ->set('zodiacSign', 'leo')
            ->assertSee('Wayan')
            ->assertDontSee('Made');
    }

    /**
     * FR-02-01 extension: cosmic match ranks guides by compatibility with
     * the logged-in customer's sign (highest first).
     */
    public function test_cosmic_match_ranks_guides_by_customer_compatibility(): void
    {
        $customer = User::factory()->customer()->create([
            'birth_date' => '1990-08-10', // Leo
        ]);

        // Sagittarius (Leo × Sagittarius = 92), Virgo (Leo × Virgo = 70)
        $this->makeVerifiedGuide('SagittariusGuide', 'edukatif', ['nature'], 500000, '1990-12-01');
        $this->makeVerifiedGuide('VirgoGuide', 'santai', ['nature'], 500000, '1990-09-10');

        Livewire::actingAs($customer)
            ->test(GuideSearch::class)
            ->set('cosmicMatch', true)
            ->assertSeeInOrder(['SagittariusGuide', 'VirgoGuide']);
    }

    /**
     * FR-02-01 extension: guides without a birth date sort last under
     * cosmic match, and no score is shown for them.
     */
    public function test_cosmic_match_puts_guides_without_birth_date_last(): void
    {
        $customer = User::factory()->customer()->create([
            'birth_date' => '1990-08-10', // Leo
        ]);

        $this->makeVerifiedGuide('WithSign', 'edukatif', ['nature'], 500000, '1990-12-01'); // Sagittarius, 92
        $this->makeVerifiedGuide('NoSign', 'santai', ['nature'], 500000); // no birth date

        Livewire::actingAs($customer)
            ->test(GuideSearch::class)
            ->set('cosmicMatch', true)
            ->assertSeeInOrder(['WithSign', 'NoSign']);
    }

    /**
     * FR-02-01 extension: cosmic match is a no-op for guests (no customer
     * sign), so default ordering is kept.
     */
    public function test_cosmic_match_requires_an_authenticated_customer_with_birth_date(): void
    {
        $this->makeVerifiedGuide('Wayan', 'edukatif', ['nature'], 500000, '1990-12-01');

        Livewire::test(GuideSearch::class)
            ->set('cosmicMatch', true)
            ->assertSee('Wayan');
    }
}

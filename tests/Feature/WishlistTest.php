<?php

namespace Tests\Feature;

use App\Enums\TariffMode;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Customer\GuideSearch;
use App\Livewire\Customer\Wishlist;
use App\Models\Favorite;
use App\Models\GuideProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedGuide(string $name = 'Wayan'): User
    {
        $guide = User::factory()->guide()->create(['name' => $name, 'status' => UserStatus::ACTIVE]);
        GuideProfile::factory()->create([
            'user_id' => $guide->id,
            'is_verified' => true,
            'base_rate' => 500000.00,
            'tariff_mode' => TariffMode::DAILY,
        ]);
        return $guide;
    }

    public function test_customer_can_toggle_favorite_from_directory(): void
    {
        $customer = User::factory()->customer()->create();
        $guide = $this->makeVerifiedGuide();

        Livewire::actingAs($customer)
            ->test(GuideSearch::class)
            ->call('toggleFavorite', $guide->id);

        $this->assertDatabaseHas('favorites', [
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
        ]);

        Livewire::actingAs($customer)
            ->test(GuideSearch::class)
            ->call('toggleFavorite', $guide->id);

        $this->assertDatabaseMissing('favorites', [
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
        ]);
    }

    public function test_guest_toggle_redirects_to_login(): void
    {
        $guide = $this->makeVerifiedGuide();

        Livewire::test(GuideSearch::class)
            ->call('toggleFavorite', $guide->id)
            ->assertRedirect(route('login'));
    }

    public function test_wishlist_lists_saved_guides(): void
    {
        $customer = User::factory()->customer()->create();
        $guide = $this->makeVerifiedGuide('Made');

        Favorite::create(['customer_id' => $customer->id, 'guide_id' => $guide->id]);

        $this->actingAs($customer)
            ->get(route('favorites'))
            ->assertOk()
            ->assertSee('Made')
            ->assertSee('View Profile');
    }

    public function test_wishlist_remove_action(): void
    {
        $customer = User::factory()->customer()->create();
        $guide = $this->makeVerifiedGuide('Nyoman');

        Favorite::create(['customer_id' => $customer->id, 'guide_id' => $guide->id]);

        Livewire::actingAs($customer)
            ->test(Wishlist::class)
            ->call('removeFavorite', $guide->id);

        $this->assertDatabaseMissing('favorites', [
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
        ]);
    }

    public function test_guest_cannot_access_favorites(): void
    {
        $this->get(route('favorites'))->assertRedirect(route('login'));
    }
}

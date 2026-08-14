<?php

namespace Tests\Feature;

use App\Livewire\Customer\ProfileSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile(): void
    {
        $this->get(route('customer.profile'))->assertRedirect(route('login'));
    }

    public function test_customer_can_update_profile_information(): void
    {
        $customer = User::factory()->customer()->create([
            'name' => 'Old Name',
            'phone_number' => null,
        ]);

        Livewire::actingAs($customer)
            ->test(ProfileSettings::class)
            ->set('name', 'New Name')
            ->set('email', 'new@example.com')
            ->set('phone_number', '081234567890')
            ->set('birth_date', '1990-08-10')
            ->call('updateProfile');

        $customer->refresh();
        $this->assertEquals('New Name', $customer->name);
        $this->assertEquals('new@example.com', $customer->email);
        $this->assertEquals('081234567890', $customer->phone_number);
        $this->assertEquals('1990-08-10', $customer->birth_date->format('Y-m-d'));
        $this->assertNull($customer->email_verified_at);
    }

    public function test_customer_can_clear_birth_date(): void
    {
        $customer = User::factory()->customer()->create([
            'birth_date' => '1990-08-10',
        ]);

        Livewire::actingAs($customer)
            ->test(ProfileSettings::class)
            ->set('birth_date', '')
            ->call('updateProfile');

        $this->assertNull($customer->refresh()->birth_date);
    }

    public function test_future_birth_date_is_rejected(): void
    {
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($customer)
            ->test(ProfileSettings::class)
            ->set('birth_date', now()->addYear()->toDateString())
            ->call('updateProfile')
            ->assertHasErrors('birth_date');
    }

    public function test_customer_can_change_password(): void
    {
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($customer)
            ->test(ProfileSettings::class)
            ->set('current_password', 'password')
            ->set('new_password', 'NewPassword123!')
            ->set('new_password_confirmation', 'NewPassword123!')
            ->call('updatePassword');

        $customer->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $customer->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($customer)
            ->test(ProfileSettings::class)
            ->set('current_password', 'wrong-password')
            ->set('new_password', 'NewPassword123!')
            ->set('new_password_confirmation', 'NewPassword123!')
            ->call('updatePassword')
            ->assertHasErrors('current_password');
    }

    public function test_customer_can_save_traveler_persona_preferences(): void
    {
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($customer)
            ->test(ProfileSettings::class)
            ->set('traveler_preferences', ['introvert', 'cafe_hopper'])
            ->call('updatePreferences');

        $customer->refresh();
        $this->assertEquals(['introvert', 'cafe_hopper'], $customer->traveler_preferences);
    }

    public function test_invalid_persona_preferences_are_rejected(): void
    {
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($customer)
            ->test(ProfileSettings::class)
            ->set('traveler_preferences', ['not_a_real_persona'])
            ->call('updatePreferences')
            ->assertHasErrors('traveler_preferences.0');
    }
}

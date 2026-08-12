<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_customers_are_redirected_to_the_trips_hub(): void
    {
        $user = User::factory()->customer()->create();
        $this->actingAs($user);

        $this->get(route('dashboard'))->assertRedirect(route('customer.trips'));
        $this->get(route('customer.trips'))->assertOk();
    }

    public function test_admin_can_visit_the_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->get(route('dashboard'))->assertOk();
    }
}

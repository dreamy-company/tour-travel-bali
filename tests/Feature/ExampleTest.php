<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_a_successful_response(): void
    {
        // The home route redirects guests to the login screen
        // and authenticated users to their dashboard.
        $this->get(route('home'))->assertRedirect(route('login'));

        $this->get(route('login'))->assertOk();
    }
}

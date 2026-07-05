<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\GuideProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Livewire\Auth\GuideRegister;
use Tests\TestCase;

class GuideRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public registration route is accessible.
     */
    public function test_public_registration_route_is_accessible(): void
    {
        $response = $this->get(route('register.guide'));
        $response->assertOk();
    }

    /**
     * Test step-by-step registration flow.
     */
    public function test_guide_registration_onboarding_flow(): void
    {
        Storage::fake('local');

        // Step 1 files & data
        $ktpPhoto = UploadedFile::fake()->image('ktp.jpg');
        $headshot = UploadedFile::fake()->image('headshot.jpg');
        $ktppFile = UploadedFile::fake()->create('ktpp.pdf', 100);
        $skckFile = UploadedFile::fake()->create('skck.pdf', 100);
        $suratSehatFile = UploadedFile::fake()->create('medical.pdf', 100);

        Livewire::test(GuideRegister::class)
            // Step 1: Account Creation
            ->set('name', 'Ketut Wijaya')
            ->set('email', 'ketut@gmail.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('phone_number', '081234567891')
            ->call('nextStep')
            ->assertSet('currentStep', 2)

            // Step 2: Identity (Tier 1 KYC)
            ->set('ktp_number', '5102030405060708')
            ->set('languages', ['id', 'en'])
            ->set('bio', 'Certified Balinese culture guide.')
            ->set('ktp_photo', $ktpPhoto)
            ->set('headshot', $headshot)
            ->call('nextStep')
            ->assertSet('currentStep', 3)

            // Step 3: Legality (Tier 2 KYC)
            ->set('ktpp_number', 'HPI-88990')
            ->set('ktpp_expired_at', now()->addYear()->toDateString())
            ->set('ktpp_file', $ktppFile)
            ->set('skck_expired_at', now()->addYear()->toDateString())
            ->set('skck_file', $skckFile)
            ->set('surat_sehat_file', $suratSehatFile)
            ->call('nextStep')
            ->assertSet('currentStep', 4)

            // Step 4: Digital SOP Agreement & Submission
            ->set('signed_sop', true)
            ->call('register')
            ->assertRedirect(route('register.under-review'));

        // Verify DB Entries
        $user = User::where('email', 'ketut@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::GUIDE, $user->role);
        $this->assertEquals(UserStatus::PENDING_VERIFICATION, $user->status);

        $profile = GuideProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertFalse($profile->is_verified);
        $this->assertNotNull($profile->signed_sop_at);

        // Verify Secure file uploads to private local storage
        Storage::disk('local')->assertExists($profile->ktp_photo);
        Storage::disk('local')->assertExists($profile->headshot);
        Storage::disk('local')->assertExists($profile->ktpp_file);
        Storage::disk('local')->assertExists($profile->skck_file);
        Storage::disk('local')->assertExists($profile->surat_sehat_file);
    }
}

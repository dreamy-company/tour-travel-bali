<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Admin\UserManagement;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\GuideProfile;
use App\Models\GuideWallet;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    }

    private function makeGuide(array $overrides = []): User
    {
        return User::factory()->guide()->create(array_merge([
            'name' => 'Wayan Sudarta',
            'status' => UserStatus::ACTIVE,
        ], $overrides));
    }

    private function makeVerifiedProfile(User $guide, array $overrides = []): GuideProfile
    {
        return GuideProfile::factory()->create(array_merge([
            'user_id' => $guide->id,
            'is_verified' => true,
            'strikes' => 0,
        ], $overrides));
    }

    // ── Access control ────────────────────────────────────────────

    public function test_admin_can_open_user_management(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee('User Management');
    }

    public function test_customer_cannot_open_user_management(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)
            ->get(route('admin.users'))
            ->assertForbidden();
    }

    public function test_old_guides_url_redirects_to_user_management(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/guides')
            ->assertRedirect('/admin/users');
    }

    // ── Listing, search & filters ─────────────────────────────────

    public function test_customers_and_guides_are_listed(): void
    {
        $customer = User::factory()->customer()->create(['name' => 'Customer One']);
        $guide = $this->makeGuide(['name' => 'Guide One']);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->assertSee('Customer One')
            ->assertSee('Guide One');
    }

    public function test_users_can_be_filtered_by_role(): void
    {
        $customer = User::factory()->customer()->create(['name' => 'Customer One']);
        $guide = $this->makeGuide(['name' => 'Guide One']);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->set('roleFilter', 'customer')
            ->assertSee('Customer One')
            ->assertDontSee('Guide One');
    }

    public function test_users_can_be_searched_by_name(): void
    {
        $this->makeGuide(['name' => 'Wayan Sudarta']);
        User::factory()->customer()->create(['name' => 'Made Ariana']);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->set('search', 'Made')
            ->assertSee('Made Ariana')
            ->assertDontSee('Wayan Sudarta');
    }

    // ── Edit user ─────────────────────────────────────────────────

    public function test_admin_can_edit_customer_account_details(): void
    {
        $customer = User::factory()->customer()->create([
            'name' => 'Old Name',
            'phone_number' => null,
            'birth_date' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $customer->id)
            ->call('openEdit', $customer->id)
            ->set('editName', 'New Name')
            ->set('editEmail', 'new@example.com')
            ->set('editPhone', '081234567890')
            ->set('editBirthDate', '1990-08-10')
            ->set('editStatus', UserStatus::SUSPENDED->value)
            ->call('saveUser')
            ->assertHasNoErrors();

        $customer->refresh();
        $this->assertEquals('New Name', $customer->name);
        $this->assertEquals('new@example.com', $customer->email);
        $this->assertEquals('081234567890', $customer->phone_number);
        $this->assertEquals('1990-08-10', $customer->birth_date->format('Y-m-d'));
        $this->assertSame(UserStatus::SUSPENDED, $customer->status);
        $this->assertSame(UserRole::CUSTOMER, $customer->role);
    }

    public function test_admin_can_edit_guide_account_details(): void
    {
        $guide = $this->makeGuide(['name' => 'Wayan', 'status' => UserStatus::ACTIVE]);
        $this->makeVerifiedProfile($guide);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('openEdit', $guide->id)
            ->set('editName', 'Wayan Sudarta')
            ->set('editEmail', $guide->email)
            ->set('editStatus', UserStatus::BANNED->value)
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertSame(UserStatus::BANNED, $guide->refresh()->status);
        $this->assertSame(UserRole::GUIDE, $guide->role);
    }

    public function test_admin_can_change_customer_role_to_guide(): void
    {
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $customer->id)
            ->call('openEdit', $customer->id)
            ->set('editRole', UserRole::GUIDE->value)
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertSame(UserRole::GUIDE, $customer->refresh()->role);
    }

    public function test_guide_with_profile_cannot_be_demoted_to_customer(): void
    {
        $guide = $this->makeGuide();
        $this->makeVerifiedProfile($guide);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('openEdit', $guide->id)
            ->set('editRole', UserRole::CUSTOMER->value)
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertSame(UserRole::GUIDE, $guide->refresh()->role);
    }

    public function test_guide_without_profile_can_be_demoted_to_customer(): void
    {
        $guide = $this->makeGuide();

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('openEdit', $guide->id)
            ->set('editRole', UserRole::CUSTOMER->value)
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertSame(UserRole::CUSTOMER, $guide->refresh()->role);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $other = User::factory()->customer()->create(['email' => 'taken@example.com']);
        $customer = User::factory()->customer()->create(['email' => 'me@example.com']);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $customer->id)
            ->call('openEdit', $customer->id)
            ->set('editEmail', 'taken@example.com')
            ->call('saveUser')
            ->assertHasErrors('editEmail');
    }

    // ── Guide-specific actions ────────────────────────────────────

    public function test_admin_can_delete_guide_repository(): void
    {
        $guide = $this->makeGuide();
        $this->makeVerifiedProfile($guide);

        TourPackage::factory()->count(3)->create(['guide_id' => $guide->id]);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('deleteRepository')
            ->assertHasNoErrors();

        $this->assertSame(0, TourPackage::where('guide_id', $guide->id)->count());
        $this->assertDatabaseHas('users', ['id' => $guide->id]);
    }

    public function test_admin_can_delete_guide_documents(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('guide_documents/ktp_photos/ktp.jpg', 'image');
        Storage::disk('local')->put('guide_documents/ktpp_files/ktpp.pdf', 'pdf');

        $guide = $this->makeGuide();
        $profile = $this->makeVerifiedProfile($guide, [
            'ktp_photo' => 'guide_documents/ktp_photos/ktp.jpg',
            'ktpp_file' => 'guide_documents/ktpp_files/ktpp.pdf',
            'is_verified' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('deleteDocuments')
            ->assertHasNoErrors();

        $profile->refresh();

        $this->assertNull($profile->ktp_photo);
        $this->assertNull($profile->ktpp_file);
        $this->assertFalse($profile->is_verified);
        Storage::disk('local')->assertMissing('guide_documents/ktp_photos/ktp.jpg');
        Storage::disk('local')->assertMissing('guide_documents/ktpp_files/ktpp.pdf');
    }

    public function test_admin_can_permanently_delete_guide_without_active_funds(): void
    {
        $guide = $this->makeGuide();
        $this->makeVerifiedProfile($guide);
        GuideWallet::factory()->create(['guide_id' => $guide->id, 'current_balance' => 0.00]);
        TourPackage::factory()->create(['guide_id' => $guide->id]);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('deleteGuide')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', ['id' => $guide->id]);
        $this->assertDatabaseMissing('guide_profiles', ['user_id' => $guide->id]);
        $this->assertDatabaseMissing('guide_wallets', ['guide_id' => $guide->id]);
        $this->assertDatabaseMissing('tour_packages', ['guide_id' => $guide->id]);
    }

    public function test_guide_with_active_escrow_cannot_be_deleted(): void
    {
        $guide = $this->makeGuide();
        $this->makeVerifiedProfile($guide);

        $customer = User::factory()->customer()->create();

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'pickup_location' => 'Kuta Hotel',
            'custom_destinations' => ['Ubud'],
            'schedule_date' => now()->addDays(1)->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => 500000.00,
            'status' => BookingStatus::CONFIRMED,
        ]);

        EscrowTransaction::factory()->create([
            'booking_id' => $booking->id,
            'status' => EscrowStatus::PAID_IN_ESCROW,
        ]);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('deleteGuide')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $guide->id]);
        $this->assertDatabaseHas('escrow_transactions', ['booking_id' => $booking->id]);
    }

    public function test_admin_can_ban_and_unban_guide(): void
    {
        $guide = $this->makeGuide(['status' => UserStatus::ACTIVE]);
        $this->makeVerifiedProfile($guide);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('toggleBan');

        $this->assertSame(UserStatus::BANNED, $guide->refresh()->status);

        Livewire::actingAs($this->admin)
            ->test(UserManagement::class)
            ->call('selectUser', $guide->id)
            ->call('toggleBan');

        $this->assertSame(UserStatus::ACTIVE, $guide->refresh()->status);
    }
}

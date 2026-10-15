<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\EscrowStatus;
use App\Enums\PaymentMethod;
use App\Enums\TariffMode;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Booking;
use App\Models\ChatMessage;
use App\Models\EscrowTransaction;
use App\Models\Favorite;
use App\Models\GuideProfile;
use App\Models\GuideWallet;
use App\Models\Review;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds one account per testable scenario so every feature (UAT-A/P/W)
 * can be exercised by logging in as the matching user.
 *
 * All accounts use the password "password".
 */
class FeatureTestSeeder extends Seeder
{
    private const PASSWORD = 'password';

    /** Document column => storage folder (must match GuideRegister / GuidePhotoController). */
    private const DOCUMENT_DIRS = [
        'ktp_photo' => 'ktp_photos',
        'headshot' => 'headshots',
        'ktpp_file' => 'ktpp_files',
        'skck_file' => 'skck_files',
        'surat_sehat_file' => 'surat_sehat_files',
    ];

    private string $hashedPassword;

    /** @var array<string, string> email => scenario, printed as a cheat sheet */
    private array $accounts = [];

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('FeatureTestSeeder truncates all tables and must not run in production.');

            return;
        }

        $this->truncateTables();
        $this->hashedPassword = Hash::make(self::PASSWORD);

        $admins = $this->seedAdmins();
        $customers = $this->seedCustomers();
        $guides = $this->seedGuides();
        $packages = $this->seedPackages($guides);

        $this->seedTripHistory($customers, $guides, $packages);
        $this->seedActiveBookings($customers, $guides, $packages);
        $this->seedWithdrawals($guides);
        $this->seedWallets($guides);
        $this->seedChats($customers, $guides);
        $this->seedFavorites($customers, $guides);

        $this->printCheatSheet();
    }

    // ---------------------------------------------------------------------
    // Setup
    // ---------------------------------------------------------------------

    private function truncateTables(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            ChatMessage::class, Favorite::class, Review::class, Withdrawal::class,
            GuideWallet::class, EscrowTransaction::class, Booking::class,
            TourPackage::class, GuideProfile::class, User::class,
        ] as $model) {
            $model::truncate();
        }

        Schema::enableForeignKeyConstraints();
    }

    // ---------------------------------------------------------------------
    // Users
    // ---------------------------------------------------------------------

    /** @return array<string, User> */
    private function seedAdmins(): array
    {
        return [
            'admin1' => $this->user('Super Admin One', 'admin1@gmail.com', UserRole::ADMIN, UserStatus::ACTIVE, null, '081111111111',
                scenario: 'Admin — verification queue, user management, finance & disputes'),
            'admin2' => $this->user('Super Admin Two', 'admin2@gmail.com', UserRole::ADMIN, UserStatus::ACTIVE, null, '082222222222',
                scenario: 'Second admin account'),
        ];
    }

    /** @return array<string, User> */
    private function seedCustomers(): array
    {
        $rows = [
            // key => [name, email, birth_date, preferences, scenario]
            'john' => ['John Doe', 'customer1@gmail.com', '1999-04-10', ['cafe_hopper', 'photography_enthusiast'],
                'Aries · completed trip + review, 2 favorites, unanswered pre-booking chat to Ketut'],
            'jane' => ['Jane Smith', 'customer2@gmail.com', '2002-09-01', ['introvert', 'wellness_seeker'],
                'Virgo · booking waiting guide confirmation (Made) + pre-booking chat'],
            'alice' => ['Alice Johnson', 'customer3@gmail.com', '1997-01-05', ['adventurer', 'foodie'],
                'Capricorn · tour ONGOING with Nyoman (live tracker) + booking chat'],
            'bob' => ['Bob Brown', 'customer4@gmail.com', '1995-06-25', ['night_owl', 'culture_lover'],
                'Cancer · guide HEADING TO LOCATION (Ketut)'],
            'charlie' => ['Charlie Green', 'customer5@gmail.com', '2000-02-14', ['foodie', 'wellness_seeker'],
                'Aquarius · CONFIRMED (paid) booking with Wayan + unread message'],
            'dewi' => ['Dewi Lestari', 'customer6@gmail.com', '2003-08-08', ['photography_enthusiast', 'culture_lover'],
                'Leo · WAITING PAYMENT (test Midtrans sandbox) + package request to Komang'],
            'rina' => ['Rina Wulandari', 'customer7@gmail.com', '1998-10-30', ['cafe_hopper', 'night_owl'],
                'Scorpio · completed trip NOT yet reviewed, rejected & cancelled bookings'],
            'putu' => ['Putu Eka Saputra', 'customer8@gmail.com', '1996-05-05', ['adventurer'],
                'Taurus · two DISPUTED bookings for admin to resolve'],
            'sarah' => ['Sarah Miller', 'customer9@gmail.com', null, [],
                'No birth date / preferences / bookings — empty states & Cosmic Match fallback'],
        ];

        $customers = [];
        $phone = 1;

        foreach ($rows as $key => [$name, $email, $birthDate, $preferences, $scenario]) {
            $customers[$key] = $this->user($name, $email, UserRole::CUSTOMER, UserStatus::ACTIVE, $birthDate,
                '0820000000'.str_pad((string) $phone++, 2, '0', STR_PAD_LEFT), $preferences, $scenario);
        }

        return $customers;
    }

    /** @return array<string, User> */
    private function seedGuides(): array
    {
        $guides = [];

        $guides['wayan'] = $this->guide('Wayan Sudarta', 'wayan@gmail.com', '1993-07-28', [
            'ktp_number' => '5171010101930001',
            'ktpp_number' => 'HPI-BALI-11111',
            'vehicle_details' => 'Avanza White DK 1234 AA',
            'bio' => 'I specialize in cultural heritage tours in Ubud, temple history, and authentic Balinese culinary trips.',
            'communication_style' => 'edukatif',
            'specializations' => ['culture_history', 'cafe_hopping'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 600000,
        ], scenario: 'Leo · daily rate, wallet balance, pending/success/failed payouts, disputed booking, active escrow (delete blocked)');

        $guides['made'] = $this->guide('Made Widiada', 'made@gmail.com', '1996-12-05', [
            'ktp_number' => '5171010512960002',
            'ktpp_number' => 'HPI-BALI-22222',
            'vehicle_details' => 'Scoopy Black DK 5678 BB',
            'bio' => 'Adventurous guide for Kintamani volcanic trails, Mt Batur sunrise climbs, and cycling tours.',
            'communication_style' => 'ekspresif',
            'specializations' => ['nature', 'photography'],
            'languages' => ['id', 'en', 'jp'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 500000,
        ], scenario: 'Sagittarius · NEW ORDER to accept (Jane), unread pre-booking chat, pending payout');

        $guides['nyoman'] = $this->guide('Nyoman Sudiarta', 'nyoman@gmail.com', '1994-03-15', [
            'ktp_number' => '5171011503940003',
            'ktpp_number' => 'HPI-BALI-33333',
            'bio' => 'Fluent in French. I guide around Bedugul water temples, Northern waterfalls, and Munduk lakes.',
            'communication_style' => 'santai',
            'specializations' => ['nature', 'healing'],
            'languages' => ['id', 'en', 'fr'],
            'tariff_mode' => TariffMode::HOURLY,
            'base_rate' => 75000,
        ], scenario: 'Pisces · hourly rate, ONGOING tour → press "End Tour" to test auto 10/90 split');

        $guides['ketut'] = $this->guide('Ketut Astawa', 'ketut@gmail.com', null, [
            'ktp_number' => '5171010210920004',
            'ktpp_number' => 'HPI-BALI-44444',
            'vehicle_details' => 'Innova Reborn Black DK 9999 CC',
            'bio' => 'Professional custom driver and tour planner. Ready to curate customized routes anywhere in Bali.',
            'communication_style' => 'profesional',
            'specializations' => ['cafe_hopping', 'nightlife'],
            'languages' => ['id', 'en', 'de'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 800000,
        ], scenario: 'No birth date (sorts last in Cosmic Match) · HEADING TO LOCATION → Start Tour');

        $guides['komang'] = $this->guide('Komang Ayu Lestari', 'komang@gmail.com', '1999-05-20', [
            'ktp_number' => '5171012005990005',
            'ktpp_number' => 'HPI-BALI-55556',
            'bio' => 'Yoga teacher and wellness guide. Slow mornings, healing retreats and sacred water purification rituals.',
            'communication_style' => 'santai',
            'specializations' => ['healing', 'culture_history'],
            'languages' => ['id', 'en', 'jp'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 650000,
        ], scenario: 'Taurus · package-focused guide, order to REJECT (Dewi), inactive package');

        $guides['kadek'] = $this->guide('Kadek Darma Putra', 'kadek@gmail.com', '2001-01-25', [
            'ktp_number' => '5171012501010006',
            'ktpp_number' => 'HPI-BALI-66666',
            'ktpp_expired_at' => now()->addDays(7),
            'bio' => 'Street & portrait photographer. I know every hidden cafe and golden-hour spot in Canggu.',
            'communication_style' => 'ekspresif',
            'specializations' => ['photography', 'cafe_hopping', 'nightlife'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::HOURLY,
            'base_rate' => 100000,
            'strikes' => 1,
        ], scenario: 'Aquarius · no packages/reviews/earnings, 1 strike, KTPP expires in 7 days, safe to DELETE');

        $guides['gede'] = $this->guide('Gede Budiasa', 'gede@gmail.com', '1997-08-30', [
            'ktp_number' => '5171013008970007',
            'ktpp_number' => 'HPI-BALI-77777',
            'ktpp_expired_at' => now()->subMonths(2),
            'skck_expired_at' => now()->subMonths(2),
            'bio' => 'Passionate local surfing instructor and beach tour specialist in Uluwatu area.',
            'communication_style' => 'santai',
            'specializations' => ['nature', 'nightlife'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 450000,
        ], scenario: 'Virgo · KTPP & SKCK EXPIRED → run `php artisan guides:check-expired-licenses`');

        $guides['ayu'] = $this->guide('Putu Ayu Pratiwi', 'ayu@gmail.com', '2000-06-12', [
            'ktp_number' => '5171011206000008',
            'ktpp_number' => 'HPI-BALI-88888',
            'bio' => 'Fresh HPI graduate, passionate about Balinese dance and temple ceremonies.',
            'communication_style' => 'edukatif',
            'specializations' => ['culture_history'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 400000,
            'is_verified' => false,
        ], UserStatus::PENDING_VERIFICATION,
            scenario: 'Gemini · PENDING VERIFICATION, complete documents → admin APPROVE');

        $guides['agus'] = $this->guide('Agus Wirawan', 'agus@gmail.com', '1998-11-11', [
            'ktp_number' => '5171011111980009',
            'ktpp_number' => 'HPI-BALI-99999',
            'skck_expired_at' => now()->addDays(3),
            'bio' => 'Night market and street-food explorer around Denpasar.',
            'communication_style' => 'santai',
            'specializations' => ['nightlife', 'cafe_hopping'],
            'languages' => ['id'],
            'tariff_mode' => TariffMode::HOURLY,
            'base_rate' => 60000,
            'is_verified' => false,
        ], UserStatus::PENDING_VERIFICATION,
            scenario: 'Scorpio · PENDING VERIFICATION, SKCK expires in 3 days → admin REJECT with reason');

        $guides['surya'] = $this->guide('Surya Dharma', 'surya@gmail.com', '1995-09-09', [
            'ktp_number' => '5171010909950010',
            'ktpp_number' => 'HPI-BALI-10101',
            'bio' => 'Beach and snorkeling guide in Amed.',
            'communication_style' => 'profesional',
            'specializations' => ['nature'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 550000,
            'is_verified' => false,
            'rejection_reason' => 'Foto KTPP buram dan nomor lisensi tidak terbaca. Silakan unggah ulang dokumen yang jelas.',
        ], UserStatus::SUSPENDED,
            scenario: 'REJECTED by admin (suspended + rejection reason)');

        $guides['bagus'] = $this->guide('Bagus Mahendra', 'bagus@gmail.com', '1993-12-25', [
            'ktp_number' => '5171012512930011',
            'ktpp_number' => 'HPI-BALI-12121',
            'bio' => 'Island hopping and fast-boat trips to the Nusa islands.',
            'communication_style' => 'ekspresif',
            'specializations' => ['nature', 'nightlife'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 700000,
            'strikes' => 3,
        ], UserStatus::BANNED,
            scenario: 'BANNED with 3 strikes → admin UNBAN; hidden from search');

        return $guides;
    }

    /**
     * @param  array<string, User>  $guides
     * @return array<string, TourPackage>
     */
    private function seedPackages(array $guides): array
    {
        $rows = [
            'ubud' => ['wayan', 'Ubud Cultural Immersion Day Tour', 750000,
                'Visit Sacred Monkey Forest, walk the ridge, marvel at Tegalalang Rice Terraces, and see a traditional craft center.',
                ['Monkey Forest Ubud', 'Campuhan Ridge Walk', 'Tegalalang Rice Terraces']],
            'batur' => ['made', 'Mount Batur Volcano Sunrise Trekking', 950000,
                'Hike to Batur peak for a breathtaking sunrise, soak in natural hot springs, and taste civet coffee.',
                ['Mount Batur Summit', 'Toya Devasya Hot Springs', 'Kopi Luwak Farm']],
            'penida' => ['made', 'Nusa Penida West Coast Day Trip', 1200000,
                'Fast boat from Sanur to Penida: Kelingking, Broken Beach, and snorkeling at Crystal Bay.',
                ['Kelingking Beach', 'Broken Beach', 'Crystal Bay Snorkeling']],
            'munduk' => ['nyoman', 'Munduk Waterfalls & Twin Lakes', 650000,
                'Three hidden waterfalls, Tamblingan & Buyan lake viewpoints, and a coffee plantation lunch.',
                ['Munduk Waterfall', 'Twin Lakes Viewpoint', 'Ulun Danu Beratan']],
            'uluwatu' => ['ketut', 'Southern Bali Uluwatu Kecak Sunset', 650000,
                'Uluwatu cliff temple, the traditional fire Kecak dance at sunset, and seafood dinner at Jimbaran bay.',
                ['Uluwatu Temple', 'Kecak Fire Performance', 'Jimbaran Seafood Dinner']],
            'healing' => ['komang', 'Sacred Water Purification & Yoga Retreat', 850000,
                'Melukat ritual at Tirta Empul, guided sunrise yoga, and a Balinese herbal spa session.',
                ['Tirta Empul Temple', 'Sunrise Yoga Shala', 'Ubud Herbal Spa']],
            'sidemen' => ['komang', 'Sidemen Valley Slow Living', 700000,
                'Rice terrace trekking, songket weaving workshop, and a healthy lunch overlooking Mount Agung.',
                ['Sidemen Rice Terrace', 'Songket Weaving House', 'Agung View Warung']],
            'retired' => ['komang', '[Inactive] Rainy Season Temple Hopping', 500000,
                'Archived package — should NOT appear on the public profile or booking form.',
                ['Pura Gunung Kawi', 'Goa Gajah'], false],
        ];

        $packages = [];

        foreach ($rows as $key => $row) {
            [$guideKey, $title, $price, $description, $destinations] = $row;

            $packages[$key] = TourPackage::create([
                'guide_id' => $guides[$guideKey]->id,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'destinations' => $destinations,
                'is_active' => $row[5] ?? true,
            ]);
        }

        return $packages;
    }

    // ---------------------------------------------------------------------
    // Bookings
    // ---------------------------------------------------------------------

    /**
     * Completed trips with released escrow and (mostly) reviews, so guides
     * have earnings and different average ratings on search/profile pages.
     */
    private function seedTripHistory(array $c, array $g, array $p): void
    {
        $history = [
            // [customer, guide, package|null, days ago, price, rating|null, comment]
            ['john', 'wayan', 'ubud', 5, 750000, 5, 'Wayan was absolutely fantastic! Knowledgeable, patient, and the car was spotless.'],
            ['alice', 'wayan', null, 30, 600000, 5, 'Best temple tour ever, he explained every ceremony in detail.'],
            ['bob', 'wayan', null, 45, 600000, 4, 'Great stories about Balinese history. Slightly late pickup.'],
            ['charlie', 'made', 'batur', 20, 950000, 5, 'Sunrise at Batur was magical and Made took amazing photos of us!'],
            ['putu', 'made', 'penida', 60, 1200000, 4, 'Fun and energetic, the boat was a bit crowded though.'],
            ['rina', 'made', null, 3, 500000, null, null], // completed but not reviewed yet
            ['jane', 'nyoman', 'munduk', 25, 650000, 4, 'Very calm and relaxing trip, perfect for an introvert like me.'],
            ['dewi', 'nyoman', null, 40, 300000, 3, 'Nice places but the itinerary felt rushed.'],
            ['john', 'ketut', 'uluwatu', 15, 650000, 5, 'Super professional, knew all the best cafes in Canggu too.'],
            ['alice', 'komang', 'healing', 12, 850000, 5, 'The melukat ritual was a life-changing experience.'],
            ['bob', 'komang', 'sidemen', 50, 700000, 4, 'Peaceful valley and lovely weaving workshop.'],
            ['charlie', 'bagus', null, 90, 700000, 2, 'Guide was rude and skipped two destinations.'],
        ];

        foreach ($history as [$customer, $guide, $package, $daysAgo, $price, $rating, $comment]) {
            $date = now()->subDays($daysAgo);

            $booking = $this->booking($c[$customer], $g[$guide], $package ? $p[$package] : null, $date, $price,
                BookingStatus::COMPLETED, EscrowStatus::RELEASED_TO_GUIDE);

            if ($rating !== null) {
                $review = Review::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $c[$customer]->id,
                    'guide_id' => $g[$guide]->id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
                $this->stamp($review, $date->copy()->setTime(20, 0));
            }
        }
    }

    /** One booking per remaining status of the booking state machine. */
    private function seedActiveBookings(array $c, array $g, array $p): void
    {
        // pending_confirmation → Made can ACCEPT
        $this->booking($c['jane'], $g['made'], null, now()->addDays(3), 500000,
            BookingStatus::PENDING_CONFIRMATION, EscrowStatus::WAITING_PAYMENT,
            ['custom_destinations' => ['Bratan Water Temple', 'Gitgit Waterfall'], 'pickup_location' => 'W Seminyak Hotel']);

        // pending_confirmation → Komang can REJECT
        $this->booking($c['dewi'], $g['komang'], $p['healing'], now()->addDays(10), 850000,
            BookingStatus::PENDING_CONFIRMATION, EscrowStatus::WAITING_PAYMENT,
            ['pickup_location' => 'Alila Ubud']);

        // waiting_payment → Dewi pays through Midtrans sandbox
        $this->booking($c['dewi'], $g['wayan'], $p['ubud'], now()->addDays(5), 750000,
            BookingStatus::WAITING_PAYMENT, EscrowStatus::WAITING_PAYMENT,
            ['pickup_location' => 'Kuta Beach Hotel']);

        // confirmed → Wayan presses "Heading to location"
        $this->booking($c['charlie'], $g['wayan'], null, now()->addDays(2), 600000,
            BookingStatus::CONFIRMED, EscrowStatus::PAID_IN_ESCROW,
            ['custom_destinations' => ['Pandawa Beach', 'Melasti Cliff'], 'pickup_location' => 'Four Seasons Jimbaran']);

        // heading_to_location → Ketut presses "Start Tour"
        $this->booking($c['bob'], $g['ketut'], $p['uluwatu'], now(), 650000,
            BookingStatus::HEADING_TO_LOCATION, EscrowStatus::PAID_IN_ESCROW,
            ['pickup_location' => 'Maya Ubud Resort', 'pickup_time' => now()->addHour()->format('H:00')]);

        // ongoing → Nyoman presses "End Tour" (escrow release + wallet credit)
        $this->booking($c['alice'], $g['nyoman'], null, now(), 300000,
            BookingStatus::ONGOING, EscrowStatus::PAID_IN_ESCROW,
            ['custom_destinations' => ['Kuta Art Market', 'Double Six Beach'], 'pickup_location' => 'Grand Hyatt Nusa Dua', 'pickup_time' => '08:00']);

        // rejected by guide
        $this->booking($c['rina'], $g['ketut'], null, now()->addDays(1), 800000,
            BookingStatus::REJECTED, EscrowStatus::WAITING_PAYMENT,
            ['custom_destinations' => ['La Brisa Canggu', 'Old Mans'], 'pickup_location' => 'Canggu Station']);

        // cancelled before payment
        $this->booking($c['rina'], $g['nyoman'], null, now()->subDays(8), 225000,
            BookingStatus::CANCELLED, EscrowStatus::WAITING_PAYMENT,
            ['custom_destinations' => ['Jatiluwih Rice Terrace'], 'pickup_location' => 'Tabanan Town']);

        // disputed → admin "Release to guide"
        $this->booking($c['putu'], $g['made'], $p['batur'], now()->subDays(6), 950000,
            BookingStatus::DISPUTED, EscrowStatus::PAID_IN_ESCROW,
            ['pickup_location' => 'Kintamani Lodge']);

        // disputed → admin "Refund customer"
        $this->booking($c['putu'], $g['wayan'], null, now()->subDays(4), 600000,
            BookingStatus::DISPUTED, EscrowStatus::PAID_IN_ESCROW,
            ['custom_destinations' => ['Tanah Lot', 'Taman Ayun'], 'pickup_location' => 'Seminyak Square']);
    }

    /**
     * Create a booking with its escrow row, mirroring what BookingForm,
     * the Midtrans webhook and EscrowReleaseService would have written.
     */
    private function booking(
        User $customer,
        User $guide,
        ?TourPackage $package,
        CarbonInterface $scheduleDate,
        float $price,
        BookingStatus $status,
        EscrowStatus $escrowStatus,
        array $overrides = [],
    ): Booking {
        $booking = Booking::create(array_merge([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'tour_package_id' => $package?->id,
            'pickup_location' => 'Hotel lobby, '.fake()->randomElement(['Kuta', 'Seminyak', 'Ubud', 'Sanur', 'Nusa Dua']),
            'dropoff_location' => null,
            'custom_destinations' => $package?->destinations ?? ['Tanah Lot Temple', 'Jatiluwih Rice Terrace'],
            'schedule_date' => $scheduleDate->toDateString(),
            'pickup_time' => '09:00',
            'total_price' => $price,
            'status' => $status,
        ], $overrides));

        $createdAt = $scheduleDate->copy()->subDays(4)->min(now()->subHours(2));
        $this->stamp($booking, $createdAt);

        $paid = $escrowStatus !== EscrowStatus::WAITING_PAYMENT;

        $escrow = EscrowTransaction::create([
            'booking_id' => $booking->id,
            'transaction_reference' => $paid
                ? 'BOOK-'.$booking->id.'-'.$createdAt->copy()->addDay()->timestamp
                : 'TXN-'.str_pad((string) $booking->id, 8, '0', STR_PAD_LEFT).'-SEED',
            'payment_method' => $paid
                ? fake()->randomElement(PaymentMethod::cases())
                : PaymentMethod::QRIS,
            'gross_amount' => $price,
            'platform_commission' => $price * 0.10,
            'guide_net_amount' => $price * 0.90,
            'status' => $escrowStatus,
        ]);
        $this->stamp($escrow, $createdAt);

        return $booking;
    }

    // ---------------------------------------------------------------------
    // Money
    // ---------------------------------------------------------------------

    private function seedWithdrawals(array $g): void
    {
        $rows = [
            // [guide, amount, bank, account, status, days ago]
            ['wayan', 500000, 'BCA', '1234567890', WithdrawalStatus::SUCCESS, 14],
            ['wayan', 200000, 'BCA', '1234567890', WithdrawalStatus::FAILED, 7],
            ['wayan', 300000, 'BCA', '1234567890', WithdrawalStatus::PENDING, 1],
            ['made', 150000, 'Mandiri', '1440012345678', WithdrawalStatus::PENDING, 0],
            ['nyoman', 100000, 'BRI', '002301000123456', WithdrawalStatus::SUCCESS, 20],
            ['komang', 400000, 'BNI', '0987654321', WithdrawalStatus::SUCCESS, 10],
        ];

        foreach ($rows as [$guide, $amount, $bank, $account, $status, $daysAgo]) {
            $withdrawal = Withdrawal::create([
                'guide_id' => $g[$guide]->id,
                'amount' => $amount,
                'bank_name' => $bank,
                'bank_account_number' => $account,
                'bank_account_name' => $g[$guide]->name,
                'status' => $status,
            ]);
            $this->stamp($withdrawal, now()->subDays($daysAgo)->subHours(3));
        }
    }

    /**
     * Wallet balance = released net earnings − withdrawals that were not
     * refunded (pending and success are already deducted; failed is returned).
     */
    private function seedWallets(array $g): void
    {
        foreach ($g as $guide) {
            if ($guide->guideProfile?->is_verified !== true) {
                continue;
            }

            $earned = EscrowTransaction::where('status', EscrowStatus::RELEASED_TO_GUIDE)
                ->whereHas('booking', fn ($q) => $q->where('guide_id', $guide->id))
                ->sum('guide_net_amount');

            $withdrawn = Withdrawal::where('guide_id', $guide->id)
                ->where('status', '!=', WithdrawalStatus::FAILED)
                ->sum('amount');

            GuideWallet::create([
                'guide_id' => $guide->id,
                'current_balance' => max(0, $earned - $withdrawn),
            ]);
        }
    }

    // ---------------------------------------------------------------------
    // Chat & favorites
    // ---------------------------------------------------------------------

    private function seedChats(array $c, array $g): void
    {
        $bookingOf = fn (User $customer, User $guide, BookingStatus $status) => Booking::where([
            'customer_id' => $customer->id,
            'guide_id' => $guide->id,
            'status' => $status,
        ])->value('id');

        // Pre-booking: Jane ↔ Made, last message unread by Made.
        $this->conversation(null, [
            [$c['jane'], $g['made'], 'Hi Made! Is the Bedugul + Gitgit route doable in one day?', 180],
            [$g['made'], $c['jane'], 'Hi Jane, yes for sure. We leave at 8 so we beat the crowds at Bratan.', 170],
            [$c['jane'], $g['made'], 'Perfect. I prefer a quiet pace, not too many people. I just sent the booking request!', 30, false],
        ]);

        // Pre-booking: John → Ketut, unanswered.
        $this->conversation(null, [
            [$c['john'], $g['ketut'], 'Hey Ketut, can you recommend 3 aesthetic cafes in Canggu for a photo trip?', 45, false],
            [$c['john'], $g['ketut'], 'Also, is half-day possible with your daily rate?', 44, false],
        ]);

        // Pre-booking: Sarah → Kadek, read & answered.
        $this->conversation(null, [
            [$c['sarah'], $g['kadek'], 'Do you do sunset portrait sessions?', 2000],
            [$g['kadek'], $c['sarah'], 'Yes! Echo Beach at golden hour is my favorite. Hourly rate applies.', 1990],
        ]);

        // Booking chat: Alice ↔ Nyoman during the ongoing tour.
        $this->conversation($bookingOf($c['alice'], $g['nyoman'], BookingStatus::ONGOING), [
            [$c['alice'], $g['nyoman'], 'Hello Nyoman, are we still good for the 8 AM pick-up today?', 300],
            [$g['nyoman'], $c['alice'], 'Hello Alice! Yes, I am on the way to Grand Hyatt now.', 290],
            [$c['alice'], $g['nyoman'], 'Great, thank you! See you soon.', 285],
            [$g['nyoman'], $c['alice'], 'We will have lunch near Double Six after the market 🙂', 60, false],
        ]);

        // Booking chat: Wayan → Charlie, unread by the customer.
        $this->conversation($bookingOf($c['charlie'], $g['wayan'], BookingStatus::CONFIRMED), [
            [$c['charlie'], $g['wayan'], 'Thanks for accepting! Payment is done.', 1500],
            [$g['wayan'], $c['charlie'], 'Payment received. Please bring a sarong for the temple visit.', 20, false],
        ]);

        // Booking chat: Putu ↔ Wayan about the disputed trip.
        $this->conversation($bookingOf($c['putu'], $g['wayan'], BookingStatus::DISPUTED), [
            [$c['putu'], $g['wayan'], 'You never showed up at Seminyak Square. I waited for 2 hours.', 4000],
            [$g['wayan'], $c['putu'], 'I am sorry, my car broke down. I tried calling you many times.', 3950],
        ]);
    }

    /** @param array<int, array{0: User, 1: User, 2: string, 3: int, 4?: bool}> $messages [sender, receiver, text, minutes ago, read?] */
    private function conversation(?int $bookingId, array $messages): void
    {
        foreach ($messages as $message) {
            [$sender, $receiver, $text, $minutesAgo] = $message;

            $chat = ChatMessage::create([
                'booking_id' => $bookingId,
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $text,
                'is_read' => $message[4] ?? true,
            ]);
            $this->stamp($chat, now()->subMinutes($minutesAgo));
        }
    }

    private function seedFavorites(array $c, array $g): void
    {
        $rows = [
            'john' => ['wayan', 'made'],
            'jane' => ['nyoman', 'komang'],
            'dewi' => ['wayan', 'kadek'],
            'alice' => ['komang'],
        ];

        foreach ($rows as $customer => $guideKeys) {
            foreach ($guideKeys as $guide) {
                Favorite::create(['customer_id' => $c[$customer]->id, 'guide_id' => $g[$guide]->id]);
            }
        }
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function user(
        string $name,
        string $email,
        UserRole $role,
        UserStatus $status,
        ?string $birthDate,
        string $phone,
        array $preferences = [],
        string $scenario = '',
    ): User {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $this->hashedPassword,
            'phone_number' => $phone,
            'birth_date' => $birthDate,
            'role' => $role,
            'status' => $status,
            'traveler_preferences' => $role === UserRole::CUSTOMER ? $preferences : null,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();
        $this->accounts[$email] = [$role->value, $status->value, $scenario];

        return $user;
    }

    private function guide(
        string $name,
        string $email,
        ?string $birthDate,
        array $profile,
        UserStatus $status = UserStatus::ACTIVE,
        string $scenario = '',
    ): User {
        static $phone = 1;

        $user = $this->user($name, $email, UserRole::GUIDE, $status, $birthDate,
            '0812345678'.str_pad((string) $phone++, 2, '0', STR_PAD_LEFT), scenario: $scenario);

        $slug = strtok($email, '@');

        GuideProfile::create(array_merge([
            'user_id' => $user->id,
            'ktpp_expired_at' => now()->addYears(2),
            'skck_expired_at' => now()->addYear(),
            'vehicle_details' => null,
            'is_verified' => true,
            'signed_sop_at' => now()->subMonths(3),
            'strikes' => 0,
        ], $this->documents($slug, $name), $profile));

        return $user->load('guideProfile');
    }

    /**
     * Write placeholder JPEGs to storage/app/private/guide_documents so the
     * admin document preview and public headshot routes return real files.
     *
     * @return array<string, string|null>
     */
    private function documents(string $slug, string $name): array
    {
        $labels = [
            'ktp_photo' => 'KARTU TANDA PENDUDUK',
            'headshot' => null,
            'ktpp_file' => 'KARTU TANDA PENGENAL PRAMUWISATA (HPI)',
            'skck_file' => 'SURAT KETERANGAN CATATAN KEPOLISIAN',
            'surat_sehat_file' => 'SURAT KETERANGAN SEHAT',
        ];

        $paths = [];

        foreach (self::DOCUMENT_DIRS as $column => $dir) {
            $path = "guide_documents/{$dir}/seed-{$slug}.jpg";
            $image = $labels[$column] === null
                ? $this->headshotImage($name)
                : $this->documentImage($labels[$column], $name);

            if ($image === null) {
                $paths[$column] = null;

                continue;
            }

            Storage::disk('local')->put($path, $image);
            $paths[$column] = $path;
        }

        return $paths;
    }

    private function documentImage(string $title, string $name): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $img = imagecreatetruecolor(640, 400);
        imagefill($img, 0, 0, imagecolorallocate($img, 236, 242, 250));
        imagefilledrectangle($img, 0, 0, 640, 60, imagecolorallocate($img, 31, 111, 178));

        $white = imagecolorallocate($img, 255, 255, 255);
        $ink = imagecolorallocate($img, 34, 34, 34);
        $red = imagecolorallocate($img, 255, 56, 92);

        imagestring($img, 5, 20, 22, $title, $white);
        imagefilledrectangle($img, 20, 90, 170, 280, imagecolorallocate($img, 200, 210, 222));
        imagestring($img, 5, 200, 100, 'Nama : '.$name, $ink);
        imagestring($img, 5, 200, 130, 'No.  : '.strtoupper(substr(md5($name.$title), 0, 12)), $ink);
        imagestring($img, 5, 200, 160, 'Prov : Bali', $ink);
        imagestring($img, 5, 20, 340, 'SAMPLE DOCUMENT - SEEDED FOR TESTING ONLY', $red);

        return $this->jpeg($img);
    }

    private function headshotImage(string $name): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        // Pastel background derived from the name, with a lighter head-and-shoulders silhouette.
        [$r, $g, $b] = sscanf(substr(md5($name), 0, 6), '%02x%02x%02x');
        [$r, $g, $b] = array_map(fn ($c) => (int) (150 + $c * 0.35), [$r, $g, $b]);

        // Draw at 2x and downsample so the curved edges come out smooth.
        $size = 800;
        $img = imagecreatetruecolor($size, $size);
        imagefill($img, 0, 0, imagecolorallocate($img, $r, $g, $b));

        $figure = imagecolorallocate($img, min(255, $r + 45), min(255, $g + 45), min(255, $b + 45));
        imagefilledellipse($img, 400, 330, 300, 300, $figure);
        imagefilledellipse($img, 400, 820, 620, 520, $figure);

        $out = imagecreatetruecolor(400, 400);
        imagecopyresampled($out, $img, 0, 0, 0, 0, 400, 400, $size, $size);
        imagedestroy($img);

        return $this->jpeg($out);
    }

    private function jpeg(\GdImage $img): string
    {
        ob_start();
        imagejpeg($img, null, 85);
        imagedestroy($img);

        return (string) ob_get_clean();
    }

    private function stamp(Model $model, CarbonInterface $at): void
    {
        $model->forceFill(['created_at' => $at, 'updated_at' => $at])->saveQuietly();
    }

    private function printCheatSheet(): void
    {
        if (! $this->command) {
            return;
        }

        $this->command->newLine();
        $this->command->info('Feature test accounts — password for all: '.self::PASSWORD);
        $this->command->table(
            ['Email', 'Role', 'Status', 'Test scenario'],
            collect($this->accounts)->map(fn ($row, $email) => [$email, ...$row])->values()->all(),
        );
    }
}

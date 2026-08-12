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
use App\Models\GuideProfile;
use App\Models\GuideWallet;
use App\Models\Review;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Disable Foreign Key Checks & Truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        ChatMessage::truncate();
        Review::truncate();
        Withdrawal::truncate();
        GuideWallet::truncate();
        EscrowTransaction::truncate();
        Booking::truncate();
        TourPackage::truncate();
        GuideProfile::truncate();
        User::truncate();

        // 2. Hash password once for performance
        $hashedPassword = Hash::make('password');

        // 3. Create Super Admins
        $admin1 = User::create([
            'name' => 'Super Admin One',
            'email' => 'admin1@gmail.com',
            'password' => $hashedPassword,
            'phone_number' => '081111111111',
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $admin2 = User::create([
            'name' => 'Super Admin Two',
            'email' => 'admin2@gmail.com',
            'password' => $hashedPassword,
            'phone_number' => '082222222222',
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        // 4. Create Customers
        $customers = [];
        $customerNames = ['John Doe', 'Jane Smith', 'Alice Johnson', 'Bob Brown', 'Charlie Green'];
        foreach ($customerNames as $index => $name) {
            $customers[] = User::create([
                'name' => $name,
                'email' => 'customer' . ($index + 1) . '@gmail.com',
                'password' => $hashedPassword,
                'phone_number' => '08200000000' . ($index + 1),
                'role' => UserRole::CUSTOMER,
                'status' => UserStatus::ACTIVE,
            ]);
        }

        // 5. Create Tour Guides
        $guides = [];
        
        // Guide 1: Wayan
        $wayanUser = User::create([
            'name' => 'Wayan Sudarta',
            'email' => 'wayan@gmail.com',
            'password' => $hashedPassword,
            'phone_number' => '081234567891',
            'role' => UserRole::GUIDE,
            'status' => UserStatus::ACTIVE,
        ]);
        $guides[] = $wayanUser;
        GuideProfile::create([
            'user_id' => $wayanUser->id,
            'ktp_number' => '1234567890123451',
            'ktp_photo' => 'guide_documents/ktp_photos/wayan_ktp.jpg',
            'ktpp_number' => 'HPI-BALI-11111',
            'ktpp_file' => 'guide_documents/ktpp_files/wayan_ktpp.jpg',
            'ktpp_expired_at' => now()->addYear(),
            'skck_file' => 'guide_documents/skck_files/wayan_skck.jpg',
            'skck_expired_at' => now()->addYear(),
            'surat_sehat_file' => 'guide_documents/surat_sehat_files/wayan_health.jpg',
            'vehicle_details' => 'Avanza White DK 1234 AA',
            'bio' => 'I specialize in cultural heritage tours in Ubud, temple history, and authentic Balinese culinary trips.',
            'communication_style' => 'edukatif',
            'specializations' => ['culture_history', 'cafe_hopping'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 600000.00,
            'is_verified' => true,
            'signed_sop_at' => now(),
        ]);

        // Guide 2: Made
        $madeUser = User::create([
            'name' => 'Made Widiada',
            'email' => 'made@gmail.com',
            'password' => $hashedPassword,
            'phone_number' => '081234567892',
            'role' => UserRole::GUIDE,
            'status' => UserStatus::ACTIVE,
        ]);
        $guides[] = $madeUser;
        GuideProfile::create([
            'user_id' => $madeUser->id,
            'ktp_number' => '1234567890123452',
            'ktp_photo' => 'guide_documents/ktp_photos/made_ktp.jpg',
            'ktpp_number' => 'HPI-BALI-22222',
            'ktpp_file' => 'guide_documents/ktpp_files/made_ktpp.jpg',
            'ktpp_expired_at' => now()->addYear(),
            'skck_file' => 'guide_documents/skck_files/made_skck.jpg',
            'skck_expired_at' => now()->addYear(),
            'surat_sehat_file' => 'guide_documents/surat_sehat_files/made_health.jpg',
            'vehicle_details' => 'Scoopy Black DK 5678 BB',
            'bio' => 'I am an adventurous guide showing Kintamani volcanic trails, Mt Batur sunrise climbs, and cycling tours.',
            'communication_style' => 'ekspresif',
            'specializations' => ['nature', 'photography'],
            'languages' => ['id', 'en', 'jp'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 500000.00,
            'is_verified' => true,
            'signed_sop_at' => now(),
        ]);

        // Guide 3: Nyoman
        $nyomanUser = User::create([
            'name' => 'Nyoman Sudiarta',
            'email' => 'nyoman@gmail.com',
            'password' => $hashedPassword,
            'phone_number' => '081234567893',
            'role' => UserRole::GUIDE,
            'status' => UserStatus::ACTIVE,
        ]);
        $guides[] = $nyomanUser;
        GuideProfile::create([
            'user_id' => $nyomanUser->id,
            'ktp_number' => '1234567890123453',
            'ktp_photo' => 'guide_documents/ktp_photos/nyoman_ktp.jpg',
            'ktpp_number' => 'HPI-BALI-33333',
            'ktpp_file' => 'guide_documents/ktpp_files/nyoman_ktpp.jpg',
            'ktpp_expired_at' => now()->addYear(),
            'skck_file' => 'guide_documents/skck_files/nyoman_skck.jpg',
            'skck_expired_at' => now()->addYear(),
            'surat_sehat_file' => 'guide_documents/surat_sehat_files/nyoman_health.jpg',
            'vehicle_details' => null,
            'bio' => 'Fluent in French. I guide around Bedugul water temples, Northern waterfalls, and Munduk lakes.',
            'communication_style' => 'santai',
            'specializations' => ['nature', 'photography'],
            'languages' => ['id', 'en', 'fr'],
            'tariff_mode' => TariffMode::HOURLY,
            'base_rate' => 75000.00,
            'is_verified' => true,
            'signed_sop_at' => now(),
        ]);

        // Guide 4: Ketut
        $ketutUser = User::create([
            'name' => 'Ketut Astawa',
            'email' => 'ketut@gmail.com',
            'password' => $hashedPassword,
            'phone_number' => '081234567894',
            'role' => UserRole::GUIDE,
            'status' => UserStatus::ACTIVE,
        ]);
        $guides[] = $ketutUser;
        GuideProfile::create([
            'user_id' => $ketutUser->id,
            'ktp_number' => '1234567890123454',
            'ktp_photo' => 'guide_documents/ktp_photos/ketut_ktp.jpg',
            'ktpp_number' => 'HPI-BALI-44444',
            'ktpp_file' => 'guide_documents/ktpp_files/ketut_ktpp.jpg',
            'ktpp_expired_at' => now()->addYear(),
            'skck_file' => 'guide_documents/skck_files/ketut_skck.jpg',
            'skck_expired_at' => now()->addYear(),
            'surat_sehat_file' => 'guide_documents/surat_sehat_files/ketut_health.jpg',
            'vehicle_details' => 'Innova Reborn Black DK 9999 CC',
            'bio' => 'Professional custom driver and tour planner. Ready to curate customized routes anywhere in Bali.',
            'communication_style' => 'profesional',
            'specializations' => ['cafe_hopping', 'nightlife'],
            'languages' => ['id', 'en', 'de'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 800000.00,
            'is_verified' => true,
            'signed_sop_at' => now(),
        ]);

        // Guide 5: Gede (Expired credentials - test suspend system)
        $gedeUser = User::create([
            'name' => 'Gede Budiasa',
            'email' => 'gede@gmail.com',
            'password' => $hashedPassword,
            'phone_number' => '081234567895',
            'role' => UserRole::GUIDE,
            'status' => UserStatus::ACTIVE,
        ]);
        $guides[] = $gedeUser;
        GuideProfile::create([
            'user_id' => $gedeUser->id,
            'ktp_number' => '1234567890123455',
            'ktp_photo' => 'guide_documents/ktp_photos/gede_ktp.jpg',
            'ktpp_number' => 'HPI-BALI-55555',
            'ktpp_file' => 'guide_documents/ktpp_files/gede_ktpp.jpg',
            'ktpp_expired_at' => now()->subMonths(2), // EXPIRED
            'skck_file' => 'guide_documents/skck_files/gede_skck.jpg',
            'skck_expired_at' => now()->subMonths(2), // EXPIRED
            'surat_sehat_file' => 'guide_documents/surat_sehat_files/gede_health.jpg',
            'vehicle_details' => null,
            'bio' => 'Passionate local surfing instructor and beach tour specialist in Uluwatu area.',
            'communication_style' => 'santai',
            'specializations' => ['nature', 'nightlife'],
            'languages' => ['id', 'en'],
            'tariff_mode' => TariffMode::DAILY,
            'base_rate' => 450000.00,
            'is_verified' => true,
            'signed_sop_at' => now(),
        ]);

        // 6. Seed Tour Packages
        $pkg1 = TourPackage::create([
            'guide_id' => $wayanUser->id,
            'title' => 'Ubud Cultural Immersion Day Tour',
            'description' => 'Visit Sacred Monkey Forest, walk the ridge, marvel at Tegalalang Rice Terraces, and see a traditional craft center.',
            'price' => 750000.00,
            'destinations' => ['Monkey Forest Ubud', 'Campuhan Ridge Walk', 'Tegalalang Rice Terraces'],
            'is_active' => true,
        ]);

        $pkg2 = TourPackage::create([
            'guide_id' => $madeUser->id,
            'title' => 'Mount Batur Volcano Sunrise Trekking',
            'description' => 'Wake up early, hike to Batur peak for a breathtaking sunrise, soak in natural hot springs, and taste famous civet coffee.',
            'price' => 950000.00,
            'destinations' => ['Mount Batur Summit', 'Toya Devasya Hot Springs', 'Kopi Luwak Farm'],
            'is_active' => true,
        ]);

        $pkg3 = TourPackage::create([
            'guide_id' => $madeUser->id,
            'title' => 'Nusa Penida West Coast Day Trip',
            'description' => 'Take a fast boat from Sanur to Penida and visit the most famous cliff viewpoints including Kelingking, Broken Beach, and Snorkel.',
            'price' => 1200000.00,
            'destinations' => ['Kelingking Beach', 'Broken Beach', 'Crystal Bay Snorkeling'],
            'is_active' => true,
        ]);

        $pkg4 = TourPackage::create([
            'guide_id' => $ketutUser->id,
            'title' => 'Southern Bali Uluwatu Kecak Sunset Cruise',
            'description' => 'Watch monkeys play in Uluwatu cliff temple, see the traditional fire Kecak dance at sunset, and dine at Jimbaran bay.',
            'price' => 650000.00,
            'destinations' => ['Uluwatu Temple', 'Kecak Fire Performance', 'Jimbaran Seafood dinner'],
            'is_active' => true,
        ]);

        // 7. Seed Guide Wallets
        foreach ($guides as $g) {
            GuideWallet::create([
                'guide_id' => $g->id,
                'current_balance' => $g->id === $wayanUser->id ? 1500000.00 : ($g->id === $madeUser->id ? 400000.00 : 0.00),
            ]);
        }

        // 8. Seed Bookings & Escrows (Diverse Statuses)
        
        // Trip 1: Completed Tour (Wayan)
        $booking1 = Booking::create([
            'customer_id' => $customers[0]->id, // John
            'guide_id' => $wayanUser->id,
            'tour_package_id' => $pkg1->id,
            'pickup_location' => 'Sheraton Kuta Resort',
            'dropoff_location' => 'Sheraton Kuta Resort',
            'custom_destinations' => $pkg1->destinations,
            'schedule_date' => now()->subDays(5),
            'pickup_time' => '08:00',
            'total_price' => 750000.00,
            'status' => BookingStatus::COMPLETED,
        ]);
        EscrowTransaction::create([
            'booking_id' => $booking1->id,
            'transaction_reference' => 'TXN-00000001-COMPLETED',
            'payment_method' => PaymentMethod::QRIS,
            'gross_amount' => 750000.00,
            'platform_commission' => 75000.00,
            'guide_net_amount' => 675000.00,
            'status' => EscrowStatus::RELEASED_TO_GUIDE,
        ]);
        Review::create([
            'booking_id' => $booking1->id,
            'customer_id' => $customers[0]->id,
            'guide_id' => $wayanUser->id,
            'rating' => 5,
            'comment' => 'Our guide Wayan was absolutely fantastic! Very helpful, knowledgeable, and the car was clean.',
        ]);

        // Trip 2: Pending Confirmation (Made)
        $booking2 = Booking::create([
            'customer_id' => $customers[1]->id, // Jane
            'guide_id' => $madeUser->id,
            'tour_package_id' => null,
            'pickup_location' => 'W Seminyak Hotel',
            'dropoff_location' => 'W Seminyak Hotel',
            'custom_destinations' => ['Bratan Water Temple', 'Gitgit Waterfall'],
            'schedule_date' => now()->addDays(3),
            'pickup_time' => '09:00',
            'total_price' => 500000.00, // Daily Rate
            'status' => BookingStatus::PENDING_CONFIRMATION,
        ]);
        EscrowTransaction::create([
            'booking_id' => $booking2->id,
            'transaction_reference' => 'TXN-00000002-PENDING',
            'payment_method' => PaymentMethod::CREDIT_CARD,
            'gross_amount' => 500000.00,
            'platform_commission' => 50000.00,
            'guide_net_amount' => 450000.00,
            'status' => EscrowStatus::WAITING_PAYMENT,
        ]);

        // Trip 3: Ongoing Tour (Nyoman)
        $booking3 = Booking::create([
            'customer_id' => $customers[2]->id, // Alice
            'guide_id' => $nyomanUser->id,
            'tour_package_id' => null,
            'pickup_location' => 'Grand Hyatt Nusa Dua',
            'dropoff_location' => 'Grand Hyatt Nusa Dua',
            'custom_destinations' => ['Kuta Art Market', 'Double Six Beach'],
            'schedule_date' => now(), // TODAY
            'pickup_time' => '13:00',
            'total_price' => 300000.00, // Hourly rate: 4 hrs * 75,000
            'status' => BookingStatus::ONGOING,
        ]);
        EscrowTransaction::create([
            'booking_id' => $booking3->id,
            'transaction_reference' => 'TXN-00000003-ONGOING',
            'payment_method' => PaymentMethod::VIRTUAL_ACCOUNT,
            'gross_amount' => 300000.00,
            'platform_commission' => 30000.00,
            'guide_net_amount' => 270000.00,
            'status' => EscrowStatus::PAID_IN_ESCROW,
        ]);

        // Trip 4: Heading to Location (Ketut)
        $booking4 = Booking::create([
            'customer_id' => $customers[3]->id, // Bob
            'guide_id' => $ketutUser->id,
            'tour_package_id' => $pkg4->id,
            'pickup_location' => 'Maya Ubud Resort',
            'dropoff_location' => 'Maya Ubud Resort',
            'custom_destinations' => $pkg4->destinations,
            'schedule_date' => now()->addDay(),
            'pickup_time' => '14:30',
            'total_price' => 650000.00,
            'status' => BookingStatus::HEADING_TO_LOCATION,
        ]);
        EscrowTransaction::create([
            'booking_id' => $booking4->id,
            'transaction_reference' => 'TXN-00000004-HEADING',
            'payment_method' => PaymentMethod::QRIS,
            'gross_amount' => 650000.00,
            'platform_commission' => 65000.00,
            'guide_net_amount' => 585000.00,
            'status' => EscrowStatus::PAID_IN_ESCROW,
        ]);

        // Trip 5: Confirmed (Wayan)
        $booking5 = Booking::create([
            'customer_id' => $customers[4]->id, // Charlie
            'guide_id' => $wayanUser->id,
            'tour_package_id' => null,
            'pickup_location' => 'Four Seasons Jimbaran',
            'dropoff_location' => 'Four Seasons Jimbaran',
            'custom_destinations' => ['Pandawa Beach', 'Melasti Cliff'],
            'schedule_date' => now()->addDays(2),
            'pickup_time' => '09:00',
            'total_price' => 600000.00,
            'status' => BookingStatus::CONFIRMED,
        ]);
        EscrowTransaction::create([
            'booking_id' => $booking5->id,
            'transaction_reference' => 'TXN-00000005-CONFIRMED',
            'payment_method' => PaymentMethod::QRIS,
            'gross_amount' => 600000.00,
            'platform_commission' => 60000.00,
            'guide_net_amount' => 540000.00,
            'status' => EscrowStatus::PAID_IN_ESCROW,
        ]);

        // 9. Seed Withdrawals
        Withdrawal::create([
            'guide_id' => $wayanUser->id,
            'amount' => 500000.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Wayan Sudarta',
            'status' => WithdrawalStatus::SUCCESS,
        ]);

        Withdrawal::create([
            'guide_id' => $wayanUser->id,
            'amount' => 300000.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Wayan Sudarta',
            'status' => WithdrawalStatus::PENDING,
        ]);

        // 10. Seed Chat Messages
        ChatMessage::create([
            'booking_id' => $booking3->id,
            'sender_id' => $customers[2]->id, // Alice
            'message' => 'Hello Nyoman, just checking if we are still good for our pick up at 1:00 PM today?',
        ]);

        ChatMessage::create([
            'booking_id' => $booking3->id,
            'sender_id' => $nyomanUser->id,
            'message' => 'Hello Alice! Yes, absolutely. I am preparing the vehicle and heading to Grand Hyatt hotel shortly.',
        ]);

        ChatMessage::create([
            'booking_id' => $booking3->id,
            'sender_id' => $customers[2]->id, // Alice
            'message' => 'Great, thank you! See you soon.',
        ]);

        // 11. Re-enable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

PRODUCT REQUIREMENTS DOCUMENT (PRD) / SRS
Platform Matchmaking Pemandu Wisata Lokal Berbasis Web untuk Mendukung Quality Tourism
Penulis: I Gede Tio Mahesa Diputra
Program Studi: S1 - Sistem Informasi, ITB STIKOM Bali
Tahun: 2026
Metodologi Pengembangan: Rapid Application Development (RAD)
Teknologi Utama: Laravel 13, Livewire 4, Tailwind CSS, MySQL
1. Visi & Pengantar Produk
Paradigma pariwisata Bali mengalami pergeseran mendasar dari mass tourism menuju quality tourism. Wisatawan modern (khususnya Generasi Z dan milenial) menuntut pengalaman wisata yang personal, imersif, dan fleksibel (berbasis gaya hidup, fotografi, kuliner/kafe estetik, hingga pelepasan penat), menggantikan paket wisata konvensional yang kaku. Namun, ekosistem kepemanduan saat ini masih bersifat transaksional dan acak, memicu fenomena mismatch (ketidakcocokan minat, gaya komunikasi, dan ritme perjalanan) antara wisatawan dan pemandu.
Platform Matchmaking Pemandu Wisata Lokal hadir sebagai solusi berbasis web yang mempertemukan wisatawan dengan pemandu wisata lokal terverifikasi berdasarkan kesamaan minat, gaya perjalanan, dan karakter (same interest, same vibe, same frequency). Platform ini mengadopsi model mental aplikasi pencocokan modern (seperti Tinder/Bumble) yang dilengkapi fitur pre-booking chat, verifikasi legalitas resmi (KTP, KTPP/HPI, SKCK), sistem pemesanan kustom, escrow payment gateway, serta evaluasi berbasis User Acceptance Testing (UAT) dan System Usability Scale (SUS).
2. Batasan & Arsitektur Peran (User Roles)
Peran (Actor)
Batasan & Deskripsi Usia/Kriteria
Tanggung Jawab Utama
 
Admin (Super Admin)
Pengelola platform resmi ITB STIKOM Bali / Manajemen Platform.
Verifikasi & audit dokumen legalitas guide (KTP, KTPP, SKCK), manajemen data pengguna, moderasi ulasan, pengawasan transaksi escrow, dan persetujuan penarikan dana (withdrawal).
Pemandu Wisata (Guide)
Usia 18–35 tahun, lokal Bali, berlisensi/terverifikasi.
Registrasi multi-tier KYC, pengisian persona/spesialisasi (kafe, alam, nightlife, budaya/sejarah, fotografi), pengelolaan paket & tarif, respon order real-time, pre-booking chat, dan penarikan dana.
Wisatawan (Customer)
Usia 19–35 tahun, wisatawan domestik/mancanegara.
Eksplorasi & pencarian guide berbasis filter matching (spesialisasi, gaya komunikasi, tarif), pre-booking chat, penyusunan kustom itinerary, pembayaran escrow, live trip tracking, dan rating/ulasan.

3. Arsitektur Basis Data (Database Schema - MySQL / DBML)
// Table Users (Multi-role Authentication)
Table users {
  id bigint [pk, increment]
  name varchar
  email varchar [unique]
  password varchar
  role enum('admin', 'guide', 'customer')
  phone_number varchar
  status enum('pending_verification', 'active', 'suspended') [default: 'pending_verification']
  created_at timestamp
  updated_at timestamp
}

// Table Guide Profiles (Persona, Legalitas, & Matching Parameters)
Table guide_profiles {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  nik_ktp varchar [unique]
  ktpp_number varchar [unique] // Lisensi Resmi HPI
  ktpp_expired_at date
  skck_expired_at date
  bio text
  communication_style enum('santai', 'edukatif', 'profesional', 'ekspresif')
  specializations json // Array: ['cafe_hopping', 'photography', 'nightlife', 'nature', 'culture_history', 'healing']
  languages json // Array: ['Indonesian', 'English', 'Japanese']
  tariff_mode enum('hourly', 'daily')
  base_rate decimal(12,2)
  is_verified boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

// Table Tour Packages
Table tour_packages {
  id bigint [pk, increment]
  guide_profile_id bigint [ref: > guide_profiles.id]
  title varchar
  description text
  price decimal(12,2)
  destinations json // List destinasi
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

// Table Bookings (State Machine Process)
Table bookings {
  id bigint [pk, increment]
  customer_id bigint [ref: > users.id]
  guide_id bigint [ref: > users.id]
  tour_package_id bigint [nullable, ref: > tour_packages.id]
  booking_date date
  pickup_location text
  custom_itinerary json
  total_price decimal(12,2)
  status enum('pending_confirmation', 'waiting_payment', 'confirmed', 'heading_to_location', 'ongoing', 'completed', 'cancelled', 'disputed') [default: 'pending_confirmation']
  created_at timestamp
  updated_at timestamp
}

// Table Escrow Transactions
Table escrow_transactions {
  id bigint [pk, increment]
  booking_id bigint [ref: > bookings.id]
  gross_amount decimal(12,2)
  platform_commission decimal(12,2) // 10% Cut
  guide_net_amount decimal(12,2) // 90% Portion
  payment_gateway_ref varchar
  status enum('waiting_payment', 'paid_in_escrow', 'released_to_guide', 'refunded') [default: 'waiting_payment']
  created_at timestamp
  updated_at timestamp
}

// Table Guide Wallets & Withdrawals
Table guide_wallets {
  id bigint [pk, increment]
  guide_id bigint [ref: > users.id]
  current_balance decimal(12,2) [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table withdrawals {
  id bigint [pk, increment]
  guide_wallet_id bigint [ref: > guide_wallets.id]
  amount decimal(12,2)
  bank_name varchar
  bank_account_number varchar
  bank_account_name varchar
  status enum('pending', 'success', 'failed') [default: 'pending']
  created_at timestamp
  updated_at timestamp
}

// Table Pre-Booking & Active In-App Chat
Table chat_messages {
  id bigint [pk, increment]
  booking_id bigint [nullable, ref: > bookings.id]
  sender_id bigint [ref: > users.id]
  receiver_id bigint [ref: > users.id]
  message text
  is_read boolean [default: false]
  created_at timestamp
}

// Table Reviews & Ratings
Table reviews {
  id bigint [pk, increment]
  booking_id bigint [ref: > bookings.id]
  customer_id bigint [ref: > users.id]
  guide_id bigint [ref: > users.id]
  rating int // Scale 1 - 5
  comment text
  created_at timestamp
}


4. Kebutuhan Fungsional Berdasarkan Fase Development
Fase 1: Onboarding & Strict Verification Flow
FR-01-01 (Multi-Role Register): Menyediakan pintu masuk pendaftaran terpisah di /register untuk Traveler (`/register/customer`) dan Tour Guide (`/register/guide`).
FR-01-02 (Guide Tiered KYC): Guide wajib mengisi NIK KTP, Nomor KTPP (HPI), tanggal kedaluwarsa SKCK & KTPP, gaya komunikasi, spesialisasi aktivitas, serta mengunggah foto KTP dan pas foto.
FR-01-03 (Digital SOP Sign-Off): Guide wajib menyetujui lembar komitmen digital terkait etika pariwisata Bali dan aturan adat daerah.
FR-01-04 (Admin Verification Dashboard): Admin memeriksa keaslian dokumen legalitas guide. Mengubah status akun menjadi `active` / `verified` atau menolak pendaftaran.
Fase 2: Matchmaking, Pre-Booking Chat, & Escrow Pipeline
FR-02-01 (Matching Engine & Filter): Wisatawan melakukan penyaringan guide berbasis 3 parameter prioritas survei: (1) Spesialisasi Aktivitas (Kafe, Alam, Nightlife, Fotografi, Budaya), (2) Gaya Komunikasi (Santai, Edukatif, Ekspresif), dan (3) Tarif Jasa.
FR-02-02 (Pre-Booking Chat): Ruang komunikasi langsung antara Wisatawan dan Guide sebelum transaksi disetujui untuk menyelaraskan ekspektasi dan kustomisasi itinerary.
FR-02-03 (Confirmation-First Booking Flow):
Wisatawan mengajukan booking → Status: `pending_confirmation`.
Guide menyetujui pesanan → Status berubah: `waiting_payment`.
Wisatawan melakukan pembayaran via Payment Gateway (QRIS/VA) → Status berubah: `confirmed`, dana masuk ke Escrow (`paid_in_escrow`).
Fase 3: Pelaksanaan Tour & Live State Machine
FR-03-01 (Guide Order Dispatch & Control): Guide secara reaktif mengubah status tour: `heading_to_location` → `ongoing` (Start Tour) → `completed` (End Tour).
FR-03-02 (Customer Live Tracking): Wisatawan memantau posisi/status tour secara real-time melalui progres stepper di dashboard customer.
FR-03-03 (Automated Revenue Split): Ketika tour berstatus `completed`, sistem secara otomatis memotong 10% komisi platform, memindahkan 90% sisa dana net ke `guide_wallets`, dan memperbarui status escrow menjadi `released_to_guide`.
Fase 4: Settlement, E-Wallet, & Automated Maintenance
FR-04-01 (Guide E-Wallet & Payout Request): Guide dapat melihat saldo bersih dan mengajukan penarikan dana (withdrawal) ke bank lokal Indonesia.
FR-04-02 (Admin Payout Approval & Dispute Center): Admin memverifikasi antrean penarikan dana dan menangani transaksi yang dalam status `disputed`.
FR-04-03 (License Expiry Auto-Suspend - Task Scheduler): Command Laravel (`php artisan schedule:run`) berjalan otomatis setiap tengah malam untuk mengubah status guide menjadi `suspended` jika lisensi KTPP/SKCK telah melewati tanggal kedaluwarsa.
5. Metode Pengujian & Kelayakan Akademis (UAT & SUS)
A. Matriks User Acceptance Testing (UAT)
Kode UAT
Aktor Target
Skenario Pengujian
Kriteria Keberhasilan (Expected Outcome)
 
UAT-A01
Admin
Login Admin & Monitoring Dashboard Widgets
Berhasil masuk dashboard dan melihat ringkasan antrean legalitas & escrow.
UAT-A02
Admin
Audit Dokumen Legalitas Guide (KTP, KTPP, SKCK)
Status guide berubah menjadi `verified`/`active` atau ditolak.
UAT-A03
Admin
Pengelolaan Transaksi & Persetujuan Withdrawal
Status transaksi escrow terbarui dan transfer bank terkonfirmasi.
UAT-P01
Guide
Registrasi Multi-Step & Upload Legakitas
Akun terdaftar dengan status `pending_verification`.
UAT-P02
Guide
Manajemen Profil, Tarif, & Paket Wisata
Data spesialisasi dan tarif tersimpan & tampil di publik.
UAT-P03
Guide
Respon Order & Kontrol Status Trip Sequence
Status booking berhasil berprogres dari `heading_to_location` hingga `completed`.
UAT-W01
Wisatawan
Pencarian Guide Berbasis Filter Matching
Sistem menampilkan guide sesuai parameter spesialisasi & gaya komunikasi.
UAT-W02
Wisatawan
Fitur Pre-Booking Chat & Custom Itinerary
Pesan terkirim real-time dan kesepakatan itinerary tercapai.
UAT-W03
Wisatawan
Pengajuan Booking & Pembayaran Gateway (Escrow)
Booking terbuat (`pending_confirmation`) dan payment gateway memproses QRIS/VA.
UAT-W04
Wisatawan
Penilaian Rating & Ulasan Post-Tour
Modal ulasan muncul saat tour `completed` dan rating tersimpan di profil guide.

B. System Usability Scale (SUS)
Sistem dinyatakan memenuhi kriteria usabilitas akademis jika nilai rata-rata dari 10 pertanyaan standar SUS (Skala Likert 1–5) yang disebarkan kepada sampel Wisatawan, Guide, dan Admin memperoleh Skor SUS Minimal > 68.0 (Grade B / Acceptable Category).
6. Spesifikasi Lingkungan Deployment (Production Stack)
Server Engine: Linux Ubuntu 24.04 LTS (VPS Cloud).
Web Server: Native Nginx Reverse Proxy (Socket Connection: `unix:/var/run/php/php8.3-fpm.sock`).
Database Engine: MySQL 8.0+.
Security Layer: SSL Enkripsi HTTPS via Let's Encrypt (Certbot), Security Headers, `client_max_body_size 20M` (Upload KYC).
Cron Task: Linux Crontab (`* * * * * cd /var/www/tour-travel && php artisan schedule:run >> /dev/null 2>&1`).

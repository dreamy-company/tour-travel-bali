# Diagram Sistem (Versi Baru) — Sequence, DFD Level 1, DFD Level 2 & ERD

> **Platform Matchmaking Pemandu Wisata Lokal Berbasis Web untuk Mendukung Quality Tourism**
> I Gede Tio Mahesa Diputra — S1 Sistem Informasi, ITB STIKOM Bali (2026)

Semua diagram di folder ini disusun ulang dari **implementasi aktual** (migrasi `database/migrations`, `app/Enums`, komponen `app/Livewire`, `PaymentController`, `EscrowReleaseService`, dan `CheckExpiredLicenses`) per **10 Oktober 2026**, bukan dari draf SRS awal. Fitur yang ditambahkan setelah SRS ikut tergambar: favorit (wishlist), preferensi wisatawan, kecocokan zodiak (*Cosmic Match*), strike & ban guide, manajemen pengguna admin, dan resolusi sengketa.

## Daftar file

| File (`.mmd` = sumber Mermaid) | Gambar | Isi |
| --- | --- | --- |
| `erd.mmd` | `png/erd.png` | Entity Relationship Diagram — 10 tabel domain |
| `sequence.mmd` | `png/sequence.png` | Sequence diagram end-to-end (9 fase) |
| `dfd-level-1.mmd` | `png/dfd-level-1.png` | DFD Level 1 — 10 proses, 11 data store |
| `dfd-level-2-p1-registrasi.mmd` | `png/dfd-level-2-p1-registrasi.png` | DFD Level 2 — Proses 1.0 |
| `dfd-level-2-p2-verifikasi.mmd` | `png/dfd-level-2-p2-verifikasi.png` | DFD Level 2 — Proses 2.0 |
| `dfd-level-2-p3-profil-layanan.mmd` | `png/dfd-level-2-p3-profil-layanan.png` | DFD Level 2 — Proses 3.0 |
| `dfd-level-2-p4-matchmaking.mmd` | `png/dfd-level-2-p4-matchmaking.png` | DFD Level 2 — Proses 4.0 |
| `dfd-level-2-p5-chat.mmd` | `png/dfd-level-2-p5-chat.png` | DFD Level 2 — Proses 5.0 |
| `dfd-level-2-p6-booking-pembayaran.mmd` | `png/dfd-level-2-p6-booking-pembayaran.png` | DFD Level 2 — Proses 6.0 |
| `dfd-level-2-p7-tour-settlement.mmd` | `png/dfd-level-2-p7-tour-settlement.png` | DFD Level 2 — Proses 7.0 |
| `dfd-level-2-p8-wallet-sengketa.mmd` | `png/dfd-level-2-p8-wallet-sengketa.png` | DFD Level 2 — Proses 8.0 |

Proses **9.0 Rating & Ulasan** dan **10.0 Auto-Suspend Lisensi** merupakan proses primitif (fungsi tunggal), sehingga tidak didekomposisi ke Level 2.

---

## 1. ERD

10 tabel domain. Tabel infrastruktur Laravel (`sessions`, `password_reset_tokens`, `cache`, `jobs`, `passkeys`) tidak digambarkan.

| Relasi | Kardinalitas | Keterangan |
| --- | --- | --- |
| users → guide_profiles | 1 : 0..1 | `user_id` unik; hanya role `guide` |
| users (guide) → tour_packages | 1 : N | `tour_packages.guide_id` → `users.id` |
| users → bookings | 1 : N (dua kali) | sebagai `customer_id` dan `guide_id` |
| tour_packages → bookings | 0..1 : N | `tour_package_id` nullable (itinerary kustom) |
| bookings → escrow_transactions | 1 : 0..1 | `booking_id` unik; dibuat saat booking diajukan |
| users (guide) → guide_wallets | 1 : 0..1 | `guide_id` unik; dibuat saat rilis escrow pertama |
| users (guide) → withdrawals | 1 : N | |
| bookings → chat_messages | 0..1 : N | `booking_id` null = chat pre-booking |
| users → chat_messages | 1 : N (dua kali) | `sender_id` dan `receiver_id` |
| bookings → reviews | 1 : 0..1 | `booking_id` unik |
| users → reviews | 1 : N (dua kali) | `customer_id` dan `guide_id` |
| users → favorites | 1 : N (dua kali) | unik per pasangan (`customer_id`, `guide_id`) |

**Perbedaan utama dari ERD lama:** tabel `favorites` baru; `tour_packages` dan `withdrawals` merujuk langsung ke `users.id`; kolom baru `birth_date`, `traveler_preferences`, 2FA, `strikes`, `rejection_reason`, `signed_sop_at`, berkas dokumen, `snap_token`/`redirect_url`; enum `tariff_mode` bertambah `package`, `booking_status` bertambah `rejected`, `user_status` bertambah `banned`, `withdrawal_status` bertambah `processing`.

## 2. Sequence Diagram

| Fase | Ringkasan |
| --- | --- |
| 1. Registrasi & Verifikasi | Wisatawan langsung `active`; guide 4 langkah → `pending_verification` → admin approve (`active`) / reject (`suspended` + alasan) |
| 2. Matchmaking & Chat | Filter + Cosmic Match zodiak, favorit, chat pre-booking via polling 3 detik |
| 3. Booking & Pembayaran | `pending_confirmation` → guide terima (`waiting_payment`) / tolak (`rejected`) → Midtrans Snap → webhook → `confirmed` + `paid_in_escrow` |
| 4. Pelaksanaan Tour | `heading_to_location` → `ongoing` → `completed`; stepper wisatawan polling 5 detik |
| 5. Settlement | `EscrowReleaseService`: komisi 10%, net 90% ke wallet, escrow `released_to_guide` |
| 6. Rating & Ulasan | Modal ulasan setelah `completed`, bisa dilewati |
| 7. Withdrawal | Saldo didebit saat pengajuan; admin approve (`success`) / reject (`failed` + saldo dikembalikan) |
| 8. Sengketa | Admin rilis ke guide atau refund ke wisatawan |
| 9. Auto-Suspend | `guides:check-expired-licenses` harian |

## 3. DFD Level 1

**Entitas eksternal:** Wisatawan, Pemandu Wisata (Guide), Admin, Payment Gateway (Midtrans), Scheduler (Cron Harian).

| Proses | Fungsi | Komponen kode |
| --- | --- | --- |
| 1.0 Registrasi & Autentikasi | Registrasi wisatawan & guide, login, 2FA/passkey | Fortify `CreateNewUser`, `Auth\GuideRegister` |
| 2.0 Verifikasi & Manajemen Pengguna | Verifikasi KYC, edit, ban, strike, hapus guide | `Admin\DocumentVerification`, `Admin\UserManagement`, `GuideAdminService` |
| 3.0 Kelola Profil & Layanan | Persona, tarif, paket guide; profil & preferensi wisatawan | `Guide\ServiceManagement`, `Customer\ProfileSettings` |
| 4.0 Pencarian & Matchmaking | Filter, zodiak, profil guide, favorit | `Customer\GuideSearch`, `GuideShow`, `Wishlist`, `ZodiacService` |
| 5.0 Chat | Chat pre-booking & booking, inbox | `Chat\ChatRoom`, `Chat\Inbox` |
| 6.0 Booking & Pembayaran Escrow | Ajukan booking, konfirmasi guide, Snap, webhook | `Customer\BookingForm`, `Guide\OrderManagement`, `CustomerTrips`, `PaymentController`, `MidtransService` |
| 7.0 Pelaksanaan Tour & Settlement | State machine tour, live tracking, bagi hasil | `Guide\OrderManagement`, `Customer\BookingTracker`, `EscrowReleaseService` |
| 8.0 Wallet, Withdrawal & Sengketa | Saldo, payout, resolusi sengketa | `Guide\PayoutManagement`, `Admin\FinancialManagement` |
| 9.0 Rating & Ulasan | Simpan ulasan booking `completed` | `Customer\CustomerTrips`, `BookingTracker` |
| 10.0 Auto-Suspend Lisensi | Suspend guide dengan KTPP/SKCK kedaluwarsa | `CheckExpiredLicenses` |

### Kamus data store

| Store | Tabel / Lokasi | Ditulis oleh | Dibaca oleh |
| --- | --- | --- | --- |
| D1 Users | `users` | 1.0, 2.0, 3.0, 10.0 | 1.0, 2.0, 3.0, 4.0, 5.0, 10.0 |
| D2 Guide Profiles | `guide_profiles` | 1.0, 2.0, 3.0 | 2.0, 3.0, 4.0, 6.0, 10.0 |
| D3 Tour Packages | `tour_packages` | 2.0, 3.0 | 4.0, 6.0 |
| D4 Bookings | `bookings` | 6.0, 7.0, 8.0 | 5.0, 6.0, 7.0, 8.0, 9.0 |
| D5 Escrow Transactions | `escrow_transactions` | 6.0, 7.0, 8.0 | 2.0, 7.0, 8.0 |
| D6 Guide Wallets | `guide_wallets` | 7.0, 8.0 | 8.0 |
| D7 Withdrawals | `withdrawals` | 8.0 | 8.0 |
| D8 Chat Messages | `chat_messages` | 5.0 | 5.0 |
| D9 Reviews | `reviews` | 9.0 | 4.0 |
| D10 Favorites | `favorites` | 4.0 | 4.0 |
| D11 Berkas Dokumen KYC | `storage/app/private/guide_documents/*` | 1.0, 2.0 | 2.0 |

## 4. DFD Level 2

| Induk | Sub-proses |
| --- | --- |
| 1.0 | 1.1 Registrasi Wisatawan · 1.2 Buat Akun Guide · 1.3 KYC Tier 1 · 1.4 KYC Tier 2 · 1.5 SOP Sign-Off & Simpan · 1.6 Login & Autentikasi |
| 2.0 | 2.1 Antrean Verifikasi · 2.2 Tinjau Dokumen · 2.3 Approve/Reject · 2.4 Edit Pengguna · 2.5 Ban/Strike · 2.6 Hapus Paket/Dokumen/Akun |
| 3.0 | 3.1 Persona Guide · 3.2 Mode & Tarif · 3.3 Paket Wisata · 3.4 Profil & Preferensi Wisatawan |
| 4.0 | 4.1 Filter Guide · 4.2 Kecocokan Zodiak · 4.3 Profil Guide · 4.4 Favorit |
| 5.0 | 5.1 Otorisasi Ruang Chat · 5.2 Kirim Pesan · 5.3 Polling & Tandai Dibaca · 5.4 Inbox |
| 6.0 | 6.1 Hitung Harga & Ajukan Booking · 6.2 Konfirmasi Guide · 6.3 Transaksi Snap · 6.4 Notifikasi Pembayaran |
| 7.0 | 7.1 Update Status Tour · 7.2 Live Tracking · 7.3 Rilis Escrow & Bagi Hasil |
| 8.0 | 8.1 Saldo & Riwayat · 8.2 Ajukan Withdrawal · 8.3 Approve/Reject Payout · 8.4 Selesaikan Sengketa |

---

## Catatan implementasi yang perlu diperhatikan

1. **Status `disputed` belum pernah di-set oleh kode.** Admin dapat menyelesaikan booking `disputed` (proses 8.4), tetapi belum ada fitur bagi wisatawan/guide untuk mengajukan sengketa. Diagram menggambarkan alur penyelesaian saja.
2. **Status `cancelled` dan `processing`** ada di enum, tetapi belum ada alur yang mengubah booking menjadi `cancelled` atau withdrawal menjadi `processing`.
3. **Transfer bank payout dilakukan manual** oleh admin di luar sistem, lalu ditandai `success`. Karena itu, Bank tidak menjadi entitas eksternal di DFD.
4. **Real-time memakai polling Livewire** (`wire:poll`: chat 3 detik, tracker 5 detik, dashboard admin 10 detik), bukan WebSocket.

## Render ulang

```bash
npx -y @mermaid-js/mermaid-cli@11.4.2 -i "dfd-level-1.mmd" -o "png/dfd-level-1.png" -s 3 -b white
```

Jika muncul error *Could not find Chrome*, tambahkan `-p pptr.json` dengan isi
`{"executablePath":"C:/Program Files/Google/Chrome/Application/chrome.exe"}`.
`dfd-level-1.mmd` memakai layout **ELK** (`layout: elk`) agar garis aliran tidak saling tumpang tindih.

// === ENUMS ===
// Aligned with SRS (2026-08-12)

Enum user_role {
  customer
  guide
  admin
}

Enum user_status {
  pending_verification
  active
  suspended
  banned
}

Enum communication_style {
  santai
  edukatif
  profesional
  ekspresif
}

Enum specialization {
  cafe_hopping
  photography
  nightlife
  nature
  culture_history
  healing
}

Enum tariff_mode {
  hourly
  daily
}

Enum booking_status {
  pending_confirmation
  waiting_payment
  confirmed
  heading_to_location
  ongoing
  completed
  cancelled
  disputed
  rejected // guide declines a pending_confirmation request
}

Enum payment_method {
  qris
  credit_card
  virtual_account
}

Enum escrow_status {
  waiting_payment
  paid_in_escrow
  released_to_guide
  refunded
}

Enum withdrawal_status {
  pending
  processing
  success
  failed
}


// === TABLES ===

Table users {
  id bigint [primary key, increment]
  name varchar
  email varchar [unique]
  password varchar
  phone_number varchar
  role user_role
  status user_status [default: 'pending_verification']
  created_at timestamp
  updated_at timestamp
}

Table guide_profiles {
  id bigint [primary key, increment]
  user_id bigint [unique]
  ktp_number varchar [unique] // NIK KTP (SRS: nik_ktp)
  ktp_photo varchar
  headshot varchar
  ktpp_number varchar [note: 'Lisensi resmi HPI']
  ktpp_file varchar
  ktpp_expired_at date
  skck_file varchar
  skck_expired_at date
  surat_sehat_file varchar
  vehicle_details text
  bio text
  communication_style communication_style [note: 'SRS FR-02-01: same vibe']
  specializations json [note: 'SRS FR-02-01: same interest — array of specialization']
  languages json [note: 'Array of languages for filtering']
  tariff_mode tariff_mode
  base_rate decimal
  is_verified boolean [default: false]
  signed_sop_at timestamp [note: 'Digital Signature SOP']
  rejection_reason text
  strikes int [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table tour_packages {
  id bigint [primary key, increment]
  guide_id bigint
  title varchar
  description text
  price decimal
  destinations json [note: 'List of coordinates or places']
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table bookings {
  id bigint [primary key, increment]
  customer_id bigint
  guide_id bigint
  tour_package_id bigint [null]
  pickup_location text
  dropoff_location text [null]
  custom_destinations json [null] // SRS: custom_itinerary
  schedule_date date // SRS: booking_date
  pickup_time time
  total_price decimal
  status booking_status [default: 'pending_confirmation']
  created_at timestamp
  updated_at timestamp
}

Table escrow_transactions {
  id bigint [primary key, increment]
  booking_id bigint [unique]
  transaction_reference varchar // SRS: payment_gateway_ref
  payment_method payment_method
  gross_amount decimal
  platform_commission decimal [note: '10% cut']
  guide_net_amount decimal [note: '90% portion']
  status escrow_status [default: 'waiting_payment']
  snap_token varchar [null]
  redirect_url text [null]
  created_at timestamp
  updated_at timestamp
}

Table guide_wallets {
  id bigint [primary key, increment]
  guide_id bigint [unique]
  current_balance decimal [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table withdrawals {
  id bigint [primary key, increment]
  guide_id bigint // SRS: guide_wallet_id
  amount decimal
  bank_name varchar
  bank_account_number varchar
  bank_account_name varchar
  status withdrawal_status [default: 'pending']
  created_at timestamp
  updated_at timestamp
}

Table reviews {
  id bigint [primary key, increment]
  booking_id bigint [unique]
  customer_id bigint
  guide_id bigint
  rating tinyint
  comment text
  created_at timestamp
  updated_at timestamp
}

Table chat_messages {
  id bigint [primary key, increment]
  booking_id bigint [null, note: 'Null = pre-booking chat (SRS FR-02-02)']
  sender_id bigint
  receiver_id bigint [note: 'SRS FR-02-02']
  message text
  is_read boolean [default: false]
  created_at timestamp
  updated_at timestamp
}


// === RELATIONSHIPS ===

Ref: guide_profiles.user_id - users.id [delete: cascade]
Ref: tour_packages.guide_id > users.id

Ref: bookings.customer_id > users.id
Ref: bookings.guide_id > users.id
Ref: bookings.tour_package_id > tour_packages.id

Ref: escrow_transactions.booking_id - bookings.id
Ref: guide_wallets.guide_id - users.id
Ref: withdrawals.guide_id > users.id

Ref: reviews.booking_id - bookings.id
Ref: reviews.customer_id > users.id
Ref: reviews.guide_id > users.id

Ref: chat_messages.booking_id > bookings.id
Ref: chat_messages.sender_id > users.id
Ref: chat_messages.receiver_id > users.id

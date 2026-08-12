<?php

use App\Livewire\Auth\GuideRegister;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', \App\Livewire\Landing::class)->name('home');

Route::get('/guides', \App\Livewire\Customer\GuideSearch::class)->name('guides.index');
Route::get('/guides/{guideProfile}', \App\Livewire\Customer\GuideShow::class)->name('guides.show');
Route::get('/guides/{guideProfile}/book', \App\Livewire\Customer\BookingForm::class)->name('guides.book');
Route::get('/guide-photos/{guideProfile}', [\App\Http\Controllers\GuidePhotoController::class, 'show'])->name('guides.photo');

Route::middleware('guest')->group(function () {
    Route::get('register/guide', GuideRegister::class)->name('register.guide');
    Route::view('register/under-review', 'auth.under-review')->name('register.under-review');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Customer trips hub — customers are redirected here from /dashboard.
    Route::get('/my-bookings', \App\Livewire\Customer\CustomerTrips::class)->name('customer.trips');
    Route::get('/favorites', \App\Livewire\Customer\Wishlist::class)->name('favorites');
    Route::get('/profile', \App\Livewire\Customer\ProfileSettings::class)->name('customer.profile');

    Route::get('/bookings/{booking?}', \App\Livewire\Customer\BookingTracker::class)->name('bookings.tracker');
    Route::post('/bookings/{booking}/checkout', [PaymentController::class, 'checkout'])->name('bookings.checkout');
    Route::get('/chat/{receiver}', \App\Livewire\Chat\ChatRoom::class)->name('chat.room');

    // Role-aware dashboard shell: customers land on the trips hub.
    Route::get('/dashboard', function () {
        return auth()->user()->role === UserRole::CUSTOMER
            ? redirect()->route('customer.trips')
            : view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/documents/{profile}/{type}', [\App\Http\Controllers\Admin\DocumentController::class, 'show'])->name('admin.documents.show');
    Route::get('/admin/verification', \App\Livewire\Admin\DocumentVerification::class)->name('admin.verification');
    Route::get('/admin/finance', \App\Livewire\Admin\FinancialManagement::class)->name('admin.finance');
});

Route::middleware(['auth', 'role:guide'])->group(function () {
    Route::get('/guide/services', \App\Livewire\Guide\ServiceManagement::class)->name('guide.services');
    Route::get('/guide/orders', \App\Livewire\Guide\OrderManagement::class)->name('guide.orders');
    Route::get('/guide/payouts', \App\Livewire\Guide\PayoutManagement::class)->name('guide.payouts');
});

Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');

require __DIR__.'/settings.php';

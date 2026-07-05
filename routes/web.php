<?php

use App\Livewire\Auth\GuideRegister;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::view('/', 'welcome')->name('home');
Route::get('/guides', \App\Livewire\Customer\GuideSearch::class)->name('guides.index');

Route::middleware('guest')->group(function () {
    Route::get('register/guide', GuideRegister::class)->name('register.guide');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/bookings/{booking?}', \App\Livewire\Customer\BookingTracker::class)->name('bookings.tracker');
    Route::post('/bookings/{booking}/checkout', [PaymentController::class, 'checkout'])->name('bookings.checkout');
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

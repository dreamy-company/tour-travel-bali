<?php

use App\Livewire\Auth\GuideRegister;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/guides', \App\Livewire\Customer\GuideSearch::class)->name('guides.index');

Route::middleware('guest')->group(function () {
    Route::get('register/guide', GuideRegister::class)->name('register.guide');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/bookings/{booking}', \App\Livewire\Customer\BookingTracker::class)->name('bookings.tracker');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/documents/{profile}/{type}', [\App\Http\Controllers\Admin\DocumentController::class, 'show'])->name('admin.documents.show');
    Route::get('/admin/verification', \App\Livewire\Admin\DocumentVerification::class)->name('admin.verification');
});

Route::middleware(['auth', 'role:guide'])->group(function () {
    Route::get('/guide/services', \App\Livewire\Guide\ServiceManagement::class)->name('guide.services');
    Route::get('/guide/orders', \App\Livewire\Guide\OrderManagement::class)->name('guide.orders');
});

require __DIR__.'/settings.php';

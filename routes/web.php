<?php

use App\Livewire\Auth\GuideRegister;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('register/guide', GuideRegister::class)->name('register.guide');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/documents/{profile}/{type}', [\App\Http\Controllers\Admin\DocumentController::class, 'show'])->name('admin.documents.show');
    Route::get('/admin/verification', \App\Livewire\Admin\DocumentVerification::class)->name('admin.verification');
});

require __DIR__.'/settings.php';

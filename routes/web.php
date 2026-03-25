<?php

use App\Http\Controllers\AdminSubZonaController;
use App\Http\Controllers\NotifikasiSlotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RealTimeController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminZonaController;
use App\Http\Controllers\AdminAnalysisController;
use App\Http\Controllers\AdminSlotController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OnboardingController;


// landing Page
Route::get('/', [LandingPageController::class, 'index'])->name('login');

//Autentikasi
Route::post('/login-proses', [AuthController::class, 'login_proses'])->name('login_proses');
Route::post('/registrasi-proses', [AuthController::class, 'registrasi_proses'])->name('registrasi_proses');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

//logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Route untuk user
    Route::middleware('role:user')->group(function () {
        //Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('user-dashboard');

        // Onboarding
        Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
        Route::post('/onboarding/next', [OnboardingController::class, 'nextStep'])->name('onboarding.next');
        Route::post('/onboarding', [OnboardingController::class, 'update'])->name('onboarding.update');

        //real rime
        Route::get('/real-time', [RealTimeController::class, 'index'])->name('real-time');

        //analysis
        Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');

        //setting
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/change-password', [SettingsController::class, 'changePassword'])->name('change.password');
        Route::post('/profil/update-foto-kendaraan', [SettingsController::class, 'updateFotoKendaraan'])->name('profil.update.foto.kendaraan');
        Route::get('/reset-password/{token}/{id}', [SettingsController::class, 'showResetForm'])->middleware('signed')->name('password.reset');
        Route::post('/send-reset-link', [SettingsController::class, 'sendResetLink'])->name('password.email');
        Route::post('/reset-password', [SettingsController::class, 'reset'])->name('password.update');

        // Notifikasi slot
        Route::post('/notifikasi-slot/daftar', [NotifikasiSlotController::class, 'daftar'])->name('notifikasi.daftar');
        Route::post('/notifikasi-slot/batal', [NotifikasiSlotController::class, 'batal'])->name('notifikasi.batal');
        Route::get('/notifikasi-slot/status', [NotifikasiSlotController::class, 'status'])->name('notifikasi.status');
    });

    // Route untuk admin
    Route::middleware('role:admin')->group(function () {
        //admin
        Route::prefix('admin')->group(function () {
            // dashboard
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin-dashboard');
            
            // user
            Route::get('/users', [AdminUserController::class, 'index'])->name('admin-users');
            Route::delete('/users/{id}', [AdminUserController::class, 'delete'])->name('users.delete');
            Route::get('/search', [AdminUserController::class, 'search'])->name('search');

            // zona
            Route::get('/zona', [AdminZonaController::class, 'index'])->name('admin-zona');
            Route::post('/addZona', [AdminZonaController::class, 'store'])->name('zona.store');
            Route::put('/updateZona/{id_area}', [AdminZonaController::class, 'update'])->name('zona.update');
            Route::delete('/deleteZona/{id_area}', [AdminZonaController::class, 'destroy'])->name('zona.destroy');

            // Subzona
            Route::get('/subzona', [AdminSubZonaController::class, 'index'])->name('admin-subzona');
            Route::post('/addSubzona', [AdminSubZonaController::class, 'store'])->name('subzona.store');
            Route::put('/updateSubzona/{id}', [AdminSubZonaController::class, 'update'])->name('subzona.update');
            Route::delete('/deleteSubzona/{id}', [AdminSubZonaController::class, 'destroy'])->name('subzona.destroy');

            // Slot
            Route::get('/slot/{zona?}', [AdminSlotController::class, 'index'])->name('admin-slot');
            Route::get('/slot/subzona/{subzonaId}', [AdminSlotController::class, 'getSlotsBySubzona'])->name('slot.getBySubzona');
            Route::post('/slot', [AdminSlotController::class, 'store'])->name('slot.store');
            Route::put('/slot/{id}', [AdminSlotController::class, 'update'])->name('slot.update');
            Route::delete('/slot/{id}', [AdminSlotController::class, 'destroy'])->name('slot.destroy');

            // Analysis
            Route::get('/analysis', [AdminAnalysisController::class, 'index'])->name('admin-analysis');
        });
    });
});

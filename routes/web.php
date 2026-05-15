<?php

use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LanguageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetLocale::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
    Route::get('/vehicles', [HomeController::class, 'vehicles'])->name('frontend.vehicles');
    Route::get('/vehicle/{id}', [HomeController::class, 'vehicleDetail'])->name('frontend.vehicle.detail');
    Route::get('/compare', [HomeController::class, 'compare'])->name('frontend.compare');

    Route::get('/booking/step1', [BookingController::class, 'step1'])->name('frontend.booking.step1');
    Route::post('/booking/step2', [BookingController::class, 'step2'])->name('frontend.booking.step2');
    Route::get('/booking/step2', [BookingController::class, 'step2']);
    Route::post('/booking/step3', [BookingController::class, 'step3'])->name('frontend.booking.step3');
    Route::get('/booking/step3', [BookingController::class, 'step3']);
    Route::post('/booking/step4', [BookingController::class, 'step4'])->name('frontend.booking.step4');
    Route::get('/booking/step4', [BookingController::class, 'step4']);
    Route::post('/booking/store', [BookingController::class, 'store'])->name('frontend.booking.store');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('frontend.login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('frontend.logout');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('frontend.register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('frontend.dashboard');
    });
});

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');

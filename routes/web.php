<?php

use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LanguageController;
use App\Livewire\Customer\BookingConfirmation;
use App\Livewire\Customer\BookingSuccess;
use App\Livewire\Customer\PaymentHistoryPage;
use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/vehicles', [HomeController::class, 'vehicles'])->name('frontend.vehicles');
Route::get('/vehicle/{id}', [HomeController::class, 'vehicleDetail'])->name('frontend.vehicle.detail');
Route::get('/compare', [HomeController::class, 'compare'])->name('frontend.compare');
Route::get('/check-availability/{vehicle}', [HomeController::class, 'checkAvailability'])->name('frontend.availability.check');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('frontend.login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('frontend.logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('frontend.register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/booking/step1', [BookingController::class, 'step1'])->name('frontend.booking.step1');
Route::match(['get', 'post'], '/booking/step2', [BookingController::class, 'step2'])->name('frontend.booking.step2');
Route::match(['get', 'post'], '/booking/step3', [BookingController::class, 'step3'])->name('frontend.booking.step3');
Route::match(['get', 'post'], '/booking/step4', [BookingController::class, 'step4'])->name('frontend.booking.step4');
Route::get('/booking/confirm', BookingConfirmation::class)->name('frontend.booking.confirm');

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/booking/success/{booking}', BookingSuccess::class)->name('frontend.booking.success');
    Route::get('/booking/{id}', [BookingController::class, 'detail'])->name('frontend.booking.detail');
    Route::get('/booking/{id}/invoice', [BookingController::class, 'invoice'])->name('frontend.booking.invoice');
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('frontend.booking.cancel');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('frontend.dashboard');
    Route::get('/account/payments', PaymentHistoryPage::class)->name('frontend.payments');
    Route::get('/payment/{payment}/receipt', function (Payment $payment) {
        $customer = auth()->user()?->customer;

        abort_if($payment->booking->customer_id !== $customer?->id, 403);

        $pdf = Pdf::loadView('receipts.payment', [
            'payment' => $payment,
            'booking' => $payment->booking,
            'customer' => $payment->customer,
        ]);

        return $pdf->download("receipt-{$payment->id}.pdf");
    })->name('frontend.payment.receipt');
});

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::middleware(['auth', 'role:agency|super_admin'])->group(function () {
    Route::get('/agency/booking/{id}/contract', function ($id) {
        $booking = Booking::with(['vehicle', 'customer', 'customer.user', 'pickupCity', 'returnCity', 'vehicle.agency'])->findOrFail($id);

        return view('filament.agency.resources.booking.pages.contract-pdf', [
            'booking' => $booking,
            'vehicle' => $booking->vehicle,
            'customer' => $booking->customer,
            'agency' => $booking->vehicle->agency,
        ]);
    })->name('agency.booking.contract');
});

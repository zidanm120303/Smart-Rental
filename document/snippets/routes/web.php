<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/aset', AssetController::class)->names('assets');

    Route::post('/booking/cek-ketersediaan', [BookingController::class, 'checkAvailability'])->name('bookings.availability.check');
    Route::post('/booking/{booking}/setujui', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/booking/{booking}/batalkan', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/booking/{booking}/pickup', [BookingController::class, 'pickup'])->name('bookings.pickup');
    Route::post('/booking/{booking}/return', [BookingController::class, 'return'])->name('bookings.return');
    Route::resource('/booking', BookingController::class)->names('bookings');

    Route::resource('/customer', CustomerController::class)->names('customers');

    Route::get('/kalender', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/kalender/events', [CalendarController::class, 'events'])->name('calendar.events');

    Route::post('/invoice/dari-booking/{booking}', [InvoiceController::class, 'fromBooking'])->name('invoices.from-booking');
    Route::post('/invoice/{invoice}/pembayaran', [PaymentController::class, 'store'])->name('invoices.payments.store');
    Route::resource('/invoice', InvoiceController::class)->names('invoices');

    Route::resource('/maintenance', MaintenanceController::class)->names('maintenance');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';

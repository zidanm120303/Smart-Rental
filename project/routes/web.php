<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/aset', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/aset', [AssetController::class, 'store'])->name('assets.store');
    Route::put('/aset/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/aset/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::get('/aset/tabel', fn () => redirect()->route('assets.index', ['view' => 'table']))->name('assets.table');
    Route::get('/aset/grid', fn () => redirect()->route('assets.index', ['view' => 'grid']))->name('assets.grid');

    Route::get('/booking', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/booking/baru', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('bookings.store');
    Route::put('/booking/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/booking/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/booking/cek-ketersediaan', [BookingController::class, 'checkAvailability'])->name('bookings.availability.check');
    Route::post('/booking/{booking}/setujui', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/booking/{booking}/batalkan', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/booking/{booking}/pickup', [BookingController::class, 'pickup'])->name('bookings.pickup');
    Route::post('/booking/{booking}/return', [BookingController::class, 'returnAssets'])->name('bookings.return');

    Route::get('/customer', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/kalender', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/kalender/events', [CalendarController::class, 'events'])->name('calendar.events');

    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoice', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoice/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoice/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('/invoice/dari-booking/{booking}', [InvoiceController::class, 'fromBooking'])->name('invoices.from-booking');
    Route::post('/invoice/{invoice}/pembayaran', [PaymentController::class, 'store'])->name('invoices.payments.store');

    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::put('/maintenance/{maintenanceRequest}', [MaintenanceController::class, 'update'])->name('maintenance.update');
    Route::delete('/maintenance/{maintenanceRequest}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{inventoryItem}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventoryItem}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/{inventoryItem}/mutasi', [InventoryController::class, 'moveStock'])->name('inventory.movements.store');
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/staff/{user}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
    Route::get('/lokasi', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/lokasi', [LocationController::class, 'store'])->name('locations.store');
    Route::put('/lokasi/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/lokasi/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

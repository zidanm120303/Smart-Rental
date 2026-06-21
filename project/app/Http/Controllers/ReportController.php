<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;

class ReportController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('reports.view'), 403, 'Anda tidak memiliki akses ke Laporan.');

        return view('pages.reports.index', [
            'revenue' => Payment::sum('amount'),
            'bookings' => Booking::count(),
            'customers' => Customer::count(),
            'assets' => Asset::count(),
            'invoices' => Invoice::with('customer')->latest()->limit(6)->get(),
            'topAssets' => Asset::orderByDesc('total_rented')->limit(6)->get(),
        ]);
    }
}

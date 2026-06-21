<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\MaintenanceRequest;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('dashboard.view'), 403, 'Anda tidak memiliki akses ke fitur ini.');

        $stats = [
            'total_aset' => Asset::count(),
            'booking_aktif' => Booking::whereIn('status', ['approved', 'active'])->count(),
            'pendapatan_bulan_ini' => Payment::whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            'aset_maintenance' => Asset::where('availability_status', 'maintenance')->count(),
        ];

        $bookingTerbaru = Booking::with('customer')->latest()->limit(8)->get();
        $maintenanceMendesak = MaintenanceRequest::with('asset')->whereIn('status', ['new', 'in_progress', 'waiting_parts'])->latest()->limit(5)->get();

        return view('pages.dashboard.index', compact('stats', 'bookingTerbaru', 'maintenanceMendesak'));
    }
}

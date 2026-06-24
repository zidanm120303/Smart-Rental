<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\InventoryItem;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('dashboard.view'), 403, 'Anda tidak memiliki akses ke Dasbor.');

        $totalAssets = Asset::count();
        $utilizedAssets = Asset::whereIn('availability_status', ['rented', 'reserved'])->count();

        $stats = [
            'total_aset' => $totalAssets,
            'booking_aktif' => Booking::whereIn('status', ['approved', 'active'])->count(),
            'pendapatan_bulan_ini' => Payment::whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            'aset_maintenance' => Asset::where('availability_status', 'maintenance')->count(),
            'utilisasi' => $totalAssets > 0 ? round(($utilizedAssets / $totalAssets) * 100) : 0,
        ];

        $bookingTerbaru = Booking::with(['customer', 'items.asset'])->latest()->limit(5)->get();
        $topAssets = Asset::with(['category', 'primaryMedia'])->orderByDesc('total_rented')->limit(5)->get();
        $activeMaintenanceQuery = MaintenanceRequest::whereIn('status', ['new', 'in_progress', 'waiting_parts']);
        $lowStockQuery = InventoryItem::query()->whereColumn('stock', '<=', 'minimum_stock');

        $maintenanceMendesak = (clone $activeMaintenanceQuery)->with('asset')->latest()->limit(5)->get();
        $lowStockItems = (clone $lowStockQuery)->limit(5)->get();

        $dashboardCounts = [
            'recent_bookings_total' => Booking::count(),
            'overdue_returns' => Booking::where('status', 'overdue')->count(),
            'active_maintenance' => (clone $activeMaintenanceQuery)->count(),
            'low_stock' => (clone $lowStockQuery)->count(),
            'upcoming_pickups' => Booking::whereIn('status', ['pending', 'approved', 'active'])
                ->whereBetween('pickup_at', [now(), now()->addDays(7)])
                ->count(),
        ];

        $statusCounts = Booking::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $revenueTrend = collect(range(1, 12))->map(function ($month) {
            return [
                'label' => now()->month($month)->translatedFormat('M'),
                'value' => (int) Payment::whereMonth('payment_date', $month)->whereYear('payment_date', now()->year)->sum('amount'),
            ];
        });

        return view('pages.dashboard.index', compact(
            'stats',
            'bookingTerbaru',
            'topAssets',
            'maintenanceMendesak',
            'lowStockItems',
            'dashboardCounts',
            'statusCounts',
            'revenueTrend'
        ));
    }
}

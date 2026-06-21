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

        $bookingTerbaru = Booking::with(['customer', 'items.asset'])->latest()->limit(6)->get();
        $topAssets = Asset::with('category')->orderByDesc('total_rented')->limit(5)->get();
        $maintenanceMendesak = MaintenanceRequest::with('asset')->whereIn('status', ['new', 'in_progress', 'waiting_parts'])->latest()->limit(5)->get();
        $lowStockItems = InventoryItem::query()->whereColumn('stock', '<=', 'minimum_stock')->limit(5)->get();

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
            'statusCounts',
            'revenueTrend'
        ));
    }
}

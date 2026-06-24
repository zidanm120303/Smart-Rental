<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Booking;
use App\Models\Location;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\User;

class CalendarController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('calendar.view'), 403, 'Anda tidak memiliki akses ke Kalender Operasional.');

        $start = now()->subMonths(2);
        $end = now()->addMonths(2);
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $bookings = Booking::with('customer')
            ->whereBetween('pickup_at', [$start, $end])
            ->orderBy('pickup_at')
            ->get();

        $maintenance = MaintenanceRequest::with('asset')
            ->whereBetween('scheduled_at', [$start, $end])
            ->orderBy('scheduled_at')
            ->get();

        $totalAssets = Asset::where('is_active', true)->count();
        $usedAssets = Asset::where('is_active', true)->whereIn('availability_status', ['rented', 'reserved'])->count();
        $availableAssets = Asset::where('is_active', true)->where('availability_status', 'available')->count();
        $maintenanceAssets = Asset::where('is_active', true)->where('availability_status', 'maintenance')->count();

        $calendarStats = [
            'bookings' => Booking::whereBetween('pickup_at', [$monthStart, $monthEnd])->count(),
            'utilization' => $totalAssets > 0 ? round(($usedAssets / $totalAssets) * 100) : 0,
            'revenue' => Payment::whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount'),
            'maintenance' => MaintenanceRequest::whereIn('status', ['new', 'in_progress', 'waiting_parts'])->count(),
            'total_assets' => $totalAssets,
            'used_assets' => $usedAssets,
            'available_assets' => $availableAssets,
            'maintenance_assets' => $maintenanceAssets,
        ];

        $bookingAgenda = Booking::with('customer')
            ->where('pickup_at', '>=', now())
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->orderBy('pickup_at')
            ->limit(5)
            ->get()
            ->toBase()
            ->map(fn ($booking) => [
                'color' => 'emerald',
                'title' => 'Pengambilan ' . ($booking->customer->name ?? '-'),
                'subtitle' => $booking->booking_code,
                'starts_at' => $booking->pickup_at,
            ]);

        $maintenanceAgenda = MaintenanceRequest::with('asset')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['new', 'in_progress', 'waiting_parts'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get()
            ->toBase()
            ->map(fn ($request) => [
                'color' => 'rose',
                'title' => $request->issue_title,
                'subtitle' => $request->asset->name ?? '-',
                'starts_at' => $request->scheduled_at,
            ]);

        $upcomingAgenda = $bookingAgenda
            ->merge($maintenanceAgenda)
            ->sortBy('starts_at')
            ->take(5)
            ->values();

        $staffOnDuty = User::with('roles')
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin_operasional', 'staff_gudang', 'teknisi']))
            ->orderBy('name')
            ->limit(3)
            ->get();

        return view('pages.calendar.index', [
            'bookings' => $bookings,
            'maintenance' => $maintenance,
            'calendarStats' => $calendarStats,
            'upcomingAgenda' => $upcomingAgenda,
            'staffOnDuty' => $staffOnDuty,
            'categories' => AssetCategory::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function events()
    {
        abort_unless(auth()->user()->hasPermission('calendar.view'), 403, 'Anda tidak memiliki akses ke Kalender Operasional.');

        $bookingEvents = Booking::with('customer')->get()->toBase()->flatMap(function ($booking) {
            return [
                [
                    'title' => 'Pengambilan - ' . ($booking->customer->name ?? '-'),
                    'start' => $booking->pickup_at->toIso8601String(),
                    'className' => 'event-pickup',
                    'backgroundColor' => '#dbeafe',
                    'borderColor' => '#bfdbfe',
                    'textColor' => '#1d4ed8',
                ],
                [
                    'title' => 'Pengembalian - ' . ($booking->customer->name ?? '-'),
                    'start' => $booking->return_at->toIso8601String(),
                    'className' => 'event-return',
                    'backgroundColor' => '#dcfce7',
                    'borderColor' => '#bbf7d0',
                    'textColor' => '#047857',
                ],
            ];
        });

        $maintenanceEvents = MaintenanceRequest::with('asset')
            ->whereNotNull('scheduled_at')
            ->get()
            ->toBase()
            ->map(fn ($request) => [
                'title' => 'Perawatan - ' . ($request->asset->name ?? '-'),
                'start' => $request->scheduled_at->toIso8601String(),
                'className' => 'event-maintenance',
                'backgroundColor' => '#ffe4e6',
                'borderColor' => '#fecdd3',
                'textColor' => '#be123c',
            ]);

        return response()->json($bookingEvents->merge($maintenanceEvents)->values());
    }
}

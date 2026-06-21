<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\Booking;
use App\Models\Location;
use App\Models\MaintenanceRequest;

class CalendarController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('calendar.view'), 403, 'Anda tidak memiliki akses ke Kalender Operasional.');

        $bookings = Booking::with('customer')->whereBetween('pickup_at', [now()->subMonths(2), now()->addMonths(2)])->get();
        $maintenance = MaintenanceRequest::with('asset')->whereBetween('scheduled_at', [now()->subMonths(2), now()->addMonths(2)])->get();

        return view('pages.calendar.index', [
            'bookings' => $bookings,
            'maintenance' => $maintenance,
            'categories' => AssetCategory::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function events()
    {
        abort_unless(auth()->user()->hasPermission('calendar.view'), 403, 'Anda tidak memiliki akses ke Kalender Operasional.');

        $bookingEvents = Booking::with('customer')->get()->flatMap(function ($booking) {
            return [
                [
                    'title' => 'Pickup - ' . $booking->customer->name,
                    'start' => $booking->pickup_at->toIso8601String(),
                    'className' => 'event-pickup',
                ],
                [
                    'title' => 'Return - ' . $booking->customer->name,
                    'start' => $booking->return_at->toIso8601String(),
                    'className' => 'event-return',
                ],
            ];
        });

        $maintenanceEvents = MaintenanceRequest::with('asset')->get()->map(fn ($request) => [
            'title' => 'Perawatan - ' . $request->asset->name,
            'start' => optional($request->scheduled_at)->toIso8601String(),
            'className' => 'event-maintenance',
        ]);

        return response()->json($bookingEvents->merge($maintenanceEvents)->values());
    }
}

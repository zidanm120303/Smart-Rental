<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\Customer;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('bookings.view'), 403, 'Anda tidak memiliki akses ke fitur ini.');

        $bookings = Booking::with('customer')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.bookings.index', compact('bookings'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('bookings.create'), 403, 'Anda tidak memiliki akses ke fitur ini.');

        return view('pages.bookings.create', [
            'customers' => Customer::orderBy('name')->get(),
            'assets' => Asset::available()->with(['category', 'location'])->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->createBooking($request->validated(), auth()->id());

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking berhasil dibuat.');
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'asset_ids' => ['required', 'array', 'min:1'],
            'asset_ids.*' => ['integer', 'exists:assets,id'],
            'pickup_at' => ['required', 'date'],
            'return_at' => ['required', 'date', 'after:pickup_at'],
        ]);

        $result = $this->bookingService->checkAvailability($validated['asset_ids'], $validated['pickup_at'], $validated['return_at']);

        return response()->json([
            'available' => $result['available'],
            'message' => $result['available'] ? 'Semua aset tersedia pada jadwal yang dipilih.' : 'Beberapa aset tidak tersedia pada jadwal yang dipilih.',
            'conflicts' => $result['conflicts'],
        ]);
    }
}

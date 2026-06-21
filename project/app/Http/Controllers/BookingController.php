<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\Customer;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('bookings.view'), 403, 'Anda tidak memiliki akses ke Booking.');

        $bookings = Booking::with(['customer', 'items.asset'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('booking_code', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.bookings.index', [
            'bookings' => $bookings,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'assets' => Asset::with(['category', 'location'])->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('bookings.create'), 403, 'Anda tidak memiliki akses membuat Booking.');

        return view('pages.bookings.create', [
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'assets' => Asset::with(['category', 'location'])->where('is_active', true)->orderBy('name')->get(),
            'bookingCode' => 'BK-' . now()->format('Ymd') . '-AUTO',
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->createBooking($request->validated(), auth()->id());

        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dibuat dan aset sudah diblokir.');
    }

    public function update(Request $request, Booking $booking)
    {
        abort_unless(auth()->user()->hasPermission('bookings.update'), 403, 'Anda tidak memiliki akses mengubah pemesanan.');

        if (in_array($booking->status, ['completed', 'cancelled'], true)) {
            return back()->withErrors(['booking' => 'Pemesanan selesai atau dibatalkan tidak dapat diubah.']);
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'asset_ids' => ['required', 'array', 'min:1'],
            'asset_ids.*' => ['integer', 'exists:assets,id'],
            'pickup_at' => ['required', 'date'],
            'return_at' => ['required', 'date', 'after:pickup_at'],
            'delivery_method' => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,pending,approved,active,overdue'],
        ]);

        $booking = $this->bookingService->updateBooking($booking, $validated);

        return redirect()->route('bookings.index', ['status' => $booking->status])->with('success', 'Pemesanan berhasil diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        abort_unless(auth()->user()->hasPermission('bookings.cancel'), 403, 'Anda tidak memiliki akses menghapus pemesanan.');

        if ($booking->invoice()->exists()) {
            return back()->withErrors(['booking' => 'Pemesanan tidak dapat dihapus karena sudah memiliki tagihan.']);
        }

        DB::transaction(function () use ($booking) {
            $booking->items()->with('asset')->get()->each(fn ($item) => $item->asset?->update(['availability_status' => 'available']));
            $booking->delete();
        });

        return redirect()->route('bookings.index')->with('success', 'Pemesanan berhasil dihapus.');
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'asset_ids' => ['required', 'array', 'min:1'],
            'asset_ids.*' => ['integer', 'exists:assets,id'],
            'pickup_at' => ['required', 'date'],
            'return_at' => ['required', 'date', 'after:pickup_at'],
        ], [
            'asset_ids.required' => 'Pilih minimal satu aset.',
            'return_at.after' => 'Jadwal kembali harus setelah jadwal pickup.',
        ]);

        $result = $this->bookingService->checkAvailability($validated['asset_ids'], $validated['pickup_at'], $validated['return_at']);

        return response()->json([
            'available' => $result['available'],
            'message' => $result['available'] ? 'Semua aset tersedia pada jadwal yang dipilih.' : 'Beberapa aset tidak tersedia pada jadwal tersebut.',
            'conflicts' => $result['conflicts'],
        ]);
    }

    public function approve(Booking $booking)
    {
        abort_unless(auth()->user()->hasPermission('bookings.approve'), 403, 'Anda tidak memiliki akses menyetujui Booking.');

        $booking->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);

        return back()->with('success', 'Booking berhasil disetujui.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        abort_unless(auth()->user()->hasPermission('bookings.cancel'), 403, 'Anda tidak memiliki akses membatalkan Booking.');

        $booking->update(['status' => 'cancelled', 'cancelled_reason' => $request->input('cancelled_reason', 'Dibatalkan dari dashboard.')]);
        $booking->items()->with('asset')->get()->each(fn ($item) => $item->asset?->update(['availability_status' => 'available']));

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    public function pickup(Booking $booking)
    {
        abort_unless(auth()->user()->hasPermission('bookings.update'), 403, 'Anda tidak memiliki akses proses pickup.');

        $booking->update(['status' => 'active']);
        $booking->items()->with('asset')->get()->each(fn ($item) => $item->asset?->update(['availability_status' => 'rented']));

        return back()->with('success', 'Pickup berhasil dicatat. Status aset menjadi disewa.');
    }

    public function returnAssets(Booking $booking)
    {
        abort_unless(auth()->user()->hasPermission('bookings.update'), 403, 'Anda tidak memiliki akses proses return.');

        $booking->update(['status' => 'completed']);
        $booking->items()->with('asset')->get()->each(function ($item) {
            $item->update(['returned_at' => now()]);
            $item->asset?->update(['availability_status' => 'available']);
        });

        return back()->with('success', 'Return berhasil dicatat. Aset kembali tersedia.');
    }
}

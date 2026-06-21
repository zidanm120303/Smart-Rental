<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\BookingItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function checkAvailability(array $assetIds, string $pickupAt, string $returnAt, ?int $ignoreBookingId = null): array
    {
        $conflicts = BookingItem::query()
            ->select('booking_items.asset_id', 'bookings.booking_code', 'bookings.pickup_at', 'bookings.return_at', 'assets.name as asset_name')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->join('assets', 'assets.id', '=', 'booking_items.asset_id')
            ->whereIn('booking_items.asset_id', $assetIds)
            ->whereIn('bookings.status', ['pending', 'approved', 'active'])
            ->when($ignoreBookingId, fn ($query) => $query->where('bookings.id', '!=', $ignoreBookingId))
            ->where('bookings.pickup_at', '<', $returnAt)
            ->where('bookings.return_at', '>', $pickupAt)
            ->get();

        return [
            'available' => $conflicts->isEmpty(),
            'conflicts' => $conflicts,
        ];
    }

    public function createBooking(array $payload, int $userId): Booking
    {
        return DB::transaction(function () use ($payload, $userId) {
            $assetIds = collect($payload['asset_ids'])->map(fn ($id) => (int) $id)->unique()->values()->all();

            $assets = Asset::query()->whereIn('id', $assetIds)->lockForUpdate()->get();

            if ($assets->count() !== count($assetIds)) {
                throw ValidationException::withMessages(['asset_ids' => 'Beberapa aset tidak ditemukan.']);
            }

            $notRentable = $assets->filter(fn ($asset) => !in_array($asset->availability_status, ['available', 'reserved'], true));
            if ($notRentable->isNotEmpty()) {
                throw ValidationException::withMessages(['asset_ids' => 'Beberapa aset tidak dapat disewa karena sedang disewa atau maintenance.']);
            }

            $availability = $this->checkAvailability($assetIds, $payload['pickup_at'], $payload['return_at']);
            if (!$availability['available']) {
                throw ValidationException::withMessages(['pickup_at' => 'Aset tidak tersedia pada jadwal yang dipilih.']);
            }

            $pickup = Carbon::parse($payload['pickup_at']);
            $return = Carbon::parse($payload['return_at']);
            $rentalDays = max(1, (int) ceil($pickup->floatDiffInHours($return) / 24));

            $subtotal = $assets->sum(fn ($asset) => (float) $asset->daily_rate * $rentalDays);
            $discount = (float) ($payload['discount_amount'] ?? 0);
            $insurance = (float) ($payload['insurance_amount'] ?? 0);
            $delivery = (float) ($payload['delivery_fee'] ?? 0);
            $taxRate = (float) config('rental.tax_rate', 0.11);
            $tax = max(0, ($subtotal - $discount + $insurance + $delivery) * $taxRate);
            $grandTotal = max(0, $subtotal - $discount + $insurance + $delivery + $tax);

            $booking = Booking::create([
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $payload['customer_id'],
                'user_id' => $userId,
                'pickup_at' => $payload['pickup_at'],
                'return_at' => $payload['return_at'],
                'delivery_method' => $payload['delivery_method'] ?? 'pickup',
                'status' => $payload['status'] ?? 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'insurance_amount' => $insurance,
                'delivery_fee' => $delivery,
                'tax_amount' => $tax,
                'deposit_amount' => $grandTotal * (float) config('rental.deposit_rate', 0.30),
                'grand_total' => $grandTotal,
                'notes' => $payload['notes'] ?? null,
            ]);

            foreach ($assets as $asset) {
                $booking->items()->create([
                    'asset_id' => $asset->id,
                    'daily_rate' => $asset->daily_rate,
                    'quantity' => 1,
                    'rental_days' => $rentalDays,
                    'line_total' => (float) $asset->daily_rate * $rentalDays,
                ]);
            }

            Asset::whereIn('id', $assetIds)->update(['availability_status' => 'reserved']);

            return $booking->load(['customer', 'items.asset']);
        });
    }
}

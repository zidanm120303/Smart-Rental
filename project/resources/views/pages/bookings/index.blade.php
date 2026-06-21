@extends('layouts.app')

@section('content')
@php($bookingCollection = $bookings->getCollection())
<div class="space-y-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-500">Pemesanan</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Semua Pemesanan</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau pemesanan draf, menunggu, aktif, selesai, dan terlambat.</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="sr-button-primary"><i data-lucide="calendar-plus" class="h-4 w-4"></i> Pemesanan Baru</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Total Pemesanan" :value="number_format($bookings->total(), 0, ',', '.')" trend="Semua status" icon="calendar-days" tone="blue" />
        <x-stat-card title="Pemesanan Aktif" :value="number_format($bookingCollection->where('status', 'active')->count(), 0, ',', '.')" trend="Halaman ini" icon="play-circle" tone="emerald" />
        <x-stat-card title="Menunggu Persetujuan" :value="number_format($bookingCollection->where('status', 'pending')->count(), 0, ',', '.')" trend="Butuh persetujuan" icon="clock" tone="amber" />
        <x-stat-card title="Terlambat" :value="number_format($bookingCollection->where('status', 'overdue')->count(), 0, ',', '.')" trend="Perlu tindak lanjut" icon="alert-triangle" tone="rose" />
    </div>

    <div class="sr-card overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-wrap gap-2">
                @foreach (['' => 'Semua', 'draft' => 'Draf', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'active' => 'Aktif', 'completed' => 'Selesai', 'overdue' => 'Terlambat'] as $status => $label)
                    <a href="{{ route('bookings.index', $status ? ['status' => $status] : []) }}" class="rounded-xl px-3 py-2 text-sm font-bold {{ request('status') === $status || (!request('status') && $status === '') ? 'bg-blue-600 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
                @endforeach
            </div>
            <form method="GET" class="relative">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input name="search" value="{{ request('search') }}" class="sr-input w-full pl-10 md:w-72" placeholder="Cari kode pemesanan atau pelanggan...">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="sr-table min-w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th>Kode Pemesanan</th>
                        <th>Pelanggan</th>
                        <th>Aset</th>
                        <th>Pengambilan</th>
                        <th>Kembali</th>
                        <th>Pengiriman</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="font-bold text-blue-700">{{ $booking->booking_code }}</td>
                            <td>
                                <p class="font-semibold text-slate-950">{{ $booking->customer->name }}</p>
                                <p class="text-xs text-slate-500">{{ $booking->customer->phone }}</p>
                            </td>
                            <td>{{ $booking->items->count() }} item</td>
                            <td>{{ $booking->pickup_at->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $booking->return_at->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $booking->delivery_label }}</td>
                            <td><x-status-badge :status="$booking->status" /></td>
                            <td class="font-bold text-slate-950">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if ($booking->status === 'pending')
                                        <form method="POST" action="{{ route('bookings.approve', $booking) }}">@csrf<button class="rounded-lg p-2 text-emerald-600 hover:bg-emerald-50" title="Setujui"><i data-lucide="check" class="h-4 w-4"></i></button></form>
                                    @endif
                                    @if (in_array($booking->status, ['approved', 'pending'], true))
                                        <form method="POST" action="{{ route('bookings.pickup', $booking) }}">@csrf<button class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Pengambilan"><i data-lucide="package-check" class="h-4 w-4"></i></button></form>
                                    @endif
                                    @if ($booking->status === 'active')
                                        <form method="POST" action="{{ route('bookings.return', $booking) }}">@csrf<button class="rounded-lg p-2 text-violet-600 hover:bg-violet-50" title="Pengembalian"><i data-lucide="rotate-ccw" class="h-4 w-4"></i></button></form>
                                    @endif
                                    @unless (in_array($booking->status, ['completed', 'cancelled'], true))
                                        <button type="button" onclick="document.getElementById('edit-booking-{{ $booking->id }}').showModal()" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Edit pemesanan"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                                    @endunless
                                    <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Hapus pemesanan {{ $booking->booking_code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Hapus pemesanan"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ $bookings->links() }}
</div>

@foreach ($bookings as $booking)
    @unless (in_array($booking->status, ['completed', 'cancelled'], true))
        <x-modal-form id="edit-booking-{{ $booking->id }}" title="Edit Pemesanan" description="{{ $booking->booking_code }} - {{ $booking->customer->name }}" size="4xl">
            @include('pages.bookings._edit_form', ['booking' => $booking, 'customers' => $customers, 'assets' => $assets])
        </x-modal-form>
    @endunless
@endforeach
@endsection

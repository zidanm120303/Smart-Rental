@extends('layouts.app')

@section('content')
@php
    $statusLabels = [
        'draft' => 'Draf',
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'active' => 'Aktif',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'overdue' => 'Terlambat',
    ];
    $statusChartLabels = $statusCounts->keys()->map(fn ($status) => $statusLabels[$status] ?? $status)->values();
    $statusChartValues = $statusCounts->values();
    $totalBookingStatus = max((int) $statusChartValues->sum(), 1);
    $canViewAssets = auth()->user()->hasPermission('assets.view');
    $canViewBookings = auth()->user()->hasPermission('bookings.view');
    $canViewMaintenance = auth()->user()->hasPermission('maintenance.view');
    $canViewInventory = auth()->user()->hasPermission('inventory.view');
@endphp

<div class="w-full space-y-4 2xl:space-y-5">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Total Aset" :value="number_format($stats['total_aset'], 0, ',', '.')" trend="Tersebar di semua kategori" icon="camera" tone="blue" />
        <x-stat-card title="Pemesanan Aktif" :value="number_format($stats['booking_aktif'], 0, ',', '.')" trend="Sedang dalam masa rental" icon="calendar-check" tone="emerald" />
        <x-stat-card title="Pendapatan Bulan Ini" :value="'Rp ' . number_format($stats['pendapatan_bulan_ini'], 0, ',', '.')" trend="Dari pembayaran tercatat" icon="badge-dollar-sign" tone="amber" />
        <x-stat-card title="Aset Perawatan" :value="number_format($stats['aset_maintenance'], 0, ',', '.')" trend="Perlu perhatian teknisi" icon="wrench" tone="rose" />
    </section>

    <section class="grid items-stretch gap-4 xl:grid-cols-4">
        <div class="sr-card overflow-hidden p-4 xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-base font-bold text-slate-950">Tren Pendapatan</h1>
                    <p class="mt-1 text-xs font-medium text-slate-500">Pendapatan bulanan tahun berjalan.</p>
                </div>
                <span class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600">Tahun Ini</span>
            </div>
            <div
                data-chart="revenue"
                data-height="300"
                data-labels='@json($revenueTrend->pluck('label'))'
                data-values='@json($revenueTrend->pluck('value'))'
                class="mt-3 min-h-[300px]"
            ></div>
        </div>

        <div class="sr-card overflow-hidden p-4">
            <h2 class="text-base font-bold text-slate-950">Status Pemesanan</h2>
            <div class="mt-3 grid min-h-[300px] items-center gap-3 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                <div
                    data-chart="booking-status"
                    data-height="220"
                    data-labels='@json($statusChartLabels)'
                    data-values='@json($statusChartValues)'
                    class="min-h-[220px]"
                ></div>
                <div class="space-y-2 text-xs">
                    @forelse ($statusCounts as $status => $count)
                        @php
                            $palette = [
                                'approved' => 'bg-blue-600',
                                'active' => 'bg-emerald-500',
                                'pending' => 'bg-amber-500',
                                'completed' => 'bg-violet-500',
                                'cancelled' => 'bg-rose-500',
                                'overdue' => 'bg-rose-500',
                                'draft' => 'bg-slate-400',
                            ][$status] ?? 'bg-slate-400';
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $palette }}"></span>
                            <span class="min-w-0 flex-1 truncate text-slate-600">{{ $statusLabels[$status] ?? $status }}</span>
                            <strong class="shrink-0 text-slate-950">{{ $count }} ({{ round(($count / $totalBookingStatus) * 100) }}%)</strong>
                        </div>
                    @empty
                        <p class="text-slate-500">Belum ada data status.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="sr-card flex min-h-[21rem] flex-col p-4">
            <h2 class="text-base font-bold text-slate-950">Utilisasi Aset</h2>
            <div data-chart="utilization" data-height="220" data-value="{{ $stats['utilisasi'] }}" class="mt-2 min-h-[220px]"></div>
            <div class="mt-auto rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-center text-xs font-bold text-blue-700">
                {{ $stats['utilisasi'] }}% aset terpakai atau dipesan
            </div>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-3">
        <div class="sr-card overflow-hidden xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <h2 class="text-base font-bold text-slate-950">Pemesanan Terbaru</h2>
                @if ($canViewBookings)
                    <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-700">Lihat Semua <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
                @endif
            </div>
            <table class="w-full table-fixed">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="w-[16%] px-4 py-2.5 text-left text-[0.68rem] font-bold uppercase text-slate-500">Kode</th>
                        <th class="w-[28%] px-3 py-2.5 text-left text-[0.68rem] font-bold uppercase text-slate-500">Pelanggan</th>
                        <th class="w-[10%] px-3 py-2.5 text-left text-[0.68rem] font-bold uppercase text-slate-500">Aset</th>
                        <th class="hidden w-[16%] px-3 py-2.5 text-left text-[0.68rem] font-bold uppercase text-slate-500 xl:table-cell">Ambil</th>
                        <th class="w-[15%] px-3 py-2.5 text-left text-[0.68rem] font-bold uppercase text-slate-500">Status</th>
                        <th class="w-[31%] px-4 py-2.5 text-right text-[0.68rem] font-bold uppercase text-slate-500 xl:w-[15%]">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookingTerbaru->take(5) as $booking)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-2.5 align-middle text-xs font-bold text-blue-700"><span class="block truncate">{{ $booking->booking_code }}</span></td>
                            <td class="px-3 py-2.5 align-middle text-xs text-slate-700"><span class="block truncate font-semibold text-slate-950">{{ $booking->customer->name }}</span></td>
                            <td class="px-3 py-2.5 align-middle text-xs text-slate-600">{{ $booking->items->count() }} item</td>
                            <td class="hidden px-3 py-2.5 align-middle text-xs text-slate-600 xl:table-cell">{{ $booking->pickup_at->translatedFormat('d M Y') }}</td>
                            <td class="px-3 py-2.5 align-middle"><x-status-badge :status="$booking->status" /></td>
                            <td class="px-4 py-2.5 text-right align-middle text-xs font-bold text-slate-950">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada pemesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="flex items-center justify-between border-t border-slate-100 px-4 py-3 text-xs text-slate-500">
                <span>Menampilkan {{ $bookingTerbaru->count() }} dari {{ number_format($dashboardCounts['recent_bookings_total'], 0, ',', '.') }} pemesanan</span>
                @if ($canViewBookings)
                    <a href="{{ route('bookings.index') }}" class="font-bold text-blue-700">Buka daftar pemesanan</a>
                @endif
            </div>
        </div>

        <div class="sr-card overflow-hidden p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-950">Aset Terlaris</h2>
                @if ($canViewAssets)
                    <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-700">Lihat Semua <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
                @endif
            </div>
            <div class="mt-3 divide-y divide-slate-100">
                @forelse ($topAssets->take(5) as $asset)
                    <div class="flex items-center gap-3 py-2.5">
                        <img src="{{ $asset->display_image_url }}" alt="{{ $asset->name }}" class="h-11 w-14 shrink-0 rounded-lg bg-slate-50 object-cover ring-1 ring-slate-200">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-950">{{ $asset->name }}</p>
                            <p class="text-xs text-slate-500">{{ $asset->category->name ?? '-' }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-bold text-slate-950">{{ $asset->total_rented }}</p>
                            <p class="text-[0.68rem] text-emerald-600">rental</p>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-slate-500">Belum ada data aset.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="sr-card p-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-slate-950">Peringatan Operasional</h2>
            @if ($canViewMaintenance)
                <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-700">Lihat Semua <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
            @endif
        </div>
        <div class="mt-3 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ $canViewBookings ? route('bookings.index', ['status' => 'overdue']) : '#' }}" class="flex items-center gap-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
                <i data-lucide="alert-triangle" class="h-7 w-7 shrink-0"></i>
                <span class="min-w-0">
                    <span class="block font-bold">{{ $dashboardCounts['overdue_returns'] }} Pengembalian Terlambat</span>
                    <span class="block truncate text-xs text-rose-600">Perlu dihubungi hari ini.</span>
                </span>
                <i data-lucide="chevron-right" class="ml-auto h-5 w-5 shrink-0"></i>
            </a>
            <a href="{{ $canViewMaintenance ? route('maintenance.index') : '#' }}" class="flex items-center gap-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-700">
                <i data-lucide="alert-triangle" class="h-7 w-7 shrink-0"></i>
                <span class="min-w-0">
                    <span class="block font-bold">{{ $dashboardCounts['active_maintenance'] }} Perawatan Aktif</span>
                    <span class="block truncate text-xs text-amber-600">{{ $maintenanceMendesak->first()?->asset->name ?? 'Perlu tindak lanjut teknisi' }}</span>
                </span>
                <i data-lucide="chevron-right" class="ml-auto h-5 w-5 shrink-0"></i>
            </a>
            <a href="{{ $canViewInventory ? route('inventory.index') : '#' }}" class="flex items-center gap-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-700">
                <i data-lucide="info" class="h-7 w-7 shrink-0"></i>
                <span class="min-w-0">
                    <span class="block font-bold">{{ $dashboardCounts['low_stock'] }} Stok Rendah</span>
                    <span class="block truncate text-xs text-blue-600">{{ $lowStockItems->pluck('name')->take(3)->implode(', ') ?: 'Inventori aman' }}</span>
                </span>
                <i data-lucide="chevron-right" class="ml-auto h-5 w-5 shrink-0"></i>
            </a>
            <a href="{{ $canViewBookings ? route('bookings.index') : '#' }}" class="flex items-center gap-4 rounded-xl border border-violet-200 bg-violet-50 p-4 text-violet-700">
                <i data-lucide="calendar-days" class="h-7 w-7 shrink-0"></i>
                <span class="min-w-0">
                    <span class="block font-bold">{{ $dashboardCounts['upcoming_pickups'] }} Pengambilan Mendatang</span>
                    <span class="block truncate text-xs text-violet-600">Jadwal operasional terdekat.</span>
                </span>
                <i data-lucide="chevron-right" class="ml-auto h-5 w-5 shrink-0"></i>
            </a>
        </div>
    </section>
</div>
@endsection

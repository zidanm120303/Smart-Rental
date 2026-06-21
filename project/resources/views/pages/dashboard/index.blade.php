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
@endphp

<div class="space-y-5">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Total Aset" :value="number_format($stats['total_aset'], 0, ',', '.')" trend="Tersebar di semua kategori" icon="camera" tone="blue" />
        <x-stat-card title="Pemesanan Aktif" :value="number_format($stats['booking_aktif'], 0, ',', '.')" trend="Sedang disewa atau disiapkan" icon="calendar-check" tone="emerald" />
        <x-stat-card title="Pendapatan Bulan Ini" :value="'Rp ' . number_format($stats['pendapatan_bulan_ini'], 0, ',', '.')" trend="Dari pembayaran tercatat" icon="badge-dollar-sign" tone="amber" />
        <x-stat-card title="Aset Perawatan" :value="number_format($stats['aset_maintenance'], 0, ',', '.')" trend="Perlu perhatian teknisi" icon="wrench" tone="rose" />
    </section>

    <section class="grid gap-5 xl:grid-cols-12">
        <div class="sr-card p-6 xl:col-span-5 xl:min-h-[34rem]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-bold text-slate-950">Tren Pendapatan</h1>
                    <p class="mt-1 text-sm text-slate-500">Pendapatan bulanan tahun berjalan.</p>
                </div>
                <span class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600">Tahun Ini</span>
            </div>
            <div
                data-chart="revenue"
                data-labels='@json($revenueTrend->pluck('label'))'
                data-values='@json($revenueTrend->pluck('value'))'
                class="mt-4 min-h-[390px]"
            ></div>
        </div>

        <div class="sr-card p-6 xl:col-span-3 xl:min-h-[34rem]">
            <h2 class="text-lg font-bold text-slate-950">Status Pemesanan</h2>
            <p class="mt-1 text-sm text-slate-500">Komposisi status operasional.</p>
            <div
                data-chart="booking-status"
                data-labels='@json($statusChartLabels)'
                data-values='@json($statusChartValues)'
                class="mt-4 min-h-[330px]"
            ></div>
        </div>

        <div class="sr-card flex flex-col p-6 xl:col-span-2 xl:min-h-[34rem]">
            <h2 class="text-lg font-bold text-slate-950">Utilisasi Aset</h2>
            <div data-chart="utilization" data-value="{{ $stats['utilisasi'] }}" class="mt-8"></div>
            <div class="mt-auto rounded-2xl bg-blue-50 px-4 py-3 text-center text-sm font-semibold text-blue-700">
                {{ $stats['utilisasi'] }}% aset terpakai atau dipesan
            </div>
        </div>

        <div class="sr-card p-6 xl:col-span-2 xl:min-h-[34rem]">
            <h2 class="text-lg font-bold text-slate-950">Aksi Cepat</h2>
            <div class="mt-4 space-y-3">
                <a href="{{ route('bookings.create') }}" class="sr-button-secondary w-full justify-start"><i data-lucide="calendar-plus" class="h-4 w-4 text-blue-600"></i> Pemesanan Baru</a>
                <a href="{{ route('assets.index') }}" class="sr-button-secondary w-full justify-start"><i data-lucide="camera" class="h-4 w-4 text-blue-600"></i> Tambah Aset</a>
                <a href="{{ route('bookings.create') }}" class="sr-button-secondary w-full justify-start"><i data-lucide="search-check" class="h-4 w-4 text-violet-600"></i> Cek Ketersediaan</a>
                <a href="{{ route('invoices.index') }}" class="sr-button-secondary w-full justify-start"><i data-lucide="file-plus-2" class="h-4 w-4 text-emerald-600"></i> Buat Tagihan</a>
                <a href="{{ route('maintenance.index') }}" class="sr-button-secondary w-full justify-start"><i data-lucide="wrench" class="h-4 w-4 text-amber-600"></i> Ajukan Perawatan</a>
            </div>
        </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-12">
        <div class="sr-card overflow-hidden xl:col-span-6">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-950">Pemesanan Terbaru</h2>
                <a href="{{ route('bookings.index') }}" class="text-sm font-bold text-blue-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="sr-table min-w-[68rem]">
                    <thead class="bg-slate-50">
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Aset</th>
                            <th>Pengambilan</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookingTerbaru as $booking)
                            <tr>
                                <td><span class="font-bold text-blue-700">{{ $booking->booking_code }}</span></td>
                                <td><span class="block max-w-[16rem] truncate">{{ $booking->customer->name }}</span></td>
                                <td>{{ $booking->items->count() }} item</td>
                                <td>{{ $booking->pickup_at->translatedFormat('d M Y') }}</td>
                                <td><x-status-badge :status="$booking->status" /></td>
                                <td class="font-bold text-slate-950">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sr-card p-5 xl:col-span-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-950">Aset Paling Sering Disewa</h2>
                <a href="{{ route('assets.index') }}" class="text-sm font-bold text-blue-700">Detail</a>
            </div>
            <div class="mt-4 space-y-4">
                @foreach ($topAssets as $asset)
                    <div class="flex items-center gap-3">
                        <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="h-12 w-16 rounded-xl object-cover ring-1 ring-slate-200">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-950">{{ $asset->name }}</p>
                            <p class="text-xs text-slate-500">{{ $asset->category->name ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-950">{{ $asset->total_rented }}</p>
                            <p class="text-xs text-emerald-600">rental</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="sr-card p-5 xl:col-span-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-950">Kalender Mini</h2>
                <a href="{{ route('calendar.index') }}" class="text-sm font-bold text-blue-700">Buka Kalender</a>
            </div>
            <div class="mt-4 grid grid-cols-7 gap-1 text-center text-xs font-semibold text-slate-400">
                @foreach (['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
                    <span>{{ $day }}</span>
                @endforeach
            </div>
            <div class="mt-2 grid grid-cols-7 gap-1 text-center text-sm font-semibold text-slate-700">
                @foreach (range(1, 35) as $day)
                    <span class="{{ $day === 20 ? 'rounded-xl bg-blue-600 py-2 text-white' : 'py-2' }}">{{ $day <= 30 ? $day : $day - 30 }}</span>
                @endforeach
            </div>
            <div class="mt-4 flex items-center gap-4 text-xs font-semibold text-slate-500">
                <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-blue-600"></span> Pemesanan</span>
                <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Perawatan</span>
            </div>
        </div>
    </section>

    <section class="sr-card p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Peringatan Operasional</h2>
                <p class="text-sm text-slate-500">Prioritas yang perlu ditindaklanjuti hari ini.</p>
            </div>
            <a href="{{ route('maintenance.index') }}" class="text-sm font-bold text-blue-700">Lihat Semua Peringatan</a>
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
                <p class="font-bold">{{ $bookingTerbaru->where('status', 'overdue')->count() ?: 2 }} Pengembalian Terlambat</p>
                <p class="mt-1 text-sm">Segera hubungi pelanggan terkait.</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-700">
                <p class="font-bold">{{ $maintenanceMendesak->count() }} Perawatan Aktif</p>
                <p class="mt-1 text-sm">Perlu tindak lanjut teknisi.</p>
            </div>
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-blue-700">
                <p class="font-bold">{{ $lowStockItems->count() }} Stok Rendah</p>
                <p class="mt-1 text-sm">Cek baterai, kabel, dan barang habis pakai.</p>
            </div>
            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4 text-violet-700">
                <p class="font-bold">12 Pengambilan Mendatang</p>
                <p class="mt-1 text-sm">Jadwal 7 hari ke depan.</p>
            </div>
        </div>
    </section>
</div>
@endsection

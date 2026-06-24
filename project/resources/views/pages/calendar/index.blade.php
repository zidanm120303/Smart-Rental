@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_repeat(4,13rem)]">
        <div class="sr-card flex items-center gap-4 p-5">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-700"><i data-lucide="calendar-days" class="h-7 w-7"></i></div>
            <div>
                <h1 class="text-2xl font-bold text-slate-950">Kalender Operasional</h1>
                <p class="mt-1 text-sm text-slate-500">Pantau pengambilan, pengembalian, perawatan, transportasi, dan staf.</p>
            </div>
        </div>
        <x-stat-card title="Pemesanan" :value="number_format($calendarStats['bookings'], 0, ',', '.')" trend="Bulan berjalan" icon="calendar-check" tone="blue" />
        <x-stat-card title="Utilisasi" :value="$calendarStats['utilization'] . '%'" trend="Aset disewa/dipesan" icon="gauge" tone="emerald" />
        <x-stat-card title="Pendapatan" :value="'Rp ' . number_format($calendarStats['revenue'], 0, ',', '.')" trend="Pembayaran bulan ini" icon="badge-dollar-sign" tone="amber" />
        <x-stat-card title="Perawatan" :value="number_format($calendarStats['maintenance'], 0, ',', '.')" trend="Perintah kerja aktif" icon="wrench" tone="rose" />
    </div>

    <div class="grid gap-5 xl:grid-cols-[14rem_minmax(0,1fr)_18rem] 2xl:grid-cols-[16rem_minmax(0,1fr)_18rem]">
        <aside class="sr-card h-fit p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-950">Filter</h2>
                <button class="text-xs font-bold text-blue-700">Reset</button>
            </div>
            <div class="mt-4 space-y-5 text-sm">
                <div>
                    <p class="mb-2 font-bold text-slate-700">Tipe Acara</p>
                    @foreach (['Pemesanan', 'Pengambilan', 'Pengembalian', 'Perawatan', 'Transportasi', 'Penugasan Staf'] as $event)
                        <label class="flex items-center justify-between border-b border-slate-100 py-2">
                            <span>{{ $event }}</span>
                            <input type="checkbox" checked class="rounded border-slate-300 text-blue-600">
                        </label>
                    @endforeach
                </div>
                <label class="block">
                    <span class="font-bold text-slate-700">Kategori Aset</span>
                    <select class="sr-input mt-2 w-full">
                        <option>Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="font-bold text-slate-700">Lokasi</span>
                    <select class="sr-input mt-2 w-full">
                        <option>Semua Lokasi</option>
                        @foreach ($locations as $location)
                            <option>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid grid-cols-3 rounded-2xl border border-slate-200 bg-slate-50 p-1 text-xs font-bold">
                    <button class="rounded-xl bg-blue-600 py-2 text-white">Bulan</button>
                    <button class="py-2 text-slate-500">Minggu</button>
                    <button class="py-2 text-slate-500">Hari</button>
                </div>
            </div>
        </aside>

        <section class="sr-card min-w-0 overflow-hidden p-4">
            <div id="operational-calendar" data-events-url="{{ route('calendar.events') }}"></div>
        </section>

        <aside class="space-y-5">
            <div class="sr-card p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-slate-950">Agenda Mendatang</h2>
                    <a href="{{ route('bookings.index') }}" class="text-xs font-bold text-blue-700">Lihat semua</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($upcomingAgenda as $agenda)
                        <div class="rounded-2xl border border-slate-200 p-3">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-2.5 w-2.5 rounded-full {{ $agenda['color'] === 'rose' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-950">{{ $agenda['title'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $agenda['subtitle'] }} &middot; {{ $agenda['starts_at']->translatedFormat('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-200 p-4 text-sm text-slate-500">Belum ada agenda mendatang.</p>
                    @endforelse
                </div>
            </div>

            <div class="sr-card p-5">
                <h2 class="font-bold text-slate-950">Ringkasan Utilisasi</h2>
                <div class="mt-4 flex items-center gap-4">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full border-[10px] border-blue-600 text-xl font-bold text-slate-950">{{ $calendarStats['utilization'] }}%</div>
                    <div class="space-y-2 text-sm">
                        <p class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-blue-600"></span> Terpakai {{ number_format($calendarStats['used_assets'], 0, ',', '.') }} aset</p>
                        <p class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-slate-300"></span> Tersedia {{ number_format($calendarStats['available_assets'], 0, ',', '.') }} aset</p>
                        <p class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-rose-500"></span> Perawatan {{ number_format($calendarStats['maintenance_assets'], 0, ',', '.') }} aset</p>
                    </div>
                </div>
            </div>

            <div class="sr-card p-5">
                <h2 class="font-bold text-slate-950">Staf Bertugas</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($staffOnDuty as $staff)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">{{ strtoupper(substr($staff->name, 0, 1)) }}</div>
                                <div>
                                    <p class="text-sm font-bold text-slate-950">{{ $staff->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $staff->role_label }}</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Bertugas</span>
                        </div>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-200 p-4 text-sm text-slate-500">Belum ada staf aktif.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

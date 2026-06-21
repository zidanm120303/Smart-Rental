@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-950">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">Ringkasan performa rental dan operasional hari ini.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Total Aset" :value="$stats['total_aset']" trend="Seluruh kategori" />
        <x-stat-card title="Booking Aktif" :value="$stats['booking_aktif']" trend="Sedang berjalan" />
        <x-stat-card title="Pendapatan Bulan Ini" :value="'Rp ' . number_format($stats['pendapatan_bulan_ini'], 0, ',', '.')" trend="Dari pembayaran" />
        <x-stat-card title="Aset Maintenance" :value="$stats['aset_maintenance']" trend="Perlu perhatian" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="font-semibold text-slate-950">Tren Pendapatan</h2>
            <canvas id="revenueChart" class="mt-4 h-72"></canvas>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-slate-950">Status Booking</h2>
            <canvas id="bookingStatusChart" class="mt-4 h-72"></canvas>
        </div>
    </div>
</div>
@endsection

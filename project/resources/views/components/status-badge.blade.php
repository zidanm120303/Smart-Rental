@props(['status', 'label' => null])

@php
    $classes = match ($status) {
        'available', 'excellent', 'verified', 'paid', 'completed', 'selesai' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'approved', 'sent', 'active', 'good' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'pending', 'reserved', 'draft', 'medium', 'waiting_parts' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'maintenance', 'in_progress', 'partially_paid' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'rented' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'overdue', 'cancelled', 'damaged', 'high', 'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'low', 'fair' => 'bg-slate-50 text-slate-700 ring-slate-200',
        default => 'bg-slate-50 text-slate-700 ring-slate-200',
    };

    $fallback = [
        'available' => 'Tersedia',
        'rented' => 'Disewa',
        'reserved' => 'Dipesan',
        'maintenance' => 'Perawatan',
        'excellent' => 'Sangat Baik',
        'good' => 'Baik',
        'fair' => 'Cukup',
        'damaged' => 'Rusak',
        'verified' => 'Terverifikasi',
        'pending' => 'Menunggu',
        'draft' => 'Draf',
        'approved' => 'Disetujui',
        'active' => 'Aktif',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'overdue' => 'Terlambat',
        'sent' => 'Terkirim',
        'paid' => 'Lunas',
        'partially_paid' => 'Dibayar Sebagian',
        'in_progress' => 'Diproses',
        'waiting_parts' => 'Menunggu Suku Cadang',
        'high' => 'Tinggi',
        'medium' => 'Sedang',
        'low' => 'Rendah',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold ring-1 ' . $classes]) }}>
    {{ $label ?? ($fallback[$status] ?? $status) }}
</span>

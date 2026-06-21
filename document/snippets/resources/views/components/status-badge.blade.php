@props(['status'])
@php
$map = [
    'available' => ['Tersedia', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
    'rented' => ['Disewa', 'bg-blue-50 text-blue-700 ring-blue-200'],
    'reserved' => ['Dipesan', 'bg-amber-50 text-amber-700 ring-amber-200'],
    'maintenance' => ['Maintenance', 'bg-violet-50 text-violet-700 ring-violet-200'],
    'retired' => ['Tidak Aktif', 'bg-slate-100 text-slate-600 ring-slate-200'],
    'pending' => ['Menunggu Persetujuan', 'bg-amber-50 text-amber-700 ring-amber-200'],
    'approved' => ['Disetujui', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
    'active' => ['Sedang Berjalan', 'bg-blue-50 text-blue-700 ring-blue-200'],
    'completed' => ['Selesai', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
    'cancelled' => ['Dibatalkan', 'bg-rose-50 text-rose-700 ring-rose-200'],
    'overdue' => ['Terlambat', 'bg-rose-50 text-rose-700 ring-rose-200'],
];
[$label, $class] = $map[$status] ?? [$status, 'bg-slate-100 text-slate-600 ring-slate-200'];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $class }}">{{ $label }}</span>

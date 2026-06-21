@extends('layouts.app')

@section('content')
@php($requestCollection = $requests->getCollection())
<div class="space-y-5">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Perawatan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola perintah kerja, inspeksi, daftar cek, suku cadang, dan kesehatan aset.</p>
        </div>
        <div class="flex gap-2">
            <button class="sr-button-secondary"><i data-lucide="filter" class="h-4 w-4"></i> Filter</button>
            <button type="button" class="sr-button-primary" onclick="document.getElementById('create-maintenance-modal').showModal()"><i data-lucide="plus" class="h-4 w-4"></i> Perintah Kerja Baru</button>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Tugas Perawatan Terbuka" :value="$requestCollection->where('status', '!=', 'completed')->count()" trend="Naik dari bulan lalu" icon="wrench" tone="blue" />
        <x-stat-card title="Inspeksi Jatuh Tempo" value="11" trend="Dalam 7 hari" icon="calendar-clock" tone="amber" />
        <x-stat-card title="Aset Tidak Tersedia" value="9" trend="Sedang perawatan" icon="alert-circle" tone="rose" />
        <x-stat-card title="Biaya Perawatan" :value="'Rp ' . number_format($requestCollection->sum('estimated_cost'), 0, ',', '.')" trend="Estimasi bulan ini" icon="badge-dollar-sign" tone="emerald" />
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach (['new' => 'Baru', 'in_progress' => 'Diproses', 'waiting_parts' => 'Menunggu Suku Cadang', 'completed' => 'Selesai'] as $status => $label)
            <a href="{{ route('maintenance.index', ['status' => $status]) }}" class="sr-card block p-5 hover:border-blue-300">
                <div class="flex items-center justify-between">
                    <x-status-badge :status="$status" />
                    <span class="text-2xl font-bold text-slate-950">{{ $requestCollection->where('status', $status)->count() }}</span>
                </div>
                <p class="mt-4 text-sm text-slate-500">Klik untuk melihat perintah kerja berstatus {{ strtolower($label) }}.</p>
            </a>
        @endforeach
    </section>

    <div class="grid gap-5 2xl:grid-cols-[minmax(0,1fr)_32rem]">
        <section class="sr-card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-bold text-slate-950">Perintah Kerja Perawatan</h2>
                    <p class="text-sm text-slate-500">Menampilkan {{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }} dari {{ $requests->total() }} tugas</p>
                </div>
                <i data-lucide="more-vertical" class="h-5 w-5 text-slate-400"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="sr-table min-w-[82rem]">
                    <thead class="bg-slate-50">
                        <tr>
                            <th>WO</th>
                            <th>Aset</th>
                            <th>Jenis Masalah</th>
                            <th>Prioritas</th>
                            <th>Teknisi</th>
                            <th>Jadwal</th>
                            <th>Progres</th>
                            <th>Estimasi Biaya</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                            <tr class="{{ optional($selectedRequest)->id === $request->id ? 'bg-blue-50/70' : '' }}">
                                <td><a href="{{ route('maintenance.index', array_merge(request()->except('page'), ['maintenance_id' => $request->id])) }}" class="font-bold text-blue-700">{{ $request->work_order_code }}</a></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $request->asset->image_url }}" alt="{{ $request->asset->name }}" class="h-12 w-16 rounded-xl object-cover ring-1 ring-slate-200">
                                        <div class="min-w-0">
                                            <p class="max-w-[18rem] truncate font-semibold text-slate-950">{{ $request->asset->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $request->asset->asset_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $request->issue_type }}</td>
                                <td><x-status-badge :status="$request->priority" /></td>
                                <td>{{ $request->technician->name ?? '-' }}</td>
                                <td>{{ optional($request->scheduled_at)->translatedFormat('d M Y') ?? '-' }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-24 rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full bg-blue-600" style="width: {{ $request->progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold">{{ $request->progress }}%</span>
                                    </div>
                                </td>
                                <td class="font-bold">Rp {{ number_format($request->estimated_cost, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$request->status" /></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="document.getElementById('edit-maintenance-{{ $request->id }}').showModal()" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Edit perintah kerja"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                                        <form method="POST" action="{{ route('maintenance.destroy', $request) }}" onsubmit="return confirm('Hapus perintah kerja {{ $request->work_order_code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Hapus perintah kerja"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4">{{ $requests->links() }}</div>
        </section>

        <aside class="sr-card h-fit p-5 2xl:sticky 2xl:top-24">
            @if ($selectedRequest)
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-blue-700">{{ $selectedRequest->work_order_code }}</p>
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ $selectedRequest->asset->image_url }}" alt="{{ $selectedRequest->asset->name }}" class="h-20 w-24 rounded-2xl object-cover ring-1 ring-slate-200">
                            <div class="min-w-0">
                                <h2 class="font-bold text-slate-950">{{ $selectedRequest->asset->name }}</h2>
                                <p class="text-sm text-slate-500">{{ $selectedRequest->asset->asset_code }}</p>
                            </div>
                        </div>
                    </div>
                    <x-status-badge :status="$selectedRequest->status" />
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-rose-50 p-4 text-rose-700">
                        <p class="text-xs font-bold uppercase">Prioritas</p>
                        <p class="mt-1 font-bold">{{ $selectedRequest->priority_label }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase text-slate-500">Estimasi Biaya</p>
                        <p class="mt-1 font-bold text-slate-950">Rp {{ number_format($selectedRequest->estimated_cost, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="mt-5 border-b border-slate-200">
                    <nav class="flex gap-5 text-sm font-bold">
                        <span class="border-b-2 border-blue-600 pb-3 text-blue-700">Detail</span>
                        <span class="pb-3 text-slate-500">Log Servis</span>
                        <span class="pb-3 text-slate-500">Inspeksi</span>
                        <span class="pb-3 text-slate-500">Lampiran</span>
                    </nav>
                </div>

                <section class="mt-5">
                    <h3 class="font-bold text-slate-950">Deskripsi Masalah</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $selectedRequest->issue_description }}</p>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Teknisi</p>
                            <p class="mt-1 font-bold text-slate-950">{{ $selectedRequest->technician->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Lokasi</p>
                            <p class="mt-1 font-bold text-slate-950">{{ $selectedRequest->asset->location->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Jadwal</p>
                            <p class="mt-1 font-bold text-slate-950">{{ optional($selectedRequest->scheduled_at)->translatedFormat('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Estimasi Waktu</p>
                            <p class="mt-1 font-bold text-slate-950">1.5 jam</p>
                        </div>
                    </div>
                </section>

                <section class="mt-5">
                    <h3 class="font-bold text-slate-950">Daftar Cek Inspeksi</h3>
                    <div class="mt-3 space-y-2">
                        @foreach ($selectedRequest->checklists as $checklist)
                            <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-3 py-2 text-sm">
                                <span class="flex items-center gap-3">
                                    <input type="checkbox" class="rounded border-slate-300 text-blue-600" @checked($checklist->is_checked)>
                                    <span>{{ $checklist->label }}</span>
                                </span>
                                <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700">Stok Ada</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="mt-5">
                    <h3 class="font-bold text-slate-950">Log Servis</h3>
                    <div class="mt-3 space-y-4 border-l border-slate-200 pl-4 text-sm">
                        <div>
                            <p class="font-bold text-slate-950">Perintah kerja dibuat</p>
                            <p class="text-xs text-slate-500">12 Mei 2026 09:15 oleh Admin Operasional</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-950">Aset dicek dan kondisi diverifikasi</p>
                            <p class="text-xs text-slate-500">12 Mei 2026 09:32 oleh Teknisi</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-950">Diagnosa awal selesai</p>
                            <p class="text-xs text-slate-500">12 Mei 2026 10:05 oleh Teknisi</p>
                        </div>
                    </div>
                </section>

                <div class="mt-6 grid grid-cols-3 gap-2">
                    <button class="sr-button-secondary px-2"><i data-lucide="shopping-cart" class="h-4 w-4"></i> Suku Cadang</button>
                    <button type="button" onclick="document.getElementById('edit-maintenance-{{ $selectedRequest->id }}')?.showModal()" class="sr-button-secondary px-2">Edit</button>
                    <button type="button" onclick="document.getElementById('edit-maintenance-{{ $selectedRequest->id }}')?.showModal()" class="sr-button-primary px-2">Selesaikan</button>
                </div>
            @else
                <div class="py-12 text-center text-sm text-slate-500">Belum ada perintah kerja.</div>
            @endif
        </aside>
    </div>
</div>

<x-modal-form id="create-maintenance-modal" title="Perintah Kerja Baru" description="Catat masalah aset dan jadwalkan teknisi." size="4xl">
    @include('pages.maintenance._form', ['action' => route('maintenance.store'), 'method' => 'POST', 'assets' => $assets, 'technicians' => $technicians])
</x-modal-form>

@foreach ($requests as $request)
    <x-modal-form id="edit-maintenance-{{ $request->id }}" title="Edit Perintah Kerja" description="{{ $request->work_order_code }} - {{ $request->asset->name }}" size="4xl">
        @include('pages.maintenance._form', ['requestItem' => $request, 'action' => route('maintenance.update', $request), 'method' => 'PUT', 'assets' => $assets, 'technicians' => $technicians])
    </x-modal-form>
@endforeach
@endsection

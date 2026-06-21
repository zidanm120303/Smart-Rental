@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Inventori</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola barang habis pakai, suku cadang, stok minimum, dan mutasi gudang.</p>
        </div>
        <div class="flex gap-2">
            @if ($items->count())
                <button type="button" class="sr-button-secondary" onclick="document.getElementById('move-inventory-{{ $items->first()->id }}').showModal()"><i data-lucide="arrow-left-right" class="h-4 w-4"></i> Mutasi Stok</button>
            @endif
            <button type="button" class="sr-button-primary" onclick="document.getElementById('create-inventory-modal').showModal()"><i data-lucide="plus" class="h-4 w-4"></i> Item Baru</button>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Total Item" :value="$items->total()" trend="Barang habis pakai dan suku cadang" icon="package" tone="blue" />
        <x-stat-card title="Stok Rendah" :value="$items->getCollection()->filter->is_low_stock->count()" trend="Perlu pembelian" icon="alert-triangle" tone="rose" />
        <x-stat-card title="Kategori" :value="$categoryCount" trend="Baterai, kabel, media" icon="tags" tone="amber" />
        <x-stat-card title="Nilai Stok" :value="'Rp ' . number_format($items->getCollection()->sum(fn ($item) => $item->stock * $item->unit_cost), 0, ',', '.')" trend="Halaman ini" icon="badge-dollar-sign" tone="emerald" />
    </section>

    <div class="sr-card overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 md:flex-row md:items-center md:justify-between">
            <form method="GET" class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input name="search" value="{{ request('search') }}" class="sr-input w-full pl-10" placeholder="Cari SKU atau nama item...">
            </form>
            <button class="sr-button-secondary"><i data-lucide="filter" class="h-4 w-4"></i> Filter</button>
        </div>
        <div class="overflow-x-auto">
            <table class="sr-table min-w-[72rem]">
                <thead class="bg-slate-50">
                    <tr>
                        <th>SKU</th>
                        <th>Nama Item</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Stok</th>
                        <th>Minimum</th>
                        <th>Harga Satuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="font-bold text-blue-700">{{ $item->sku }}</td>
                            <td class="font-semibold text-slate-950">{{ $item->name }}</td>
                            <td>{{ $item->category }}</td>
                            <td>{{ $item->location->name ?? '-' }}</td>
                            <td>{{ $item->stock }} {{ $item->unit }}</td>
                            <td>{{ $item->minimum_stock }} {{ $item->unit }}</td>
                            <td>Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                            <td>
                                @if ($item->is_low_stock)
                                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Stok Rendah</span>
                                @else
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">Stok Aman</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="document.getElementById('move-inventory-{{ $item->id }}').showModal()" class="rounded-lg p-2 text-violet-600 hover:bg-violet-50" title="Mutasi stok"><i data-lucide="arrow-left-right" class="h-4 w-4"></i></button>
                                    <button type="button" onclick="document.getElementById('edit-inventory-{{ $item->id }}').showModal()" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Edit item"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                                    <form method="POST" action="{{ route('inventory.destroy', $item) }}" onsubmit="return confirm('Hapus item {{ $item->sku }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Hapus item"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $items->links() }}</div>
    </div>
</div>

<x-modal-form id="create-inventory-modal" title="Item Inventori Baru" description="Tambahkan barang habis pakai, suku cadang, atau media." size="3xl">
    @include('pages.inventory._form', ['action' => route('inventory.store'), 'method' => 'POST', 'locations' => $locations, 'categories' => $categories])
</x-modal-form>

@foreach ($items as $item)
    <x-modal-form id="edit-inventory-{{ $item->id }}" title="Edit Item Inventori" description="{{ $item->sku }} - {{ $item->name }}" size="3xl">
        @include('pages.inventory._form', ['item' => $item, 'action' => route('inventory.update', $item), 'method' => 'PUT', 'locations' => $locations, 'categories' => $categories])
    </x-modal-form>

    <x-modal-form id="move-inventory-{{ $item->id }}" title="Mutasi Stok" description="Catat stok masuk, keluar, atau penyesuaian." size="2xl">
        @include('pages.inventory._movement_form', ['item' => $item])
    </x-modal-form>
@endforeach
@endsection

@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Manajemen Aset</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola inventaris peralatan, ketersediaan, kondisi, dan siklus perawatan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="sr-button-secondary"><i data-lucide="upload" class="h-4 w-4"></i> Impor</button>
            <button class="sr-button-secondary"><i data-lucide="download" class="h-4 w-4"></i> Ekspor</button>
            <button type="button" class="sr-button-primary" onclick="document.getElementById('create-asset-modal').showModal()"><i data-lucide="plus" class="h-4 w-4"></i> Tambah Aset</button>
        </div>
    </div>

    <form method="GET" class="sr-card p-4">
        <input type="hidden" name="view" value="{{ $viewMode }}">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
            <label class="relative xl:col-span-2">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input name="search" value="{{ request('search') }}" class="sr-input w-full pl-10" placeholder="Cari aset, kode, atau serial...">
            </label>
            <select name="category_id" class="sr-input">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="sr-input">
                <option value="">Semua Status</option>
                <option value="available" @selected(request('status') === 'available')>Tersedia</option>
                <option value="rented" @selected(request('status') === 'rented')>Disewa</option>
                <option value="reserved" @selected(request('status') === 'reserved')>Dipesan</option>
                <option value="maintenance" @selected(request('status') === 'maintenance')>Perawatan</option>
            </select>
            <select name="location_id" class="sr-input">
                <option value="">Semua Lokasi</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="sr-button-secondary flex-1"><i data-lucide="filter" class="h-4 w-4"></i> Filter</button>
                <a href="{{ route('assets.index', ['view' => $viewMode]) }}" class="sr-button-secondary px-3" aria-label="Reset filter"><i data-lucide="x" class="h-4 w-4"></i></a>
            </div>
        </div>
    </form>

    <div class="grid gap-5 2xl:grid-cols-[minmax(0,1fr)_31rem]">
        <section class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="text-sm font-semibold text-slate-500">Menampilkan {{ $assets->firstItem() ?? 0 }} sampai {{ $assets->lastItem() ?? 0 }} dari {{ $assets->total() }} aset</div>
                <div class="inline-flex rounded-2xl border border-slate-200 bg-white p-1 shadow-sm">
                    <a href="{{ route('assets.index', array_merge(request()->except('page'), ['view' => 'table'])) }}" class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-bold {{ $viewMode === 'table' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}"><i data-lucide="table-2" class="h-4 w-4"></i> Tabel</a>
                    <a href="{{ route('assets.index', array_merge(request()->except('page'), ['view' => 'grid'])) }}" class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-bold {{ $viewMode === 'grid' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}"><i data-lucide="layout-grid" class="h-4 w-4"></i> Grid</a>
                </div>
            </div>

            @if ($viewMode === 'grid')
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($assets as $asset)
                        <a href="{{ route('assets.index', array_merge(request()->except('page'), ['asset_id' => $asset->id, 'view' => 'grid'])) }}" class="sr-card block p-4 transition hover:border-blue-300 hover:shadow-md {{ optional($selectedAsset)->id === $asset->id ? 'ring-2 ring-blue-500' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked(optional($selectedAsset)->id === $asset->id)>
                                <i data-lucide="star" class="h-5 w-5 text-slate-400"></i>
                            </div>
                            <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="mx-auto mt-2 h-36 w-full rounded-2xl object-cover ring-1 ring-slate-200">
                            <div class="mt-3">
                                <h2 class="line-clamp-1 text-base font-bold text-slate-950">{{ $asset->name }}</h2>
                                <p class="mt-1 text-sm text-slate-500">{{ $asset->category->name ?? '-' }}</p>
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <x-status-badge :status="$asset->availability_status" />
                                    <span class="text-xs font-semibold text-slate-500">{{ $asset->location->name ?? '-' }} &middot; {{ $asset->shelf_position }}</span>
                                </div>
                                <p class="mt-3 text-sm font-bold text-blue-700">Rp {{ number_format($asset->daily_rate, 0, ',', '.') }} / hari</p>
                                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-slate-500">
                                    <span class="inline-flex items-center gap-1 text-xs"><i data-lucide="activity" class="h-3.5 w-3.5"></i> {{ $asset->utilization_rate }}%</span>
                                    <span class="inline-flex items-center gap-1 text-xs"><i data-lucide="calendar" class="h-3.5 w-3.5"></i> {{ $asset->total_rented }} rental</span>
                                    <span class="inline-flex items-center gap-1 text-xs"><i data-lucide="more-horizontal" class="h-3.5 w-3.5"></i></span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sr-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="sr-table min-w-[86rem]">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th><input type="checkbox" class="rounded border-slate-300 text-blue-600"></th>
                                    <th>Kode Aset</th>
                                    <th>Nama Aset</th>
                                    <th>Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Kondisi</th>
                                    <th>Tarif Harian</th>
                                    <th>Perawatan Terakhir</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $asset)
                                    <tr class="{{ optional($selectedAsset)->id === $asset->id ? 'bg-blue-50/70' : 'bg-white' }}">
                                        <td><input type="checkbox" class="rounded border-slate-300 text-blue-600" @checked(optional($selectedAsset)->id === $asset->id)></td>
                                        <td><a href="{{ route('assets.index', array_merge(request()->except('page'), ['asset_id' => $asset->id, 'view' => 'table'])) }}" class="font-bold text-blue-700">{{ $asset->asset_code }}</a></td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $asset->image_url }}" alt="{{ $asset->name }}" class="h-12 w-16 rounded-xl object-cover ring-1 ring-slate-200">
                                                <span class="block max-w-[18rem] truncate font-semibold text-slate-950">{{ $asset->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $asset->category->name ?? '-' }}</td>
                                        <td>{{ $asset->location->name ?? '-' }}</td>
                                        <td><x-status-badge :status="$asset->availability_status" /></td>
                                        <td><x-status-badge :status="$asset->condition_status" /></td>
                                        <td class="font-semibold">Rp {{ number_format($asset->daily_rate, 0, ',', '.') }}</td>
                                        <td>{{ optional($asset->last_maintenance_at)->translatedFormat('d M Y') ?? '-' }}</td>
                                        <td>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" onclick="document.getElementById('edit-asset-{{ $asset->id }}').showModal()" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Edit aset"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                                                <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset {{ $asset->asset_code }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Hapus aset"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{ $assets->links() }}
        </section>

        <aside class="sr-card h-fit p-5 2xl:sticky 2xl:top-24">
            @if ($selectedAsset)
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-blue-700">{{ $selectedAsset->asset_code }}</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-950">{{ $selectedAsset->name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $selectedAsset->category->name ?? '-' }} &middot; Serial: {{ $selectedAsset->serial_number }}</p>
                    </div>
                    <x-status-badge :status="$selectedAsset->availability_status" />
                </div>

                <img src="{{ $selectedAsset->image_url }}" alt="{{ $selectedAsset->name }}" class="mt-5 h-56 w-full rounded-2xl bg-slate-50 object-cover ring-1 ring-slate-200">

                <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-2xl bg-slate-50 p-3">
                        <p class="text-lg font-bold text-slate-950">{{ $selectedAsset->utilization_rate }}%</p>
                        <p class="text-xs text-slate-500">Utilisasi</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-3">
                        <p class="text-lg font-bold text-slate-950">{{ $selectedAsset->total_rented }}</p>
                        <p class="text-xs text-slate-500">Pemesanan</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-3">
                        <p class="text-lg font-bold text-slate-950">Rp {{ number_format($selectedAsset->daily_rate / 1000, 0, ',', '.') }}rb</p>
                        <p class="text-xs text-slate-500">Per Hari</p>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-950">Spesifikasi</h3>
                    <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
                        @foreach ($selectedAsset->specifications as $spec)
                            <div>
                                <dt class="text-xs font-semibold text-slate-500">{{ $spec->name }}</dt>
                                <dd class="mt-1 font-semibold text-slate-800">{{ $spec->value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-950">Riwayat Pemesanan</h3>
                        <a href="{{ route('bookings.index') }}" class="text-xs font-bold text-blue-700">Lihat Semua</a>
                    </div>
                    <div class="mt-3 space-y-3">
                        @forelse ($selectedAsset->bookingItems->take(3) as $item)
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $item->booking->customer->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ optional($item->booking->pickup_at)->translatedFormat('d M') }} - {{ optional($item->booking->return_at)->translatedFormat('d M Y') }}</p>
                                </div>
                                <x-status-badge :status="$item->booking->status ?? 'draft'" />
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada riwayat pemesanan.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-5">
                    <h3 class="font-bold text-slate-950">QR / Barcode</h3>
                    <div class="mt-3 flex items-center gap-4 rounded-2xl bg-slate-50 p-4">
                        <div class="grid h-20 w-20 grid-cols-4 gap-1 rounded-xl bg-white p-2 ring-1 ring-slate-200">
                            @foreach (range(1, 16) as $i)
                                <span class="{{ in_array($i, [1,2,5,6,10,11,15,16]) ? 'bg-slate-950' : 'bg-slate-200' }}"></span>
                            @endforeach
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-950">{{ $selectedAsset->barcode }}</p>
                            <p class="mt-1 text-xs text-slate-500">Kode identifikasi aset untuk pengambilan dan pengembalian.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-2">
                    <button type="button" onclick="document.getElementById('edit-asset-{{ $selectedAsset->id }}')?.showModal()" class="sr-button-primary w-full"><i data-lucide="pencil" class="h-4 w-4"></i> Edit Aset</button>
                    <a href="{{ route('bookings.create') }}" class="sr-button-secondary w-full"><i data-lucide="search-check" class="h-4 w-4"></i> Cek Ketersediaan</a>
                    <a href="{{ route('bookings.create') }}" class="sr-button-secondary w-full"><i data-lucide="calendar-plus" class="h-4 w-4"></i> Buat Pemesanan</a>
                    <a href="{{ route('maintenance.index') }}" class="sr-button-secondary w-full"><i data-lucide="wrench" class="h-4 w-4"></i> Jadwalkan Perawatan</a>
                </div>
            @else
                <div class="py-12 text-center text-sm text-slate-500">Belum ada aset yang tersedia.</div>
            @endif
        </aside>
    </div>
</div>

<x-modal-form id="create-asset-modal" title="Tambah Aset" description="Lengkapi data inventaris rental baru." size="4xl">
    @include('pages.assets._form', ['action' => route('assets.store'), 'method' => 'POST'])
</x-modal-form>

@foreach ($assets as $asset)
    <x-modal-form id="edit-asset-{{ $asset->id }}" title="Edit Aset" description="{{ $asset->asset_code }} - {{ $asset->name }}" size="4xl">
        @include('pages.assets._form', ['asset' => $asset, 'action' => route('assets.update', $asset), 'method' => 'PUT'])
    </x-modal-form>
@endforeach
@endsection

@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Lokasi</h1>
            <p class="mt-1 text-sm text-slate-500">Gudang, studio, rak, dan titik pengambilan aset rental.</p>
        </div>
        <button type="button" class="sr-button-primary" onclick="document.getElementById('create-location-modal').showModal()"><i data-lucide="map-pin-plus" class="h-4 w-4"></i> Tambah Lokasi</button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($locations as $location)
            <section class="sr-card p-5">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700"><i data-lucide="warehouse" class="h-6 w-6"></i></div>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Aktif</span>
                </div>
                <h2 class="mt-4 text-lg font-bold text-slate-950">{{ $location->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $location->code }} &middot; {{ ucfirst($location->type) }}</p>
                <p class="mt-3 text-sm text-slate-600">{{ $location->address }}, {{ $location->city }}</p>
                <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-500">Jumlah Aset</p>
                    <p class="mt-1 text-2xl font-bold text-slate-950">{{ $location->assets_count }}</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="button" onclick="document.getElementById('edit-location-{{ $location->id }}').showModal()" class="sr-button-secondary flex-1"><i data-lucide="pencil" class="h-4 w-4"></i> Edit</button>
                    <form method="POST" action="{{ route('locations.destroy', $location) }}" onsubmit="return confirm('Hapus lokasi {{ $location->name }}?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button class="sr-button-secondary w-full text-rose-600"><i data-lucide="trash-2" class="h-4 w-4"></i> Hapus</button>
                    </form>
                </div>
            </section>
        @endforeach
    </div>
</div>

<x-modal-form id="create-location-modal" title="Tambah Lokasi" description="Tambahkan gudang, studio, rak, atau titik pengambilan." size="2xl">
    @include('pages.locations._form', ['action' => route('locations.store'), 'method' => 'POST'])
</x-modal-form>

@foreach ($locations as $locationItem)
    <x-modal-form id="edit-location-{{ $locationItem->id }}" title="Edit Lokasi" description="{{ $locationItem->code }} - {{ $locationItem->name }}" size="2xl">
        @include('pages.locations._form', ['locationItem' => $locationItem, 'action' => route('locations.update', $locationItem), 'method' => 'PUT'])
    </x-modal-form>
@endforeach
@endsection

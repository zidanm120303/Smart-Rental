@extends('layouts.app')

@section('content')
@php($customerCollection = $customers->getCollection())
<div class="space-y-5">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Pelanggan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola relasi pelanggan, pemesanan, dokumen, tagihan, dan catatan akun.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="sr-button-secondary"><i data-lucide="upload-cloud" class="h-4 w-4"></i> Impor Pelanggan</button>
            <button type="button" class="sr-button-primary" onclick="document.getElementById('create-customer-modal').showModal()"><i data-lucide="plus" class="h-4 w-4"></i> Tambah Pelanggan</button>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Total Pelanggan" :value="number_format($customers->total(), 0, ',', '.')" trend="Seluruh data pelanggan" icon="users" tone="blue" />
        <x-stat-card title="Penyewa Aktif" :value="number_format($customerCollection->where('total_bookings', '>', 0)->count(), 0, ',', '.')" trend="Pernah memesan" icon="user-check" tone="emerald" />
        <x-stat-card title="Pelanggan VIP" :value="number_format($customerCollection->where('customer_level', 'vip')->count(), 0, ',', '.')" trend="Nilai tinggi" icon="crown" tone="amber" />
        <x-stat-card title="Akun Menunggu" :value="number_format($customerCollection->where('verification_status', 'pending')->count(), 0, ',', '.')" trend="Butuh verifikasi" icon="alert-triangle" tone="rose" />
    </section>

    <div class="grid gap-5 2xl:grid-cols-[minmax(0,1fr)_32rem]">
        <section class="space-y-5">
            <form method="GET" class="sr-card p-4">
                <div class="grid gap-3 md:grid-cols-4">
                    <label class="relative md:col-span-2">
                        <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                        <input name="search" value="{{ request('search') }}" class="sr-input w-full pl-10" placeholder="Cari nama, email, atau perusahaan...">
                    </label>
                    <select name="tag" class="sr-input">
                        <option value="">Semua Tag</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}" @selected(request('tag') === $tag)>{{ $tag }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <select name="status" class="sr-input flex-1">
                            <option value="">Semua Status</option>
                            <option value="verified" @selected(request('status') === 'verified')>Terverifikasi</option>
                            <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
                        </select>
                        <button class="sr-button-secondary px-3" aria-label="Filter pelanggan"><i data-lucide="filter" class="h-4 w-4"></i></button>
                    </div>
                </div>
            </form>

            <div class="sr-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="sr-table min-w-[78rem]">
                        <thead class="bg-slate-50">
                            <tr>
                                <th><input type="checkbox" class="rounded border-slate-300 text-blue-600"></th>
                                <th>Pelanggan</th>
                                <th>Kontak</th>
                                <th>Pemesanan Terbaru</th>
                                <th>Total Nilai Rental</th>
                                <th>Verifikasi</th>
                                <th>Tag</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr class="{{ optional($selectedCustomer)->id === $customer->id ? 'bg-blue-50/70' : 'bg-white' }}">
                                    <td><input type="checkbox" class="rounded border-slate-300 text-blue-600" @checked(optional($selectedCustomer)->id === $customer->id)></td>
                                    <td>
                                        <a href="{{ route('customers.index', array_merge(request()->except('page'), ['customer_id' => $customer->id])) }}" class="flex items-center gap-3">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                            <span class="min-w-0">
                                                <span class="block max-w-[16rem] truncate font-bold text-slate-950">{{ $customer->name }}</span>
                                                <span class="text-xs text-slate-500">{{ $customer->contact_person }}</span>
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <p class="max-w-[15rem] truncate">{{ $customer->email }}</p>
                                        <p class="text-xs text-slate-500">{{ $customer->phone }}</p>
                                    </td>
                                    <td>{{ $customer->total_bookings }} pemesanan</td>
                                    <td class="font-bold text-slate-950">Rp {{ number_format($customer->lifetime_value, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$customer->verification_status" /></td>
                                    <td><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">{{ $customer->tag }}</span></td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="document.getElementById('edit-customer-{{ $customer->id }}').showModal()" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Edit pelanggan"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Hapus pelanggan {{ $customer->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Hapus pelanggan"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $customers->links() }}
        </section>

        <aside class="sr-card h-fit p-5 2xl:sticky 2xl:top-24">
            @if ($selectedCustomer)
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-xl font-bold text-white">
                            {{ strtoupper(substr($selectedCustomer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-bold text-slate-950">{{ $selectedCustomer->name }}</h2>
                                @if ($selectedCustomer->customer_level === 'vip')
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">VIP</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $selectedCustomer->contact_person }} &middot; {{ $selectedCustomer->tag }}</p>
                        </div>
                    </div>
                    <i data-lucide="x" class="h-5 w-5 text-slate-400"></i>
                </div>

                <div class="mt-5 grid gap-2 sm:grid-cols-2">
                    <a href="{{ route('bookings.create') }}" class="sr-button-primary min-h-11"><i data-lucide="calendar-plus" class="h-4 w-4"></i> Pemesanan Baru</a>
                    <button type="button" onclick="document.getElementById('edit-customer-{{ $selectedCustomer->id }}')?.showModal()" class="sr-button-secondary min-h-11"><i data-lucide="pencil" class="h-4 w-4"></i> Edit Pelanggan</button>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-slate-500">Total Belanja</p>
                        <p class="mt-1 text-lg font-bold text-slate-950">Rp {{ number_format($selectedCustomer->lifetime_value, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-slate-500">Total Pemesanan</p>
                        <p class="mt-1 text-lg font-bold text-slate-950">{{ $selectedCustomer->total_bookings }}</p>
                    </div>
                </div>

                <div class="mt-5 border-b border-slate-200">
                    <nav class="flex gap-5 text-sm font-bold">
                        <span class="border-b-2 border-blue-600 pb-3 text-blue-700">Ringkasan</span>
                        <span class="pb-3 text-slate-500">Pemesanan</span>
                        <span class="pb-3 text-slate-500">Tagihan</span>
                        <span class="pb-3 text-slate-500">Catatan</span>
                    </nav>
                </div>

                <div class="mt-5 grid gap-5 lg:grid-cols-2 2xl:grid-cols-1">
                    <section>
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-950">Riwayat Rental</h3>
                            <a href="{{ route('bookings.index') }}" class="text-xs font-bold text-blue-700">Lihat Semua</a>
                        </div>
                        <div class="mt-3 space-y-3">
                            @forelse ($selectedCustomer->bookings->take(3) as $booking)
                                <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 p-3">
                                    <div>
                                        <p class="text-sm font-bold text-slate-950">{{ $booking->items->first()?->asset?->name ?? $booking->booking_code }}</p>
                                        <p class="text-xs text-slate-500">{{ $booking->pickup_at->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-slate-950">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada riwayat rental.</p>
                            @endforelse
                        </div>
                    </section>

                    <section>
                        <h3 class="font-bold text-slate-950">Kategori Favorit</h3>
                        <div class="mt-3 space-y-2">
                            @foreach (['Kamera' => 18, 'Lighting' => 15, 'Lensa' => 12, 'Audio' => 6] as $category => $count)
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-3 py-2 text-sm">
                                    <span class="font-semibold text-slate-700">{{ $category }}</span>
                                    <span class="font-bold text-slate-950">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                <div class="mt-5 grid gap-5 lg:grid-cols-2 2xl:grid-cols-1">
                    <section class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-950">Dokumen Verifikasi</h3>
                            <x-status-badge :status="$selectedCustomer->verification_status" />
                        </div>
                        <div class="mt-3 space-y-2 text-sm">
                            <p class="flex items-center justify-between"><span>Izin Usaha</span><span class="font-bold text-emerald-600">Terverifikasi</span></p>
                            <p class="flex items-center justify-between"><span>Identitas PIC</span><span class="font-bold text-emerald-600">Terverifikasi</span></p>
                            <p class="flex items-center justify-between"><span>Profil Billing</span><span class="font-bold text-emerald-600">Lengkap</span></p>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 p-4">
                        <h3 class="font-bold text-slate-950">Profil Tagihan</h3>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p class="flex justify-between gap-3"><span>Termin Pembayaran</span><strong class="text-slate-950">Net 15</strong></p>
                            <p class="flex justify-between gap-3"><span>Metode Utama</span><strong class="text-slate-950">Transfer Bank</strong></p>
                            <p class="flex justify-between gap-3"><span>Alamat</span><strong class="text-right text-slate-950">{{ $selectedCustomer->city }}</strong></p>
                        </div>
                    </section>
                </div>

                <section class="mt-5 rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-950">Catatan</h3>
                        <button class="text-xs font-bold text-blue-700">Tambah Catatan</button>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $selectedCustomer->notes }}</p>
                </section>
            @else
                <div class="py-12 text-center text-sm text-slate-500">Belum ada pelanggan.</div>
            @endif
        </aside>
    </div>
</div>

<x-modal-form id="create-customer-modal" title="Tambah Pelanggan" description="Masukkan data pelanggan baru." size="4xl">
    @include('pages.customers._form', ['action' => route('customers.store'), 'method' => 'POST'])
</x-modal-form>

@foreach ($customers as $customer)
    <x-modal-form id="edit-customer-{{ $customer->id }}" title="Edit Pelanggan" description="{{ $customer->customer_code }} - {{ $customer->name }}" size="4xl">
        @include('pages.customers._form', ['customer' => $customer, 'action' => route('customers.update', $customer), 'method' => 'PUT'])
    </x-modal-form>
@endforeach
@endsection

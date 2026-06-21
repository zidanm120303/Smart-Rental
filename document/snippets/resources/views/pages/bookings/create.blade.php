@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('bookings.store') }}" class="space-y-6">
    @csrf
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm text-slate-500">Booking › Booking Baru</p>
            <h1 class="text-2xl font-bold text-slate-950">Booking Baru</h1>
            <p class="mt-1 text-sm text-slate-500">Buat booking peralatan rental dalam beberapa langkah.</p>
        </div>
        <div class="flex gap-2">
            <button name="status" value="draft" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700">Simpan Draft</button>
            <button name="status" value="pending" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white">Ajukan Booking</button>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-950">1. Customer</h2>
                <select name="customer_id" class="mt-4 w-full rounded-xl border-slate-200 text-sm" required>
                    <option value="">Pilih customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} — {{ $customer->phone }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-950">2. Pilih Aset Rental</h2>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach ($assets as $asset)
                        <label class="flex cursor-pointer gap-3 rounded-xl border border-slate-200 p-3 hover:border-blue-300">
                            <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="mt-1 rounded border-slate-300 text-blue-600">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $asset->name }}</p>
                                <p class="text-sm text-slate-500">{{ $asset->category->name ?? '-' }} • {{ $asset->location->name ?? '-' }}</p>
                                <p class="mt-1 text-sm font-semibold text-blue-600">Rp {{ number_format($asset->daily_rate, 0, ',', '.') }}/hari</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-950">3. Jadwal Pickup dan Return</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <input type="datetime-local" name="pickup_at" class="rounded-xl border-slate-200 text-sm" required>
                    <input type="datetime-local" name="return_at" class="rounded-xl border-slate-200 text-sm" required>
                </div>
            </div>
        </div>
        <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-slate-950">Ringkasan Booking</h2>
            <select name="delivery_method" class="mt-4 w-full rounded-xl border-slate-200 text-sm">
                <option value="pickup">Ambil Sendiri</option>
                <option value="delivery">Dikirim</option>
            </select>
            <textarea name="notes" rows="5" class="mt-4 w-full rounded-xl border-slate-200 text-sm" placeholder="Catatan khusus..."></textarea>
        </aside>
    </div>
</form>
@endsection

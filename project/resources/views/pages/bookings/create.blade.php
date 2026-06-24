@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('bookings.store') }}" data-booking-form class="space-y-5">
    @csrf

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-500">Pemesanan &gt; Pemesanan Baru</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Pemesanan Baru</h1>
            <p class="mt-1 text-sm text-slate-500">Buat reservasi peralatan dalam beberapa langkah.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button name="status" value="draft" class="sr-button-secondary"><i data-lucide="save" class="h-4 w-4"></i> Simpan Draf</button>
            <button name="status" value="pending" class="sr-button-primary">Ajukan Pemesanan <i data-lucide="arrow-right" class="h-4 w-4"></i></button>
        </div>
    </div>

    <div class="sr-card p-4">
        <div class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
            @foreach (['Pelanggan', 'Pilih Aset', 'Jadwal', 'Pengiriman', 'Harga', 'Tinjauan'] as $index => $step)
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $index < 3 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500' }} text-sm font-bold">{{ $index + 1 }}</span>
                    <span class="text-sm font-bold {{ $index < 3 ? 'text-blue-700' : 'text-slate-500' }}">{{ $step }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_24rem]">
        <section class="space-y-5">
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="sr-card p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-950">Pelanggan</h2>
                        <a href="{{ route('customers.index') }}" class="text-sm font-bold text-blue-700">Kelola</a>
                    </div>
                    <label class="mt-4 block">
                        <span class="text-sm font-bold text-slate-700">Pilih Pelanggan</span>
                        <select name="customer_id" class="sr-input mt-2 w-full" required>
                            <option value="">Pilih pelanggan...</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }} - {{ $customer->contact_person }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="mt-4 rounded-2xl bg-blue-50 p-4 text-sm text-blue-700">
                        <p class="font-bold">Pastikan pelanggan sudah terverifikasi.</p>
                        <p class="mt-1">Dokumen identitas atau legalitas perusahaan wajib lengkap sebelum pengambilan.</p>
                    </div>
                </div>

                <div class="sr-card p-5">
                    <h2 class="text-lg font-bold text-slate-950">Referensi Pemesanan</h2>
                    <div class="mt-4 rounded-2xl border border-slate-200 p-4">
                        <p class="text-sm font-semibold text-slate-500">Kode otomatis</p>
                        <p class="mt-1 text-2xl font-bold text-slate-950">{{ $bookingCode }}</p>
                    </div>
                    <label class="mt-4 block">
                        <span class="text-sm font-bold text-slate-700">Catatan Operasional</span>
                        <textarea name="notes" rows="4" class="sr-input mt-2 w-full" placeholder="Contoh: Siapkan baterai penuh, kartu memori kosong, dan semua item sudah dites.">{{ old('notes') }}</textarea>
                    </label>
                </div>
            </div>

            <div class="sr-card p-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-950">Pilih Aset Rental</h2>
                        <p class="mt-1 text-sm text-slate-500">Pilih item yang akan diblokir pada jadwal pemesanan.</p>
                    </div>
                    <span data-summary-count class="rounded-full bg-blue-50 px-3 py-1.5 text-sm font-bold text-blue-700">0 aset</span>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach ($assets as $asset)
                        <label class="flex cursor-pointer gap-3 rounded-2xl border border-slate-200 p-3 transition hover:border-blue-300 hover:bg-blue-50/40">
                            <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" data-asset-checkbox data-rate="{{ $asset->daily_rate }}" class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked(in_array($asset->id, old('asset_ids', [])))>
                            <img src="{{ $asset->display_image_url }}" alt="{{ $asset->name }}" class="h-16 w-20 rounded-xl object-contain ring-1 ring-slate-200">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold text-slate-950">{{ $asset->name }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $asset->category->name ?? '-' }} &middot; {{ $asset->location->name ?? '-' }}</span>
                                <span class="mt-2 inline-flex items-center gap-2 text-sm font-bold text-blue-700">Rp {{ number_format($asset->daily_rate, 0, ',', '.') }} / hari</span>
                            </span>
                            <x-status-badge :status="$asset->availability_status" />
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <div class="sr-card p-5">
                    <h2 class="text-lg font-bold text-slate-950">Jadwal Pengambilan & Pengembalian</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label>
                            <span class="text-sm font-bold text-slate-700">Jadwal Pengambilan</span>
                            <input name="pickup_at" value="{{ old('pickup_at', now()->addDays(2)->format('Y-m-d H:i')) }}" data-datepicker class="sr-input mt-2 w-full" placeholder="Pilih tanggal dan jam pengambilan" required>
                        </label>
                        <label>
                            <span class="text-sm font-bold text-slate-700">Jadwal Kembali</span>
                            <input name="return_at" value="{{ old('return_at', now()->addDays(5)->format('Y-m-d H:i')) }}" data-datepicker class="sr-input mt-2 w-full" placeholder="Pilih tanggal dan jam kembali" required>
                        </label>
                    </div>
                    <div class="mt-4 rounded-2xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
                        Sistem akan mengecek bentrok jadwal sebelum data disimpan.
                    </div>
                </div>

                <div class="sr-card p-5">
                    <h2 class="text-lg font-bold text-slate-950">Pengiriman</h2>
                    <div class="mt-4 grid gap-3">
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3">
                            <input type="radio" name="delivery_method" value="pickup" class="text-blue-600 focus:ring-blue-500" @checked(old('delivery_method', 'pickup') === 'pickup')>
                            <span>
                                <span class="block font-bold text-slate-950">Ambil di Gudang</span>
                                <span class="text-sm text-slate-500">Pelanggan mengambil aset di lokasi pengambilan.</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3">
                            <input type="radio" name="delivery_method" value="delivery" class="text-blue-600 focus:ring-blue-500" @checked(old('delivery_method') === 'delivery')>
                            <span>
                                <span class="block font-bold text-slate-950">Dikirim ke Lokasi</span>
                                <span class="text-sm text-slate-500">Tim operasional mengantar aset.</span>
                            </span>
                        </label>
                        <input name="delivery_address" value="{{ old('delivery_address') }}" class="sr-input" placeholder="Alamat pengiriman jika memilih dikirim">
                    </div>
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                <div class="sr-card p-5">
                    <h2 class="font-bold text-slate-950">Asuransi & Perlindungan</h2>
                    <label class="mt-4 block">
                        <span class="text-sm font-bold text-slate-700">Biaya Asuransi</span>
                        <input name="insurance_amount" type="number" value="{{ old('insurance_amount', 0) }}" class="sr-input mt-2 w-full" placeholder="Contoh: 125000">
                    </label>
                </div>
                <div class="sr-card p-5">
                    <h2 class="font-bold text-slate-950">Diskon</h2>
                    <label class="mt-4 block">
                        <span class="text-sm font-bold text-slate-700">Nominal Diskon</span>
                        <input name="discount_amount" type="number" value="{{ old('discount_amount', 0) }}" class="sr-input mt-2 w-full" placeholder="Contoh: 100000">
                    </label>
                </div>
                <div class="sr-card p-5">
                    <h2 class="font-bold text-slate-950">Biaya Pengiriman</h2>
                    <label class="mt-4 block">
                        <span class="text-sm font-bold text-slate-700">Nominal Pengiriman</span>
                        <input name="delivery_fee" type="number" value="{{ old('delivery_fee', 0) }}" class="sr-input mt-2 w-full" placeholder="Contoh: 150000">
                    </label>
                </div>
            </div>
        </section>

        <aside class="sr-card h-fit p-5 xl:sticky xl:top-24">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-950">Ringkasan Pemesanan</h2>
                <span class="text-sm font-bold text-slate-500">Langkah 6 dari 6</span>
            </div>
            <div class="mt-4 h-2 rounded-full bg-slate-100">
                <div class="h-2 w-full rounded-full bg-blue-600"></div>
            </div>

            <div class="mt-6 space-y-4 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">Subtotal aset</span>
                    <span data-summary-subtotal class="font-bold text-slate-950">Rp 0</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">Pajak dihitung server</span>
                    <span class="font-bold text-slate-950">11%</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">Deposit konfirmasi</span>
                    <span class="font-bold text-slate-950">30%</span>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <div class="flex justify-between gap-4">
                        <span class="text-base font-bold text-slate-950">Estimasi Total</span>
                        <span data-summary-total class="text-xl font-bold text-blue-700">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                <p class="font-bold">Validasi akhir dilakukan oleh server.</p>
                <p class="mt-1">Aset dikunci dengan transaksi database untuk mencegah pemesanan ganda.</p>
            </div>
        </aside>
    </div>
</form>
@endsection

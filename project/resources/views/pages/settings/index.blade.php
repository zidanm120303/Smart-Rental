@extends('layouts.app')

@section('content')
@php
    $company = $settings->get('company', collect())->keyBy('key');
    $rental = $settings->get('rental', collect())->keyBy('key');
    $finance = $settings->get('finance', collect())->keyBy('key');
@endphp
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-slate-950">Pengaturan</h1>
        <p class="mt-1 text-sm text-slate-500">Beranda &gt; Pengaturan &gt; Profil Perusahaan dan Aturan Bisnis</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" class="grid gap-5 xl:grid-cols-[18rem_minmax(0,1fr)]">
        @csrf
        @method('PUT')
        <aside class="sr-card h-fit p-4">
            <h2 class="px-2 text-lg font-bold text-slate-950">Pengaturan</h2>
            <div class="mt-4 space-y-1">
                @foreach ([
                    ['Profil Perusahaan', 'Alamat, kontak, dan logo', 'building-2'],
                    ['Aturan Rental', 'Harga, durasi, dan deposit', 'clipboard-list'],
                    ['Kebijakan Pemesanan', 'Pembatalan dan biaya', 'calendar-check'],
                    ['Pajak & Mata Uang', 'Pajak, mata uang, penagihan', 'badge-dollar-sign'],
                    ['Metode Pembayaran', 'Gerbang bayar dan pencairan', 'credit-card'],
                    ['Notifikasi', 'Email dan preferensi SMS', 'bell'],
                    ['Peran Pengguna', 'Pengguna dan hak akses', 'users'],
                    ['Kategori Aset', 'Tipe, merek, atribut', 'tags'],
                    ['Lokasi', 'Gudang dan titik pengambilan', 'map-pin'],
                    ['Integrasi', 'Aplikasi pihak ketiga', 'blocks'],
                    ['Tampilan Aplikasi', 'Identitas visual dan tampilan', 'palette'],
                ] as $index => $item)
                    <button type="button" class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left {{ $index === 0 ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i data-lucide="{{ $item[2] }}" class="h-5 w-5"></i>
                        <span>
                            <span class="block text-sm font-bold">{{ $item[0] }}</span>
                            <span class="text-xs">{{ $item[1] }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>

        <section class="sr-card p-5">
            <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-950">Profil Perusahaan & Bisnis</h2>
                    <p class="mt-1 text-sm text-slate-500">Kelola profil perusahaan, kontak, dan informasi bisnis rental.</p>
                </div>
                <div class="flex gap-2">
                    <button class="sr-button-primary"><i data-lucide="check" class="h-4 w-4"></i> Simpan Perubahan</button>
                    <button type="reset" class="sr-button-secondary"><i data-lucide="rotate-ccw" class="h-4 w-4"></i> Reset</button>
                </div>
            </div>

            <div class="mt-5 grid gap-5 2xl:grid-cols-[minmax(0,1fr)_28rem]">
                <div class="space-y-5">
                    <section class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="font-bold text-slate-950">Profil Perusahaan</h3>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <label>
                                <span class="text-sm font-bold text-slate-700">Nama Perusahaan</span>
                                <input name="company[name]" value="{{ $company->get('name')->value ?? 'Smart Rental Pro' }}" class="sr-input mt-2 w-full" placeholder="Nama perusahaan">
                            </label>
                            <label>
                                <span class="text-sm font-bold text-slate-700">Nama Legal Bisnis</span>
                                <input name="company[legal_name]" value="{{ $company->get('legal_name')->value ?? '' }}" class="sr-input mt-2 w-full" placeholder="Nama legal bisnis">
                            </label>
                            <label>
                                <span class="text-sm font-bold text-slate-700">Industri</span>
                                <select class="sr-input mt-2 w-full"><option>Rental Peralatan</option><option>Event Production</option></select>
                            </label>
                            <label>
                                <span class="text-sm font-bold text-slate-700">Ukuran Perusahaan</span>
                                <select class="sr-input mt-2 w-full"><option>1-20 staf</option><option>21-50 staf</option><option>51-200 staf</option></select>
                            </label>
                        </div>
                        <div class="mt-5 grid gap-4 md:grid-cols-[5rem_minmax(0,1fr)]">
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-blue-600 text-white"><i data-lucide="camera" class="h-9 w-9"></i></div>
                            <div class="flex items-center justify-center rounded-2xl border border-dashed border-slate-300 p-5 text-center">
                                <div>
                                    <i data-lucide="upload-cloud" class="mx-auto h-6 w-6 text-blue-600"></i>
                                    <p class="mt-2 text-sm font-bold text-slate-700">Upload logo baru</p>
                                    <p class="text-xs text-slate-500">PNG, JPG, atau SVG maksimal 2MB</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="font-bold text-slate-950">Aturan Rental</h3>
                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            <label>
                                <span class="text-sm font-bold text-slate-700">Minimal Durasi</span>
                                <input name="rental[minimum_days]" value="{{ $rental->get('minimum_days')->value ?? 1 }}" class="sr-input mt-2 w-full" placeholder="Minimal hari rental">
                            </label>
                            <label>
                                <span class="text-sm font-bold text-slate-700">Deposit</span>
                                <input name="rental[deposit_rate]" value="{{ $rental->get('deposit_rate')->value ?? '0.30' }}" class="sr-input mt-2 w-full" placeholder="Contoh: 0.30">
                            </label>
                            <label>
                                <span class="text-sm font-bold text-slate-700">Pajak</span>
                                <input name="finance[tax_rate]" value="{{ $finance->get('tax_rate')->value ?? '0.11' }}" class="sr-input mt-2 w-full" placeholder="Contoh: 0.11">
                            </label>
                        </div>
                    </section>
                </div>

                <div class="space-y-5">
                    <section class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="font-bold text-slate-950">Kontak Utama</h3>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <label>
                                <span class="text-sm font-bold text-slate-700">Email</span>
                                <input name="company[email]" value="{{ $company->get('email')->value ?? '' }}" class="sr-input mt-2 w-full" placeholder="email@perusahaan.com">
                            </label>
                            <label>
                                <span class="text-sm font-bold text-slate-700">Telepon</span>
                                <input name="company[phone]" value="{{ $company->get('phone')->value ?? '' }}" class="sr-input mt-2 w-full" placeholder="+62 21 555 0123">
                            </label>
                            <label class="md:col-span-2">
                                <span class="text-sm font-bold text-slate-700">Alamat Bisnis</span>
                                <textarea name="company[address]" rows="3" class="sr-input mt-2 w-full" placeholder="Alamat lengkap perusahaan">{{ $company->get('address')->value ?? '' }}</textarea>
                            </label>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="font-bold text-slate-950">Peran & Kategori</h3>
                        <div class="mt-4 space-y-3">
                            @foreach ($roles as $role)
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span class="text-sm font-bold text-slate-700">{{ $role->display_name }}</span>
                                    <span class="text-sm font-bold text-slate-950">{{ $role->users_count }} pengguna</span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
            </div>

            <section class="mt-5 rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-950">Bagian Pengaturan Lainnya</h3>
                        <p class="mt-1 text-sm text-slate-500">Konfigurasi lain untuk operasional rental.</p>
                    </div>
                    <button type="button" class="text-sm font-bold text-blue-700">Lihat semua pengaturan</button>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    @foreach (['Aturan Rental', 'Kebijakan Pemesanan', 'Pajak & Mata Uang', 'Metode Pembayaran', 'Notifikasi'] as $section)
                        <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">
                            <span>{{ $section }}</span>
                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                        </div>
                    @endforeach
                </div>
            </section>
        </section>
    </form>
</div>
@endsection

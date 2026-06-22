@extends('layouts.app')

@section('content')
@php($invoiceCollection = $invoices->getCollection())
<div class="space-y-5">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Tagihan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola tagihan, status pembayaran, dan cetak PDF.</p>
        </div>
        <button type="button" class="sr-button-primary" onclick="document.getElementById('create-invoice-modal').showModal()"><i data-lucide="plus" class="h-4 w-4"></i> Tagihan Baru</button>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-card title="Total Tagihan" :value="number_format($invoices->total(), 0, ',', '.')" trend="Semua status" icon="file-text" tone="blue" />
        <x-stat-card title="Lunas" :value="'Rp ' . number_format($invoiceCollection->where('status', 'paid')->sum('paid_amount'), 0, ',', '.')" trend="Halaman ini" icon="check-circle" tone="emerald" />
        <x-stat-card title="Belum Dibayar" :value="'Rp ' . number_format($invoiceCollection->sum('total_due'), 0, ',', '.')" trend="Belum dibayar" icon="badge-dollar-sign" tone="amber" />
        <x-stat-card title="Jatuh Tempo" :value="'Rp ' . number_format($invoiceCollection->where('status', 'overdue')->sum('total_due'), 0, ',', '.')" trend="Perlu tindak lanjut" icon="alert-circle" tone="rose" />
        <x-stat-card title="Draf" :value="number_format($invoiceCollection->where('status', 'draft')->count(), 0, ',', '.')" trend="Belum dikirim" icon="file-clock" tone="slate" />
    </section>

    <div class="grid gap-5 2xl:grid-cols-[24rem_minmax(0,1fr)_22rem]">
        <section class="sr-card overflow-hidden">
            <div class="border-b border-slate-100 px-5 pt-4">
                <nav class="flex gap-5 overflow-x-auto text-sm font-bold">
                    @foreach (['' => 'Semua Tagihan', 'draft' => 'Draf', 'sent' => 'Terkirim', 'paid' => 'Lunas', 'partially_paid' => 'Dibayar Sebagian', 'overdue' => 'Jatuh Tempo'] as $status => $label)
                        <a href="{{ route('invoices.index', $status ? ['status' => $status] : []) }}" class="whitespace-nowrap border-b-2 pb-3 {{ request('status') === $status || (!request('status') && $status === '') ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500' }}">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
            <div class="flex flex-col gap-3 px-5 py-4 md:flex-row md:items-center">
                <form method="GET" class="relative flex-1">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input name="search" value="{{ request('search') }}" class="sr-input w-full pl-10" placeholder="Cari tagihan, pemesanan, atau pelanggan...">
                </form>
                <button class="sr-button-secondary"><i data-lucide="filter" class="h-4 w-4"></i> Filter</button>
                <button class="sr-button-secondary"><i data-lucide="calendar" class="h-4 w-4"></i> Tahun Ini</button>
            </div>
            <div class="overflow-x-auto">
                <table class="sr-table min-w-[74rem]">
                    <thead class="bg-slate-50">
                        <tr>
                            <th>Tagihan</th>
                            <th>Pelanggan</th>
                            <th>Referensi Pemesanan</th>
                            <th>Tanggal Terbit</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr class="{{ optional($selectedInvoice)->id === $invoice->id ? 'bg-blue-50/70' : '' }}">
                                <td><a href="{{ route('invoices.index', array_merge(request()->except('page'), ['invoice_id' => $invoice->id])) }}" class="font-bold text-blue-700">{{ $invoice->invoice_code }}</a></td>
                                <td><span class="block max-w-[15rem] truncate">{{ $invoice->customer->name }}</span></td>
                                <td>{{ $invoice->booking->booking_code }}</td>
                                <td>{{ $invoice->issue_date->translatedFormat('d M Y') }}</td>
                                <td>{{ $invoice->due_date->translatedFormat('d M Y') }}</td>
                                <td><x-status-badge :status="$invoice->status" /></td>
                                <td class="font-bold text-slate-950">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="document.getElementById('edit-invoice-{{ $invoice->id }}').showModal()" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Edit tagihan"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Hapus tagihan {{ $invoice->invoice_code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Hapus tagihan"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4">{{ $invoices->links() }}</div>
        </section>

        <section class="sr-card h-fit p-5">
            @if ($selectedInvoice)
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white"><i data-lucide="camera" class="h-6 w-6"></i></div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-950">Smart Rental Pro</h2>
                            <p class="text-sm text-slate-500">Manajemen Rental Peralatan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-black text-blue-700">TAGIHAN</p>
                        <p class="text-sm font-bold text-slate-600">{{ $selectedInvoice->invoice_code }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-500">Ditagihkan Kepada</p>
                        <h3 class="mt-2 font-bold text-slate-950">{{ $selectedInvoice->customer->name }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $selectedInvoice->customer->address }}</p>
                        <p class="text-sm text-slate-600">{{ $selectedInvoice->customer->email }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm">
                        <p class="flex justify-between gap-3"><span>Tanggal Terbit</span><strong>{{ $selectedInvoice->issue_date->translatedFormat('d M Y') }}</strong></p>
                        <p class="mt-2 flex justify-between gap-3"><span>Jatuh Tempo</span><strong>{{ $selectedInvoice->due_date->translatedFormat('d M Y') }}</strong></p>
                        <p class="mt-2 flex justify-between gap-3"><span>Referensi Pemesanan</span><strong>{{ $selectedInvoice->booking->booking_code }}</strong></p>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="sr-table min-w-[44rem]">
                        <thead class="bg-slate-50">
                            <tr>
                                <th>Item</th>
                                <th>Tanggal Rental</th>
                                <th>Jumlah</th>
                                <th>Tarif</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selectedInvoice->items as $item)
                                <tr>
                                    <td><span class="block max-w-[16rem] truncate">{{ $item->description }}</span></td>
                                    <td>{{ optional($item->rental_start)->translatedFormat('d M') }} - {{ optional($item->rental_end)->translatedFormat('d M Y') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp {{ number_format($item->rate, 0, ',', '.') }}</td>
                                    <td class="font-bold">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 ml-auto max-w-sm space-y-2 text-sm">
                    <p class="flex justify-between"><span>Subtotal</span><strong>Rp {{ number_format($selectedInvoice->subtotal, 0, ',', '.') }}</strong></p>
                    <p class="flex justify-between text-emerald-700"><span>Diskon</span><strong>- Rp {{ number_format($selectedInvoice->discount_amount, 0, ',', '.') }}</strong></p>
                    <p class="flex justify-between"><span>Pajak</span><strong>Rp {{ number_format($selectedInvoice->tax_amount, 0, ',', '.') }}</strong></p>
                    <p class="flex justify-between text-emerald-700"><span>Deposit Dibayar</span><strong>- Rp {{ number_format($selectedInvoice->deposit_paid, 0, ',', '.') }}</strong></p>
                    <div class="rounded-2xl bg-blue-50 p-4">
                        <p class="flex justify-between gap-3 text-lg font-black text-blue-700"><span>Total Tagihan</span><span class="text-right">Rp {{ number_format($selectedInvoice->total_due, 0, ',', '.') }}</span></p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 p-4 text-sm text-slate-600">
                    {{ $selectedInvoice->notes }}
                </div>
            @else
                <div class="py-12 text-center text-sm text-slate-500">Belum ada tagihan.</div>
            @endif
        </section>

        <aside class="space-y-5">
            <div class="sr-card p-5">
                <h2 class="font-bold text-slate-950">Aksi Tagihan</h2>
                <div class="mt-4 space-y-3">
                    @if ($selectedInvoice)
                        <a href="{{ route('invoices.pdf', $selectedInvoice) }}" class="sr-button-secondary w-full justify-start"><i data-lucide="download" class="h-4 w-4 text-rose-600"></i> Unduh PDF</a>
                    @endif
                    <button class="sr-button-secondary w-full justify-start"><i data-lucide="printer" class="h-4 w-4 text-violet-600"></i> Cetak Tagihan</button>
                </div>
            </div>

            @if ($selectedInvoice)
                <form id="payment-card" method="POST" action="{{ route('invoices.payments.store', $selectedInvoice) }}" class="sr-card p-5">
                    @csrf
                    <h2 class="font-bold text-slate-950">Catat Pembayaran</h2>
                    <div class="mt-4 space-y-3">
                        <input name="payment_date" type="date" value="{{ now()->toDateString() }}" class="sr-input w-full" required>
                        <select name="method" class="sr-input w-full" required>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Kartu Kredit">Kartu Kredit</option>
                            <option value="Tunai">Tunai</option>
                        </select>
                        <input name="amount" type="number" class="sr-input w-full" placeholder="Nominal pembayaran" required>
                        <input name="reference_number" class="sr-input w-full" placeholder="Nomor referensi">
                        <button class="sr-button-primary w-full">Simpan Pembayaran</button>
                    </div>
                </form>
            @endif

            <div class="sr-card p-5">
                <h2 class="font-bold text-slate-950">Ringkasan Pembayaran</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <p class="flex justify-between"><span>Lunas</span><strong class="text-emerald-600">Rp {{ number_format($invoiceCollection->where('status', 'paid')->sum('paid_amount'), 0, ',', '.') }}</strong></p>
                    <p class="flex justify-between"><span>Dibayar Sebagian</span><strong class="text-violet-600">Rp {{ number_format($invoiceCollection->where('status', 'partially_paid')->sum('paid_amount'), 0, ',', '.') }}</strong></p>
                    <p class="flex justify-between"><span>Belum Dibayar</span><strong class="text-amber-600">Rp {{ number_format($invoiceCollection->sum('total_due'), 0, ',', '.') }}</strong></p>
                </div>
            </div>
        </aside>
    </div>
</div>

<x-modal-form id="create-invoice-modal" title="Tagihan Baru" description="Buat tagihan dari pemesanan yang sudah tercatat." size="2xl">
    @include('pages.invoices._form', ['action' => route('invoices.store'), 'method' => 'POST', 'bookings' => $bookings])
</x-modal-form>

@foreach ($invoices as $invoice)
    <x-modal-form id="edit-invoice-{{ $invoice->id }}" title="Edit Tagihan" description="{{ $invoice->invoice_code }} - {{ $invoice->customer->name }}" size="2xl">
        @include('pages.invoices._form', ['invoice' => $invoice, 'action' => route('invoices.update', $invoice), 'method' => 'PUT', 'bookings' => $bookings])
    </x-modal-form>
@endforeach
@endsection

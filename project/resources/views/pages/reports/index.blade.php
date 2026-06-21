@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-slate-950">Laporan</h1>
        <p class="mt-1 text-sm text-slate-500">Ringkasan pendapatan, utilisasi aset, performa pelanggan, dan tagihan.</p>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Pendapatan" :value="'Rp ' . number_format($revenue, 0, ',', '.')" trend="Total pembayaran" icon="badge-dollar-sign" tone="emerald" />
        <x-stat-card title="Pemesanan" :value="$bookings" trend="Seluruh status" icon="calendar-check" tone="blue" />
        <x-stat-card title="Pelanggan" :value="$customers" trend="Akun aktif" icon="users" tone="amber" />
        <x-stat-card title="Aset" :value="$assets" trend="Inventori rental" icon="camera" tone="violet" />
    </section>

    <div class="grid gap-5 xl:grid-cols-2">
        <section class="sr-card overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-bold text-slate-950">Tagihan Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="sr-table min-w-[42rem]">
                    <thead class="bg-slate-50"><tr><th>Tagihan</th><th>Pelanggan</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="font-bold text-blue-700">{{ $invoice->invoice_code }}</td>
                                <td><span class="block max-w-[16rem] truncate">{{ $invoice->customer->name }}</span></td>
                                <td><x-status-badge :status="$invoice->status" /></td>
                                <td class="font-bold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sr-card p-5">
            <h2 class="font-bold text-slate-950">Utilisasi Aset Teratas</h2>
            <div class="mt-4 space-y-4">
                @foreach ($topAssets as $asset)
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="max-w-[28rem] truncate font-bold text-slate-950">{{ $asset->name }}</span>
                            <span class="font-bold text-blue-700">{{ $asset->utilization_rate }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-blue-600" style="width: {{ $asset->utilization_rate }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection

@php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $tanggal = fn ($date) => $date ? $date->translatedFormat('d F Y') : '-';
    $statusColor = match ($invoice->status) {
        'paid' => '#047857',
        'partially_paid' => '#6d28d9',
        'overdue' => '#be123c',
        'sent' => '#1d4ed8',
        default => '#475569',
    };
@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_code }}</title>
    <style>
        @page { margin: 28px; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            font-size: 11.5px;
            line-height: 1.45;
            background: #ffffff;
        }
        table { width: 100%; border-collapse: collapse; }
        .muted { color: #64748b; }
        .small { font-size: 10px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .brand-table td { border: 0; padding: 0; vertical-align: top; }
        .brand-name { margin: 0; font-size: 20px; font-weight: 800; color: #0f172a; }
        .brand-tagline { margin: 2px 0 0; font-size: 11px; color: #475569; }
        .logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #2563eb;
            position: relative;
            display: inline-block;
            margin-right: 10px;
            vertical-align: top;
        }
        .logo-body {
            position: absolute;
            left: 8px;
            top: 14px;
            width: 26px;
            height: 18px;
            border-radius: 6px;
            background: #ffffff;
        }
        .logo-top {
            position: absolute;
            left: 14px;
            top: 9px;
            width: 11px;
            height: 7px;
            border-radius: 3px;
            background: #ffffff;
        }
        .logo-lens {
            position: absolute;
            left: 15px;
            top: 18px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2563eb;
        }
        .invoice-title {
            margin: 0;
            color: #2563eb;
            font-size: 30px;
            letter-spacing: 1.5px;
            font-weight: 800;
        }
        .invoice-code { margin-top: 2px; font-size: 13px; font-weight: 700; color: #334155; }
        .divider { height: 1px; background: #e2e8f0; margin: 22px 0; }
        .card {
            border: 1px solid #dbe4f0;
            border-radius: 14px;
            padding: 16px;
            background: #ffffff;
        }
        .panel-title {
            margin: 0 0 10px;
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .customer-name { margin: 0 0 6px; font-size: 16px; font-weight: 800; color: #0f172a; }
        .meta-table td {
            border: 0;
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-label { color: #64748b; width: 42%; }
        .meta-value { font-weight: 700; color: #0f172a; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #dbeafe;
            background: #eff6ff;
            color: {{ $statusColor }};
            font-size: 10px;
            font-weight: 800;
        }
        .items th {
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            padding: 10px 9px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .items td {
            border-bottom: 1px solid #eef2f7;
            padding: 11px 9px;
            vertical-align: top;
        }
        .item-name { font-weight: 800; color: #0f172a; }
        .summary td { border: 0; padding: 5px 0; }
        .summary .label { color: #64748b; }
        .summary .value { text-align: right; font-weight: 800; }
        .summary-total {
            margin-top: 10px;
            border-radius: 12px;
            background: #eff6ff;
            padding: 13px 14px;
        }
        .summary-total table td { border: 0; padding: 0; }
        .summary-total .label { font-size: 12px; font-weight: 800; color: #1d4ed8; }
        .summary-total .value { font-size: 20px; font-weight: 800; color: #1d4ed8; text-align: right; }
        .payment-table th,
        .payment-table td {
            border-bottom: 1px solid #eef2f7;
            padding: 7px 0;
        }
        .payment-table th {
            color: #64748b;
            font-size: 10px;
            text-align: left;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            color: #64748b;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <table class="brand-table">
        <tr>
            <td>
                <span class="logo">
                    <span class="logo-body"></span>
                    <span class="logo-top"></span>
                    <span class="logo-lens"></span>
                </span>
                <span style="display:inline-block;">
                    <h1 class="brand-name">Smart Rental Pro</h1>
                    <p class="brand-tagline">Manajemen Rental Peralatan</p>
                    <p class="brand-tagline">Jl. Kemang Raya No. 21, Jakarta Selatan</p>
                </span>
            </td>
            <td class="right">
                <h2 class="invoice-title">TAGIHAN</h2>
                <div class="invoice-code">{{ $invoice->invoice_code }}</div>
                <div style="margin-top:8px;"><span class="badge">{{ $invoice->status_label }}</span></div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td style="width: 58%; padding-right: 12px; vertical-align: top;">
                <div class="card">
                    <p class="panel-title">Ditagihkan Kepada</p>
                    <h3 class="customer-name">{{ $invoice->customer->name }}</h3>
                    <div class="muted">
                        {{ $invoice->customer->address ?: 'Alamat pelanggan belum diisi.' }}<br>
                        {{ $invoice->customer->email ?: '-' }}<br>
                        {{ $invoice->customer->phone ?: '-' }}
                    </div>
                </div>
            </td>
            <td style="width: 42%; padding-left: 12px; vertical-align: top;">
                <div class="card">
                    <p class="panel-title">Informasi Tagihan</p>
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Tanggal Terbit</td>
                            <td class="meta-value right">{{ $tanggal($invoice->issue_date) }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Jatuh Tempo</td>
                            <td class="meta-value right">{{ $tanggal($invoice->due_date) }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Referensi Pemesanan</td>
                            <td class="meta-value right">{{ $invoice->booking->booking_code ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Metode Pembayaran</td>
                            <td class="meta-value right">Transfer Bank</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div style="height: 18px;"></div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 42%;">Item</th>
                <th style="width: 23%;">Tanggal Rental</th>
                <th class="center" style="width: 10%;">Jumlah</th>
                <th class="right" style="width: 12%;">Tarif</th>
                <th class="right" style="width: 13%;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->description }}</div>
                        <div class="small muted">Peralatan rental profesional</div>
                    </td>
                    <td>
                        {{ $tanggal($item->rental_start) }}<br>
                        <span class="muted">sampai {{ $tanggal($item->rental_end) }}</span>
                    </td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ $rupiah($item->rate) }}</td>
                    <td class="right"><strong>{{ $rupiah($item->amount) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="height: 18px;"></div>

    <table>
        <tr>
            <td style="width: 54%; padding-right: 16px; vertical-align: top;">
                <div class="card">
                    <p class="panel-title">Catatan</p>
                    <p style="margin:0;" class="muted">
                        {{ $invoice->notes ?: 'Terima kasih sudah menggunakan layanan Smart Rental Pro. Mohon lakukan pembayaran sebelum tanggal jatuh tempo.' }}
                    </p>
                </div>

                <div style="height: 12px;"></div>

                <div class="card">
                    <p class="panel-title">Riwayat Pembayaran</p>
                    @if ($invoice->payments->isNotEmpty())
                        <table class="payment-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th class="right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $tanggal($payment->payment_date) }}</td>
                                        <td>{{ \Illuminate\Support\Str::headline($payment->method) }}</td>
                                        <td class="right"><strong>{{ $rupiah($payment->amount) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p style="margin:0;" class="muted">Belum ada pembayaran yang tercatat untuk tagihan ini.</p>
                    @endif
                </div>
            </td>
            <td style="width: 46%; padding-left: 16px; vertical-align: top;">
                <div class="card">
                    <p class="panel-title">Ringkasan Biaya</p>
                    <table class="summary">
                        <tr>
                            <td class="label">Subtotal</td>
                            <td class="value">{{ $rupiah($invoice->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Diskon</td>
                            <td class="value">- {{ $rupiah($invoice->discount_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Pajak</td>
                            <td class="value">{{ $rupiah($invoice->tax_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Deposit Dibayar</td>
                            <td class="value">- {{ $rupiah($invoice->deposit_paid) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Pembayaran Tercatat</td>
                            <td class="value">- {{ $rupiah($invoice->paid_amount) }}</td>
                        </tr>
                    </table>
                    <div class="summary-total">
                        <table>
                            <tr>
                                <td class="label">Total Harus Dibayar</td>
                                <td class="value">{{ $rupiah($invoice->total_due) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div style="height: 12px;"></div>

                <div class="card">
                    <p class="panel-title">Instruksi Pembayaran</p>
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Bank</td>
                            <td class="meta-value right">BCA</td>
                        </tr>
                        <tr>
                            <td class="meta-label">No. Rekening</td>
                            <td class="meta-value right">1234567890</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Atas Nama</td>
                            <td class="meta-value right">Smart Rental Pro</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh Smart Rental Pro. Simpan bukti pembayaran dan cantumkan nomor tagihan
        {{ $invoice->invoice_code }} pada berita transfer.
    </div>
</body>
</html>

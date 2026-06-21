<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #f8fafc; color: #475569; }
        .right { text-align: right; }
        .title { color: #2563eb; font-size: 28px; font-weight: 700; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td>
                <h2>Smart Rental Pro</h2>
                <p>Manajemen Rental Peralatan</p>
            </td>
            <td class="right">
                <div class="title">TAGIHAN</div>
                <strong>{{ $invoice->invoice_code }}</strong>
            </td>
        </tr>
    </table>
    <p><strong>Pelanggan:</strong> {{ $invoice->customer->name }}<br>{{ $invoice->customer->address }}</p>
    <p><strong>Tanggal:</strong> {{ $invoice->issue_date->format('d/m/Y') }} | <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
    <table>
        <thead>
            <tr><th>Item</th><th>Jumlah</th><th>Tarif</th><th class="right">Nominal</th></tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->rate, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h3 class="right">Total Tagihan: Rp {{ number_format($invoice->total_due, 0, ',', '.') }}</h3>
</body>
</html>

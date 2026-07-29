<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $customerInvoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #2563eb; }
        .info { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info td { padding: 5px; vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f8fafc; font-weight: bold; }
        .text-right { text-align: right !important; }
        .totals { width: 50%; float: right; border-collapse: collapse; }
        .totals td { padding: 5px; }
        .totals .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT Cahaya Tiga Putri Mandiri</h2>
        <h1>INVOICE / TAGIHAN</h1>
        <p>No. Faktur: <strong>{{ $customerInvoice->invoice_number }}</strong></p>
    </div>

    <table class="info">
        <tr>
            <td width="50%">
                <strong>Kepada Yth:</strong><br>
                {{ $customerInvoice->customer->name }}<br>
                {{ $customerInvoice->customer->email ?? '' }}<br>
                {{ $customerInvoice->customer->phone ?? '' }}<br>
                {{ $customerInvoice->customer->address ?? '' }}
            </td>
            <td width="50%" class="text-right">
                <strong>Tanggal Faktur:</strong> {{ \Carbon\Carbon::parse($customerInvoice->invoice_date)->format('d F Y') }}<br>
                <strong>Jatuh Tempo:</strong> {{ $customerInvoice->due_date ? \Carbon\Carbon::parse($customerInvoice->due_date)->format('d F Y') : '-' }}<br>
                <strong>Status:</strong> {{ $customerInvoice->status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Deskripsi Produk</th>
                <th class="text-right">Kuantitas</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customerInvoice->lines as $index => $line)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $line->product ? $line->product->name : $line->description }}</td>
                <td class="text-right">{{ $line->quantity }}</td>
                <td class="text-right">Rp {{ number_format($line->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($line->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format($customerInvoice->lines->sum('subtotal'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pajak ({{ $customerInvoice->tax_percentage }}%)</td>
            <td class="text-right">Rp {{ number_format($customerInvoice->tax_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="bold">TOTAL TAGIHAN</td>
            <td class="bold text-right">Rp {{ number_format($customerInvoice->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>
    <div style="clear: both;"></div>

    @if($customerInvoice->notes)
    <div style="margin-top: 30px;">
        <strong>Catatan:</strong><br>
        <p>{{ $customerInvoice->notes }}</p>
    </div>
    @endif
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $customerInvoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 13px; color: #374151; line-height: 1.5; }
        .invoice-box { max-width: 800px; margin: 0 auto; padding: 0; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 30px 40px; border-radius: 12px 12px 0 0; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .brand h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .brand p { font-size: 11px; opacity: 0.85; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; font-weight: 800; letter-spacing: 2px; }
        .invoice-title .inv-num { font-size: 12px; opacity: 0.9; margin-top: 4px; }
        .body { background: white; padding: 30px 40px; border: 1px solid #e5e7eb; border-top: none; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 30px; gap: 30px; }
        .info-block { flex: 1; }
        .info-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-bottom: 6px; font-weight: 600; }
        .info-block .name { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .info-block .detail { font-size: 12px; color: #6b7280; }
        .meta-table { width: 100%; }
        .meta-table td { padding: 4px 0; font-size: 12px; }
        .meta-table .label { color: #6b7280; }
        .meta-table .value { font-weight: 600; color: #111827; text-align: right; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-unpaid { background: #fef3c7; color: #92400e; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .table thead th { background: #f9fafb; color: #4b5563; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 10px; text-align: left; border-bottom: 2px solid #e5e7eb; }
        .table tbody td { padding: 12px 10px; border-bottom: 1px solid #f3f4f6; }
        .table tbody tr:nth-child(even) { background: #fafbfc; }
        .text-right { text-align: right !important; }
        .totals-section { margin-left: 50%; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 8px 0; font-size: 13px; }
        .totals-table .label { color: #6b7280; }
        .totals-table .value { text-align: right; font-weight: 600; color: #111827; }
        .totals-table .grand-total { background: #f3f4f6; }
        .totals-table .grand-total td { padding: 14px 12px; font-size: 16px; font-weight: 800; border-top: 2px solid #4f46e5; border-bottom: 2px solid #4f46e5; }
        .totals-table .grand-total .value { color: #4f46e5; }
        .notes { margin-top: 30px; padding: 15px; background: #f9fafb; border-left: 3px solid #4f46e5; border-radius: 0 8px 8px 0; }
        .notes-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: 600; margin-bottom: 4px; }
        .notes p { font-size: 12px; color: #4b5563; }
        .footer { text-align: center; padding: 20px; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; margin-top: 20px; }
        .signature { margin-top: 40px; text-align: center; }
        .signature p { font-size: 12px; color: #6b7280; margin-bottom: 60px; }
        .signature .name { font-weight: 700; color: #111827; border-top: 1px solid #d1d5db; padding-top: 6px; display: inline-block; min-width: 200px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="header-top">
                <div class="brand">
                    <h2>PT Cahaya Tiga Putri Mandiri</h2>
                    <p>Kontraktor &amp; Jasa Konstruksi</p>
                </div>
                <div class="invoice-title">
                    <h1>INVOICE</h1>
                    <div class="inv-num">#{{ $customerInvoice->invoice_number }}</div>
                </div>
            </div>
        </div>

        <div class="body">
            <div class="info-grid">
                <div class="info-block">
                    <div class="info-label">Ditagihkan Kepada</div>
                    <div class="name">{{ $customerInvoice->customer->name }}</div>
                    @if($customerInvoice->customer->email)<div class="detail">{{ $customerInvoice->customer->email }}</div>@endif
                    @if($customerInvoice->customer->phone)<div class="detail">{{ $customerInvoice->customer->phone }}</div>@endif
                    @if($customerInvoice->customer->address)<div class="detail">{{ $customerInvoice->customer->address }}</div>@endif
                </div>
                <div class="info-block" style="flex: 0 0 45%;">
                    <div class="info-label">Detail Invoice</div>
                    <table class="meta-table">
                        <tr>
                            <td class="label">Tanggal Faktur</td>
                            <td class="value">{{ \Carbon\Carbon::parse($customerInvoice->invoice_date)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jatuh Tempo</td>
                            <td class="value">{{ $customerInvoice->due_date ? \Carbon\Carbon::parse($customerInvoice->due_date)->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value"><span class="status-badge {{ $customerInvoice->status === 'paid' ? 'status-paid' : 'status-unpaid' }}">{{ $customerInvoice->status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Deskripsi</th>
                        <th class="text-right" style="width: 70px;">Qty</th>
                        <th class="text-right" style="width: 110px;">Harga</th>
                        <th class="text-right" style="width: 120px;">Subtotal</th>
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

            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">Rp {{ number_format($customerInvoice->lines->sum('subtotal'), 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pajak ({{ $customerInvoice->tax_percentage }}%)</td>
                        <td class="value">Rp {{ number_format($customerInvoice->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL TAGIHAN</td>
                        <td class="value">Rp {{ number_format($customerInvoice->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>

            @if($customerInvoice->notes)
            <div class="notes">
                <div class="notes-label">Catatan</div>
                <p>{{ $customerInvoice->notes }}</p>
            </div>
            @endif

            <div class="signature">
                <p>Hormat kami,</p>
                <span class="name">PT Cahaya Tiga Putri Mandiri</span>
            </div>
        </div>

        <div class="footer">
            Terima kasih atas kepercayaan Anda. Dokumen ini dihasilkan secara otomatis oleh Sistem Accounting.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1f2937; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border-bottom: 1px solid #ddd; padding: 10px 5px; text-align: left; }
        .table th { background-color: #f8fafc; font-weight: bold; }
        .text-right { text-align: right !important; }
        .section-title { font-weight: bold; font-size: 16px; margin-top: 25px; margin-bottom: 10px; color: #4b5563; }
        .total-row { font-weight: bold; background-color: #f1f5f9; }
        .grand-total { font-size: 16px; font-weight: bold; text-align: right; padding: 15px; margin-top: 20px; border-top: 2px solid #333; border-bottom: 2px solid #333; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .summary-box { background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .summary-box table { width: 100%; }
        .summary-box td { padding: 6px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT Cahaya Tiga Putri Mandiri</h2>
        <h1>LAPORAN ARUS KAS</h1>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></p>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td><strong>Saldo Kas Awal</strong></td>
                <td class="text-right">Rp {{ number_format($openingCash, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Perubahan Kas Bersih</strong></td>
                <td class="text-right {{ $netChange >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($netChange, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Saldo Kas Akhir</strong></td>
                <td class="text-right text-success"><strong>Rp {{ number_format($closingCash, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="section-title">AKTIVITAS OPERASI</div>
    <table class="table">
        <tbody>
            @foreach($operatingDetails as $detail)
            <tr>
                <td>{{ $detail['code'] }} - {{ $detail['name'] }}</td>
                <td class="text-right {{ $detail['amount'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($detail['amount'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Arus Kas Bersih dari Aktivitas Operasi</td>
                <td class="text-right">Rp {{ number_format($operatingFlow, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">AKTIVITAS PENDANAAN</div>
    <table class="table">
        <tbody>
            @foreach($financingDetails as $detail)
            <tr>
                <td>{{ $detail['code'] }} - {{ $detail['name'] }}</td>
                <td class="text-right {{ $detail['amount'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($detail['amount'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Arus Kas Bersih dari Aktivitas Pendanaan</td>
                <td class="text-right">Rp {{ number_format($financingFlow, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="grand-total">
        PERUBAHAN KAS BERSIH: 
        <span class="{{ $netChange >= 0 ? 'text-success' : 'text-danger' }}">
            Rp {{ number_format($netChange, 0, ',', '.') }}
        </span>
    </div>
</body>
</html>

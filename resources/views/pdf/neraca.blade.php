<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1f2937; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border-bottom: 1px solid #ddd; padding: 10px 5px; text-align: left; }
        .table th { background-color: #f8fafc; font-weight: bold; }
        .text-right { text-align: right !important; }
        .section-title { font-weight: bold; font-size: 16px; margin-top: 20px; margin-bottom: 10px; color: #4b5563; }
        .total-row { font-weight: bold; background-color: #f1f5f9; }
        .grand-total { font-size: 16px; font-weight: bold; text-align: right; padding: 15px; margin-top: 20px; border-top: 2px solid #333; border-bottom: 2px solid #333; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #4f46e5; }
        .balance-ok { background: #dcfce7; color: #166534; padding: 10px; text-align: center; font-weight: bold; border-radius: 4px; margin-bottom: 20px; }
        .balance-no { background: #fee2e2; color: #991b1b; padding: 10px; text-align: center; font-weight: bold; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT Cahaya Tiga Putri Mandiri</h2>
        <h1>LAPORAN NERACA</h1>
        <p>Per Tanggal: <strong>{{ \Carbon\Carbon::parse($asOfDate)->format('d M Y') }}</strong></p>
    </div>

    @php $balanced = abs($totalAsset - $totalLiabilityEquity) < 1; @endphp
    <div class="{{ $balanced ? 'balance-ok' : 'balance-no' }}">
        {{ $balanced ? '✓ NERACA SEIMBANG (Aset = Kewajiban + Modal)' : '⚠ NERACA TIDAK SEIMBANG — periksa data jurnal' }}
    </div>

    <div class="section-title">ASET</div>
    <table class="table">
        <tbody>
            @foreach($assetAccounts as $account)
            @if(abs($account->calculated_balance) > 0)
            <tr>
                <td>{{ $account->code }} - {{ $account->name }}</td>
                <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="total-row">
                <td>Total Aset</td>
                <td class="text-right text-success">Rp {{ number_format($totalAsset, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">KEWAJIBAN</div>
    <table class="table">
        <tbody>
            @foreach($liabilityAccounts as $account)
            @if(abs($account->calculated_balance) > 0)
            <tr>
                <td>{{ $account->code }} - {{ $account->name }}</td>
                <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="total-row">
                <td>Total Kewajiban</td>
                <td class="text-right text-danger">Rp {{ number_format($totalLiability, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">MODAL</div>
    <table class="table">
        <tbody>
            @foreach($equityAccounts as $account)
            @if(abs($account->calculated_balance) > 0)
            <tr>
                <td>{{ $account->code }} - {{ $account->name }}</td>
                <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            @if(abs($currentYearIncome) > 0)
            <tr>
                <td>399 - Laba Berjalan (Tahun Berjalan)</td>
                <td class="text-right">Rp {{ number_format($currentYearIncome, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Modal + Laba</td>
                <td class="text-right text-primary">Rp {{ number_format($totalEquity, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Kewajiban + Modal</td>
                <td class="text-right">Rp {{ number_format($totalLiabilityEquity, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

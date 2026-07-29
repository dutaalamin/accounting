<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
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
        .net-income { font-size: 18px; font-weight: bold; text-align: right; padding: 15px; margin-top: 30px; border-top: 2px solid #333; border-bottom: 2px solid #333; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT Cahaya Tiga Putri Mandiri</h2>
        <h1>LAPORAN LABA RUGI</h1>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></p>
    </div>

    <div class="section-title">PENDAPATAN / PEMASUKAN</div>
    <table class="table">
        <tbody>
            @foreach($revenueAccounts as $account)
            @if($account->calculated_balance > 0)
            <tr>
                <td>{{ $account->code ?? '' }} - {{ $account->name }}</td>
                <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="total-row">
                <td>Total Pendapatan</td>
                <td class="text-right text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">BEBAN / PENGELUARAN</div>
    <table class="table">
        <tbody>
            @foreach($expenseAccounts as $account)
            @if($account->calculated_balance > 0)
            <tr>
                <td>{{ $account->code ?? '' }} - {{ $account->name }}</td>
                <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="total-row">
                <td>Total Pengeluaran</td>
                <td class="text-right text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="net-income">
        LABA (RUGI) BERSIH: 
        <span class="{{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
            Rp {{ number_format($netIncome, 0, ',', '.') }}
        </span>
    </div>
</body>
</html>

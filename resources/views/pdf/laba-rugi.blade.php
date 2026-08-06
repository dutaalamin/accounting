<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 13px; color: #374151; line-height: 1.5; }
        .report-box { max-width: 800px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center; }
        .header h2 { font-size: 18px; font-weight: 600; opacity: 0.9; margin-bottom: 4px; }
        .header h1 { font-size: 24px; font-weight: 800; letter-spacing: 1px; margin-bottom: 8px; }
        .header p { font-size: 12px; opacity: 0.85; }
        .body { background: white; padding: 30px 40px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 12px 12px; }
        .section-title { font-size: 14px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin: 25px 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb; }
        .section-title:first-child { margin-top: 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table td { padding: 10px 8px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right !important; }
        .total-row { font-weight: 700; background: #f9fafb; }
        .total-row td { border-bottom: 2px solid #e5e7eb; padding: 12px 8px; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .net-income-box { margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 10px; border-left: 4px solid #4f46e5; }
        .net-income-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: 600; }
        .net-income-value { font-size: 24px; font-weight: 800; margin-top: 4px; }
        .footer { text-align: center; padding: 15px; font-size: 11px; color: #9ca3af; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="report-box">
        <div class="header">
            <h2>PT Cahaya Tiga Putri Mandiri</h2>
            <h1>LAPORAN LABA RUGI</h1>
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        <div class="body">
            <div class="section-title">Pendapatan / Pemasukan</div>
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

            <div class="section-title">Beban / Pengeluaran</div>
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

            <div class="net-income-box">
                <div class="net-income-label">{{ $netIncome >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</div>
                <div class="net-income-value {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($netIncome, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="footer">
            Dokumen ini dihasilkan secara otomatis oleh Sistem Accounting PT Cahaya Tiga Putri Mandiri.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca - PT Cahaya Tiga Putri Mandiri</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 12px; 
            color: #1f2937; 
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .border-top-accent {
            border-top: 4px solid #4f46e5;
            margin-top: -20px;
            margin-bottom: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
        }
        .header h2 { 
            margin: 0; 
            font-size: 14px; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            color: #4b5563; 
        }
        .header h1 { 
            margin: 5px 0 0 0; 
            font-size: 20px; 
            color: #111827; 
            font-weight: 800;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #6b7280;
        }
        .balance-banner {
            padding: 10px 15px;
            text-align: center;
            font-weight: bold;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .balance-ok { 
            background: #ecfdf5; 
            color: #065f46; 
            border: 1px solid #a7f3d0;
        }
        .balance-no { 
            background: #fef2f2; 
            color: #991b1b; 
            border: 1px solid #fecaca;
        }
        .report-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
        }
        .report-table th { 
            background-color: #f9fafb; 
            font-weight: bold; 
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            color: #374151;
            border-bottom: 2px solid #e5e7eb; 
            padding: 8px 10px;
        }
        .report-table td { 
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6; 
        }
        .section-header-row td {
            font-weight: bold;
            font-size: 13px;
            color: #111827;
            padding-top: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        .sub-section-row td {
            font-weight: bold;
            font-size: 11px;
            color: #4b5563;
            background-color: #f9fafb;
            padding-left: 10px;
        }
        .account-row td {
            padding-left: 20px;
            color: #374151;
        }
        .account-code {
            font-family: monospace;
            color: #9ca3af;
            margin-right: 8px;
        }
        .total-row td { 
            font-weight: bold; 
            background-color: #f9fafb;
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            color: #111827;
        }
        .grand-total-row td {
            font-weight: bold;
            font-size: 13px;
            background-color: #f3f4f6;
            border-top: 2px solid #9ca3af;
            border-bottom: 4px double #111827; /* Double line for final totals */
            color: #111827;
            padding: 10px;
        }
        .text-right { 
            text-align: right !important; 
        }
    </style>
</head>
<body>
    <div class="border-top-accent"></div>
    <div class="header">
        <h2>PT Cahaya Tiga Putri Mandiri</h2>
        <h1>LAPORAN NERACA</h1>
        <p>Per Tanggal: <strong>{{ \Carbon\Carbon::parse($asOfDate)->format('d M Y') }}</strong></p>
    </div>

    @php $balanced = abs($totalAsset - $totalLiabilityEquity) < 1; @endphp
    <div class="balance-banner {{ $balanced ? 'balance-ok' : 'balance-no' }}">
        {{ $balanced ? '✓ NERACA SEIMBANG (Aset = Kewajiban + Modal)' : '⚠ NERACA TIDAK SEIMBANG — Silakan periksa entri jurnal Anda' }}
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="text-align: left;">Deskripsi Akun</th>
                <th style="width: 35%;" class="text-right">Saldo (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <!-- SECTION ASET -->
            <tr class="section-header-row">
                <td colspan="2">ASET</td>
            </tr>
            @php $hasAssets = false; @endphp
            @foreach($assetAccounts as $account)
                @if(abs($account->calculated_balance) > 0)
                    @php $hasAssets = true; @endphp
                    <tr class="account-row">
                        <td><span class="account-code">{{ $account->code }}</span>{{ $account->name }}</td>
                        <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
            @if(!$hasAssets)
                <tr class="account-row"><td colspan="2" class="text-center" style="color: #9ca3af; font-style: italic;">Tidak ada saldo aset aktif</td></tr>
            @endif
            <tr class="total-row">
                <td style="padding-left: 10px;">Total Aset</td>
                <td class="text-right">Rp {{ number_format($totalAsset, 0, ',', '.') }}</td>
            </tr>

            <!-- SECTION KEWAJIBAN & MODAL -->
            <tr class="section-header-row">
                <td colspan="2">KEWAJIBAN & MODAL</td>
            </tr>
            
            <!-- SUB-SECTION KEWAJIBAN -->
            <tr class="sub-section-row">
                <td colspan="2">Kewajiban (Liabilitas)</td>
            </tr>
            @php $hasLiabilities = false; @endphp
            @foreach($liabilityAccounts as $account)
                @if(abs($account->calculated_balance) > 0)
                    @php $hasLiabilities = true; @endphp
                    <tr class="account-row">
                        <td><span class="account-code">{{ $account->code }}</span>{{ $account->name }}</td>
                        <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
            @if(!$hasLiabilities)
                <tr class="account-row"><td colspan="2" style="color: #9ca3af; font-style: italic;">Tidak ada saldo kewajiban aktif</td></tr>
            @endif
            <tr class="total-row">
                <td style="padding-left: 10px; font-weight: normal; color: #4b5563;">Total Kewajiban</td>
                <td class="text-right" style="font-weight: normal; color: #4b5563;">Rp {{ number_format($totalLiability, 0, ',', '.') }}</td>
            </tr>

            <!-- SUB-SECTION MODAL -->
            <tr class="sub-section-row">
                <td colspan="2">Modal (Ekuitas)</td>
            </tr>
            @php $hasEquity = false; @endphp
            @foreach($equityAccounts as $account)
                @if(abs($account->calculated_balance) > 0)
                    @php $hasEquity = true; @endphp
                    <tr class="account-row">
                        <td><span class="account-code">{{ $account->code }}</span>{{ $account->name }}</td>
                        <td class="text-right">Rp {{ number_format($account->calculated_balance, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
            @if(abs($currentYearIncome) > 0)
                @php $hasEquity = true; @endphp
                <tr class="account-row">
                    <td><span class="account-code">399</span>Laba Berjalan (Tahun Berjalan)</td>
                    <td class="text-right">Rp {{ number_format($currentYearIncome, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if(!$hasEquity)
                <tr class="account-row"><td colspan="2" style="color: #9ca3af; font-style: italic;">Tidak ada saldo modal aktif</td></tr>
            @endif
            <tr class="total-row">
                <td style="padding-left: 10px; font-weight: normal; color: #4b5563;">Total Modal</td>
                <td class="text-right" style="font-weight: normal; color: #4b5563;">Rp {{ number_format($totalEquity, 0, ',', '.') }}</td>
            </tr>

            <!-- GRAND TOTAL KEWAJIBAN & MODAL -->
            <tr class="grand-total-row">
                <td>TOTAL KEWAJIBAN & MODAL</td>
                <td class="text-right">Rp {{ number_format($totalLiabilityEquity, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

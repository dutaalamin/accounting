<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas - PT Cahaya Tiga Putri Mandiri</title>
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
        .summary-box { 
            background: #f9fafb; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            border: 1px solid #e5e7eb;
        }
        .summary-box table { 
            width: 100%; 
            border-collapse: collapse;
        }
        .summary-box td { 
            padding: 6px 0; 
            font-size: 12px;
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
        .text-success {
            color: #065f46;
        }
        .text-danger {
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="border-top-accent"></div>
    <div class="header">
        <h2>PT Cahaya Tiga Putri Mandiri</h2>
        <h1>LAPORAN ARUS KAS</h1>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></p>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td><strong>Saldo Kas Awal</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($openingCash, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Perubahan Kas Bersih</td>
                <td class="text-right {{ $netChange >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($netChange, 0, ',', '.') }}
                </td>
            </tr>
            <tr style="border-top: 1px solid #e5e7eb;">
                <td style="padding-top: 8px;"><strong>Saldo Kas Akhir</strong></td>
                <td class="text-right text-success" style="padding-top: 8px; font-size: 13px;">
                    <strong>Rp {{ number_format($closingCash, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="text-align: left;">Aktivitas & Kategori Akun</th>
                <th style="width: 35%;" class="text-right">Arus Kas (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <!-- AKTIVITAS OPERASI -->
            <tr class="section-header-row">
                <td colspan="2">AKTIVITAS OPERASI</td>
            </tr>
            @php $hasOperating = false; @endphp
            @foreach($operatingDetails as $detail)
                @php $hasOperating = true; @endphp
                <tr class="account-row">
                    <td><span class="account-code">{{ $detail['code'] }}</span>{{ $detail['name'] }}</td>
                    <td class="text-right {{ $detail['amount'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($detail['amount'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            @if(!$hasOperating)
                <tr class="account-row"><td colspan="2" class="text-center" style="color: #9ca3af; font-style: italic;">Tidak ada aktivitas operasi aktif</td></tr>
            @endif
            <tr class="total-row">
                <td style="padding-left: 10px;">Arus Kas Bersih dari Aktivitas Operasi</td>
                <td class="text-right {{ $operatingFlow >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($operatingFlow, 0, ',', '.') }}
                </td>
            </tr>

            <!-- AKTIVITAS PENDANAAN -->
            <tr class="section-header-row">
                <td colspan="2">AKTIVITAS PENDANAAN</td>
            </tr>
            @php $hasFinancing = false; @endphp
            @foreach($financingDetails as $detail)
                @php $hasFinancing = true; @endphp
                <tr class="account-row">
                    <td><span class="account-code">{{ $detail['code'] }}</span>{{ $detail['name'] }}</td>
                    <td class="text-right {{ $detail['amount'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($detail['amount'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            @if(!$hasFinancing)
                <tr class="account-row"><td colspan="2" class="text-center" style="color: #9ca3af; font-style: italic;">Tidak ada aktivitas pendanaan aktif</td></tr>
            @endif
            <tr class="total-row">
                <td style="padding-left: 10px;">Arus Kas Bersih dari Aktivitas Pendanaan</td>
                <td class="text-right {{ $financingFlow >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($financingFlow, 0, ',', '.') }}
                </td>
            </tr>

            <!-- PERUBAHAN KAS BERSIH -->
            <tr class="grand-total-row">
                <td>PERUBAHAN KAS BERSIH</td>
                <td class="text-right {{ $netChange >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($netChange, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>

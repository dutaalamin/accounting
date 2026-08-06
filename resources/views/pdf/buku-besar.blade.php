<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Besar - {{ $account->code }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; color: #1f2937; }
        .account-info { background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .account-info h3 { margin: 0 0 5px 0; color: #1f2937; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border-bottom: 1px solid #ddd; padding: 8px 5px; text-align: left; }
        .table th { background-color: #f1f5f9; font-weight: bold; }
        .text-right { text-align: right !important; }
        .total-row { font-weight: bold; background-color: #f1f5f9; }
        .opening-row { background-color: #f8fafc; font-weight: bold; }
        .closing-row { background-color: #dcfce7; font-weight: bold; color: #166534; }
        .badge { display: inline-block; padding: 2px 8px; background: #e0e7ff; color: #4338ca; border-radius: 4px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT Cahaya Tiga Putri Mandiri</h2>
        <h1>BUKU BESAR</h1>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></p>
    </div>

    <div class="account-info">
        <h3>Akun: {{ $account->code }} - {{ $account->name }}</h3>
        <p style="margin:0; color:#6b7280; text-transform:uppercase; font-size:11px;">Tipe: {{ $account->type }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Referensi</th>
                <th>Deskripsi</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Kredit</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr class="opening-row">
                <td colspan="3">Saldo Awal (per {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }})</td>
                <td colspan="2"></td>
                <td class="text-right">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>

            @php
                $running = $openingBalance;
                $isDebitNormal = in_array($account->type, ['asset', 'expense']);
            @endphp
            @foreach($lines as $line)
                @php
                    $debit = (float) $line->debit;
                    $credit = (float) $line->credit;
                    $running += $isDebitNormal ? ($debit - $credit) : ($credit - $debit);
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($line->journalEntry->date)->format('d/m/Y') }}</td>
                    <td><span class="badge">{{ $line->journalEntry->reference ?? $line->journalEntry->id }}</span></td>
                    <td>{{ $line->journalEntry->description ?? '-' }}</td>
                    <td class="text-right">{{ $debit > 0 ? 'Rp ' . number_format($debit, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $credit > 0 ? 'Rp ' . number_format($credit, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($running, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            @if($lines->isEmpty())
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#9ca3af;">Tidak ada mutasi pada rentang tanggal ini.</td>
            </tr>
            @endif

            <tr class="closing-row">
                <td colspan="5">Saldo Akhir (per {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</td>
                <td class="text-right">Rp {{ number_format($running, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Account;

class AccountingStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Total Kas & Bank (Asset) = Saldo Debit - Kredit
        $assetAccounts = Account::where('type', 'asset')->pluck('id');
        $debit = \App\Models\JournalEntryLine::whereIn('account_id', $assetAccounts)->sum('debit');
        $credit = \App\Models\JournalEntryLine::whereIn('account_id', $assetAccounts)->sum('credit');
        $totalKas = $debit - $credit;
        
        // 2. Total Piutang Berjalan (AR) -> Tagihan belum lunas
        $totalAR = \App\Models\CustomerInvoice::where('status', 'unpaid')->sum('total_amount');
        
        // 3. Total Utang Dagang (AP) -> Tagihan vendor belum dibayar
        $totalAP = \App\Models\SupplierInvoice::where('status', 'unpaid')->sum('total_amount');

        return [
            Stat::make('Total Kas & Bank', 'Rp ' . number_format($totalKas, 0, ',', '.'))
                ->description('Total saldo aset saat ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([15, 12, 18, 14, 22, 25, $totalKas > 0 ? 28 : 10])
                ->color('success'),
            
            Stat::make('Total Piutang Berjalan (AR)', 'Rp ' . number_format($totalAR, 0, ',', '.'))
                ->description('Invoice pelanggan belum lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([10, 15, 8, 12, 19, 14, $totalAR > 0 ? 25 : 5])
                ->color('info'),
                
            Stat::make('Total Utang Dagang (AP)', 'Rp ' . number_format($totalAP, 0, ',', '.'))
                ->description('Invoice vendor belum dibayar')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([12, 10, 8, 9, 7, 5, $totalAP > 0 ? 15 : 2])
                ->color('danger'),
        ];
    }
}

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
        // 1. Total Kas & Bank (Asset)
        $totalKas = Account::where('type', 'asset')->sum('initial_balance');
        
        // 2. Total Piutang Berjalan (AR)
        $totalAR = 0; // Akan diupdate saat tabel Invoice siap
        
        // 3. Total Utang Dagang (AP)
        $totalAP = 0; // Akan diupdate saat tabel Invoice siap

        return [
            Stat::make('Total Kas & Bank', 'Rp ' . number_format($totalKas, 2, ',', '.'))
                ->description('Total saldo aset saat ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            
            Stat::make('Total Piutang Berjalan (AR)', 'Rp ' . number_format($totalAR, 2, ',', '.'))
                ->description('Invoice pelanggan belum lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
                
            Stat::make('Total Utang Dagang (AP)', 'Rp ' . number_format($totalAP, 2, ',', '.'))
                ->description('Invoice vendor belum dibayar')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Account;
use App\Models\JournalEntryLine;

class AccountTypePieChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Pengeluaran Bulan Ini';
    protected static ?int $sort = 2; // Posisi di bawah Stat Cards
    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d');

        // Ambil semua akun tipe expense
        $expenseAccounts = Account::where('type', 'expense')->get();

        $labels = [];
        $data = [];
        $colors = [
            '#ef4444', // red
            '#f59e0b', // amber
            '#8b5cf6', // violet
            '#ec4899', // pink
            '#06b6d4', // cyan
            '#84cc16', // lime
            '#f97316', // orange
            '#6366f1', // indigo
            '#14b8a6', // teal
            '#e11d48', // rose
        ];

        foreach ($expenseAccounts as $index => $account) {
            $debit = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
                })->sum('debit');
            $credit = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
                })->sum('credit');

            $total = max(0, $debit - $credit);

            if ($total > 0) {
                $labels[] = $account->name;
                $data[] = $total;
            }
        }

        // Jika belum ada pengeluaran bulan ini, tampilkan pesan
        if (empty($data)) {
            $labels = ['Belum ada pengeluaran'];
            $data = [1];
            $colors = ['#d1d5db']; // gray
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah (Rp)',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Account;

class AccountTypePieChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Akun per Tipe';
    protected static ?int $sort = 2; // Posisi di bawah Stat Cards

    protected function getData(): array
    {
        $types = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $data = [];
        foreach ($types as $type) {
            $data[] = Account::where('type', $type)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Akun',
                    'data' => $data,
                    'backgroundColor' => [
                        '#10b981', // emerald-500 (Asset)
                        '#ef4444', // red-500 (Liability)
                        '#3b82f6', // blue-500 (Equity)
                        '#14b8a6', // teal-500 (Revenue)
                        '#f59e0b', // amber-500 (Expense)
                    ],
                ],
            ],
            'labels' => ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

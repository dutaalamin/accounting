<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class IncomeExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan & Pengeluaran';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Data dummy sementara agar chart tidak kosong
        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Revenue)',
                    'data' => [12000000, 15000000, 14000000, 18000000, 22000000, 25000000, 21000000],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
                [
                    'label' => 'Pengeluaran (Expense)',
                    'data' => [8000000, 9500000, 11000000, 10000000, 13000000, 15000000, 14000000],
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

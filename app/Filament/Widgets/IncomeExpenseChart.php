<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class IncomeExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan & Pengeluaran';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $revenues = [];
        $expenses = [];
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        $revAccounts = \App\Models\Account::where('type', 'revenue')->pluck('id');
        $expAccounts = \App\Models\Account::where('type', 'expense')->pluck('id');

        for ($i = 1; $i <= 12; $i++) {
            $startDate = now()->setMonth($i)->startOfMonth()->format('Y-m-d');
            $endDate = now()->setMonth($i)->endOfMonth()->format('Y-m-d');

            // Hitung total Pendapatan (Kredit - Debit)
            $revDebit = \App\Models\JournalEntryLine::whereIn('account_id', $revAccounts)
                ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                })->sum('debit');
            $revCredit = \App\Models\JournalEntryLine::whereIn('account_id', $revAccounts)
                ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                })->sum('credit');
            $revenues[] = max(0, $revCredit - $revDebit);

            // Hitung total Pengeluaran (Debit - Kredit)
            $expDebit = \App\Models\JournalEntryLine::whereIn('account_id', $expAccounts)
                ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                })->sum('debit');
            $expCredit = \App\Models\JournalEntryLine::whereIn('account_id', $expAccounts)
                ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                })->sum('credit');
            $expenses[] = max(0, $expDebit - $expCredit);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Revenue)',
                    'data' => $revenues,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
                [
                    'label' => 'Pengeluaran (Expense)',
                    'data' => $expenses,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

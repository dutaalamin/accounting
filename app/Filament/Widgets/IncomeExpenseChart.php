<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class IncomeExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan & Pengeluaran';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '250px';

    public ?string $filter = '2026';

    protected function getData(): array
    {
        $revenues = [];
        $expenses = [];
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        $revAccounts = \App\Models\Account::where('type', 'revenue')->pluck('id');
        $expAccounts = \App\Models\Account::where('type', 'expense')->pluck('id');

        $year = (int) ($this->filter ?? now()->year);

        for ($i = 1; $i <= 12; $i++) {
            // Gunakan Carbon::create untuk menghindari bug mutasi now()->setMonth()
            $startDate = \Carbon\Carbon::createFromDate($year, $i, 1)->startOfMonth()->format('Y-m-d');
            $endDate = \Carbon\Carbon::createFromDate($year, $i, 1)->endOfMonth()->format('Y-m-d');

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
                    'borderColor' => '#6366f1', // Indigo
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pengeluaran (Expense)',
                    'data' => $expenses,
                    'borderColor' => '#ec4899', // Pink
                    'backgroundColor' => 'rgba(236, 72, 153, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;
        $years = range($currentYear - 4, $currentYear + 1);

        return array_combine($years, $years);
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Account;
use App\Models\JournalEntryLine;

class LabaRugiReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Laporan Keuangan';
    protected static ?string $navigationLabel = 'Laba Rugi (Income Statement)';
    protected static ?string $title = 'Laporan Laba Rugi Bersih';

    protected static string $view = 'filament.pages.laba-rugi-report';

    public float $totalRevenue = 0;
    public float $totalExpense = 0;
    public float $netIncome = 0;

    public function mount()
    {
        // Hitung total Pemasukan (Revenue) = Saldo Kredit - Debit
        $revenueAccounts = Account::where('type', 'revenue')->pluck('id');
        
        $revenueDebit = JournalEntryLine::whereIn('account_id', $revenueAccounts)->sum('debit');
        $revenueCredit = JournalEntryLine::whereIn('account_id', $revenueAccounts)->sum('credit');
        
        // Akun Revenue bersaldo normal KREDIT
        $this->totalRevenue = $revenueCredit - $revenueDebit;

        // Hitung total Pengeluaran (Expense) = Saldo Debit - Kredit
        $expenseAccounts = Account::where('type', 'expense')->pluck('id');
        
        $expenseDebit = JournalEntryLine::whereIn('account_id', $expenseAccounts)->sum('debit');
        $expenseCredit = JournalEntryLine::whereIn('account_id', $expenseAccounts)->sum('credit');

        // Akun Expense bersaldo normal DEBIT
        $this->totalExpense = $expenseDebit - $expenseCredit;

        // Laba Bersih = Pendapatan - Pengeluaran
        $this->netIncome = $this->totalRevenue - $this->totalExpense;
    }
}

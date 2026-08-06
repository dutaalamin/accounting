<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Account;
use App\Models\JournalEntryLine;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Carbon\Carbon;

class NeracaReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Laporan Keuangan';
    protected static ?string $navigationLabel = 'Neraca (Balance Sheet)';
    protected static ?string $title = 'Laporan Neraca Keuangan';

    protected static string $view = 'filament.pages.neraca-report';

    public float $totalAsset = 0;
    public float $totalLiability = 0;
    public float $totalEquity = 0;
    public float $totalLiabilityEquity = 0;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'as_of_date' => Carbon::now()->toDateString(),
        ]);

        $this->calculateTotals();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    DatePicker::make('as_of_date')
                        ->label('Per Tanggal (Snapshot)')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now())
                        ->live(),
                ]),
            ])
            ->statePath('data');
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'data.')) {
            $this->calculateTotals();
        }
    }

    /**
     * Hitung saldo akun sampai tanggal tertentu.
     * Asset/Expense: initial_balance + debit - credit
     * Liability/Equity/Revenue: initial_balance + credit - debit
     */
    protected function balanceUntil(Account $account, string $asOfDate): float
    {
        $lines = JournalEntryLine::where('account_id', $account->id)
            ->whereHas('journalEntry', fn ($q) => $q->whereDate('date', '<=', $asOfDate))
            ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
            ->first();

        $debit = (float) ($lines->debit ?? 0);
        $credit = (float) ($lines->credit ?? 0);

        if (in_array($account->type, ['asset', 'expense'])) {
            return (float) $account->initial_balance + $debit - $credit;
        }

        return (float) $account->initial_balance + $credit - $debit;
    }

    public function calculateTotals(): void
    {
        $asOfDate = $this->data['as_of_date'] ?? Carbon::now()->toDateString();

        $this->totalAsset = 0;
        $this->totalLiability = 0;
        $this->totalEquity = 0;

        foreach (Account::where('type', 'asset')->get() as $account) {
            $account->calculated_balance = $this->balanceUntil($account, $asOfDate);
            $this->totalAsset += $account->calculated_balance;
        }

        foreach (Account::where('type', 'liability')->get() as $account) {
            $account->calculated_balance = $this->balanceUntil($account, $asOfDate);
            $this->totalLiability += $account->calculated_balance;
        }

        foreach (Account::where('type', 'equity')->get() as $account) {
            $account->calculated_balance = $this->balanceUntil($account, $asOfDate);
            $this->totalEquity += $account->calculated_balance;
        }

        // Laba ditahan (current year income) ditambahkan ke equity agar neraca balance
        // Aset = Kewajiban + Modal + Laba Berjalan
        $revenueAccounts = Account::where('type', 'revenue')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();
        $totalRevenue = 0;
        $totalExpense = 0;
        foreach ($revenueAccounts as $account) {
            $totalRevenue += $this->balanceUntil($account, $asOfDate);
        }
        foreach ($expenseAccounts as $account) {
            $totalExpense += $this->balanceUntil($account, $asOfDate);
        }
        $this->currentYearIncome = $totalRevenue - $totalExpense;
        $this->totalEquity += $this->currentYearIncome;

        $this->totalLiabilityEquity = $this->totalLiability + $this->totalEquity;
    }

    public function currentYearIncome(): float
    {
        return $this->currentYearIncome ?? 0;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('pdf')
                ->label('Cetak PDF')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn (): string => route('neraca.pdf', [
                    'as_of_date' => $this->data['as_of_date'] ?? Carbon::now()->toDateString(),
                ]))
                ->openUrlInNewTab(),
        ];
    }
}

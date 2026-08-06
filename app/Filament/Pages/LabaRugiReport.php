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

class LabaRugiReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Laporan Keuangan';
    protected static ?string $navigationLabel = 'Laba Rugi (Income Statement)';
    protected static ?string $title = 'Laporan Laba Rugi Bersih';

    protected static string $view = 'filament.pages.laba-rugi-report';

    public float $totalRevenue = 0;
    public float $totalExpense = 0;
    public float $netIncome = 0;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => Carbon::now()->startOfMonth()->toDateString(),
            'end_date' => Carbon::now()->endOfMonth()->toDateString(),
        ]);
        
        $this->calculateTotals();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function () {
                            $this->validateDates();
                            $this->calculateTotals();
                        }),
                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function () {
                            $this->validateDates();
                            $this->calculateTotals();
                        }),
                ]),
            ])
            ->statePath('data');
    }

    protected function validateDates(): void
    {
        $start = $this->data['start_date'] ?? null;
        $end = $this->data['end_date'] ?? null;

        if ($start && $end && $start > $end) {
            \Filament\Notifications\Notification::make()
                ->title('Rentang tanggal tidak valid')
                ->body('Tanggal mulai tidak boleh setelah tanggal akhir.')
                ->danger()
                ->send();
            // Tukar agar perhitungan tidak negatif
            $this->data['start_date'] = $end;
            $this->data['end_date'] = $start;
        }
    }

    public function calculateTotals(): void
    {
        $startDate = $this->data['start_date'] ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $this->data['end_date'] ?? Carbon::now()->endOfMonth()->toDateString();

        // Revenue
        $revenueAccounts = Account::where('type', 'revenue')->pluck('id');
        
        $revenueDebit = JournalEntryLine::whereIn('account_id', $revenueAccounts)
            ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->sum('debit');
            
        $revenueCredit = JournalEntryLine::whereIn('account_id', $revenueAccounts)
            ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->sum('credit');
            
        $this->totalRevenue = $revenueCredit - $revenueDebit;

        // Expense
        $expenseAccounts = Account::where('type', 'expense')->pluck('id');
        
        $expenseDebit = JournalEntryLine::whereIn('account_id', $expenseAccounts)
            ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->sum('debit');
            
        $expenseCredit = JournalEntryLine::whereIn('account_id', $expenseAccounts)
            ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->sum('credit');

        $this->totalExpense = $expenseDebit - $expenseCredit;

        // Net Income
        $this->netIncome = $this->totalRevenue - $this->totalExpense;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('pdf')
                ->label('Cetak PDF')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn (): string => route('laba-rugi.pdf', [
                    'start_date' => $this->data['start_date'] ?? Carbon::now()->startOfMonth()->toDateString(),
                    'end_date' => $this->data['end_date'] ?? Carbon::now()->endOfMonth()->toDateString(),
                ]))
                ->openUrlInNewTab(),
        ];
    }
}

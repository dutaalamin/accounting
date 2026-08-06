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
use Filament\Forms\Components\Select;
use Carbon\Carbon;

class BukuBesarReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Laporan Keuangan';
    protected static ?string $navigationLabel = 'Buku Besar (General Ledger)';
    protected static ?string $title = 'Laporan Buku Besar';

    protected static string $view = 'filament.pages.buku-besar-report';

    public ?array $data = [];
    public $lines;
    public $account;
    public float $openingBalance = 0;
    public float $runningBalance = 0;

    public function mount(): void
    {
        $this->form->fill([
            'account_id' => null,
            'start_date' => Carbon::now()->startOfMonth()->toDateString(),
            'end_date' => Carbon::now()->endOfMonth()->toDateString(),
        ]);

        $this->calculateTotals();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Select::make('account_id')
                        ->label('Pilih Akun')
                        ->options(Account::orderBy('code')->get()->mapWithKeys(fn ($a) => [$a->id => $a->code . ' - ' . $a->name]))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn () => $this->calculateTotals()),
                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now()->startOfMonth())
                        ->live()
                        ->afterStateUpdated(fn () => $this->calculateTotals()),
                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now()->endOfMonth())
                        ->live()
                        ->afterStateUpdated(fn () => $this->calculateTotals()),
                ]),
            ])
            ->statePath('data');
    }

    public function calculateTotals(): void
    {
        $accountId = $this->data['account_id'] ?? null;
        $startDate = $this->data['start_date'] ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $this->data['end_date'] ?? Carbon::now()->endOfMonth()->toDateString();

        if (! $accountId) {
            $this->lines = collect();
            $this->account = null;
            $this->openingBalance = 0;
            $this->runningBalance = 0;
            return;
        }

        $this->account = Account::find($accountId);

        // Saldo awal = semua transaksi SEBELUM start_date
        $opening = JournalEntryLine::where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->whereDate('date', '<', $startDate))
            ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
            ->first();

        $openingDebit = (float) ($opening->debit ?? 0);
        $openingCredit = (float) ($opening->credit ?? 0);

        if ($this->account && in_array($this->account->type, ['asset', 'expense'])) {
            $this->openingBalance = (float) $this->account->initial_balance + $openingDebit - $openingCredit;
        } else {
            $this->openingBalance = (float) $this->account->initial_balance + $openingCredit - $openingDebit;
        }

        // Mutasi dalam rentang tanggal
        $this->lines = JournalEntryLine::where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->with('journalEntry')
            ->orderBy('journalEntry.date')
            ->get()
            ->sortBy(fn ($line) => $line->journalEntry->date);

        $this->runningBalance = $this->openingBalance;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('pdf')
                ->label('Cetak PDF')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn (): string => route('buku-besar.pdf', [
                    'account_id' => $this->data['account_id'] ?? '',
                    'start_date' => $this->data['start_date'] ?? Carbon::now()->startOfMonth()->toDateString(),
                    'end_date' => $this->data['end_date'] ?? Carbon::now()->endOfMonth()->toDateString(),
                ]))
                ->openUrlInNewTab(),
        ];
    }
}

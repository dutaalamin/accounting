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

class ArusKasReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Laporan Keuangan';
    protected static ?string $navigationLabel = 'Arus Kas (Cash Flow)';
    protected static ?string $title = 'Laporan Arus Kas';

    protected static string $view = 'filament.pages.arus-kas-report';

    public ?array $data = [];

    // Saldo kas awal & akhir
    public float $openingCash = 0;
    public float $closingCash = 0;

    // Arus kas per aktivitas
    public float $operatingFlow = 0;
    public float $investingFlow = 0;
    public float $financingFlow = 0;
    public float $netChange = 0;

    // Detail per akun
    public $operatingDetails;
    public $investingDetails;
    public $financingDetails;

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => Carbon::now()->startOfYear()->toDateString(),
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
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now()->startOfYear())
                        ->live(),
                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now()->endOfMonth())
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
     * Hitung mutasi sebuah akun dalam rentang tanggal.
     * Untuk akun kas (asset): debit = masuk, credit = keluar.
     */
    protected function accountFlow(Account $account, string $startDate, string $endDate): float
    {
        $lines = JournalEntryLine::where('account_id', $account->id)
            ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
            ->first();

        $debit = (float) ($lines->debit ?? 0);
        $credit = (float) ($lines->credit ?? 0);

        // Untuk akun asset (kas): debit menambah, credit mengurangi
        if (in_array($account->type, ['asset', 'expense'])) {
            return $debit - $credit;
        }
        // Untuk liability/equity/revenue: credit menambah, debit mengurangi
        return $credit - $debit;
    }

    /**
     * Saldo kas sampai tanggal tertentu.
     */
    protected function cashBalanceUntil(string $asOfDate): float
    {
        $cashAccounts = Account::where('type', 'asset')
            ->whereIn('code', ['111', '112']) // Kas Proyek & Bank BCA
            ->get();

        $total = 0;
        foreach ($cashAccounts as $account) {
            $lines = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->whereDate('date', '<=', $asOfDate))
                ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                ->first();
            $debit = (float) ($lines->debit ?? 0);
            $credit = (float) ($lines->credit ?? 0);
            $total += (float) $account->initial_balance + $debit - $credit;
        }

        return $total;
    }

    public function calculateTotals(): void
    {
        $startDate = $this->data['start_date'] ?? Carbon::now()->startOfYear()->toDateString();
        $endDate = $this->data['end_date'] ?? Carbon::now()->endOfMonth()->toDateString();

        // Saldo kas awal (sebelum start_date) dan akhir (sampai end_date)
        $this->openingCash = $this->cashBalanceUntil(Carbon::parse($startDate)->subDay()->toDateString());
        $this->closingCash = $this->cashBalanceUntil($endDate);

        // === AKTIVITAS OPERASI ===
        // Pendapatan (revenue) + Beban (expense) — non-kas
        $this->operatingFlow = 0;
        $this->operatingDetails = collect();

        $revenueAccounts = Account::where('type', 'revenue')->get();
        foreach ($revenueAccounts as $account) {
            $flow = $this->accountFlow($account, $startDate, $endDate);
            if (abs($flow) > 0) {
                $this->operatingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $this->operatingFlow += $flow;
            }
        }

        $expenseAccounts = Account::where('type', 'expense')->get();
        foreach ($expenseAccounts as $account) {
            $flow = $this->accountFlow($account, $startDate, $endDate);
            if (abs($flow) > 0) {
                $this->operatingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $this->operatingFlow += $flow;
            }
        }

        // Perubahan modal kerja (piutang, utang, uang muka) — sederhana
        $workingCapitalCodes = ['113', '114', '211', '212']; // Piutang, Uang Muka, Utang, PPN Keluaran
        $workingAccounts = Account::whereIn('code', $workingCapitalCodes)->get();
        foreach ($workingAccounts as $account) {
            $flow = $this->accountFlow($account, $startDate, $endDate);
            if (abs($flow) > 0) {
                // Untuk piutang: naik = kas keluar (negatif), turun = kas masuk (positif)
                // Untuk utang: naik = kas masuk (positif), turun = kas keluar (negatif)
                // accountFlow sudah mengembalikan credit-debit untuk liability, debit-credit untuk asset
                // Kita perlu inverse untuk asset (piutang naik = kas turun)
                if (in_array($account->type, ['asset'])) {
                    $flow = -$flow; // piutang/uang muka naik = kas berkurang
                }
                $this->operatingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $this->operatingFlow += $flow;
            }
        }

        // === AKTIVITAS INVESTASI ===
        // Aset non-kas lainnya (jika ada di masa depan). Saat ini kosong.
        $this->investingFlow = 0;
        $this->investingDetails = collect();

        // === AKTIVITAS PENDANAAN ===
        // Modal (equity)
        $this->financingFlow = 0;
        $this->financingDetails = collect();

        $equityAccounts = Account::where('type', 'equity')->get();
        foreach ($equityAccounts as $account) {
            $flow = $this->accountFlow($account, $startDate, $endDate);
            if (abs($flow) > 0) {
                $this->financingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $this->financingFlow += $flow;
            }
        }

        $this->netChange = $this->operatingFlow + $this->investingFlow + $this->financingFlow;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('pdf')
                ->label('Cetak PDF')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn (): string => route('arus-kas.pdf', [
                    'start_date' => $this->data['start_date'] ?? Carbon::now()->startOfYear()->toDateString(),
                    'end_date' => $this->data['end_date'] ?? Carbon::now()->endOfMonth()->toDateString(),
                ]))
                ->openUrlInNewTab(),
        ];
    }
}

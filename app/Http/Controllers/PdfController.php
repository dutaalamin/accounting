<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CustomerInvoice;
use App\Models\SupplierInvoice;
use App\Models\Account;

class PdfController extends Controller
{
    public function customerInvoice(CustomerInvoice $customerInvoice)
    {
        $customerInvoice->load(['customer', 'lines.product']);
        $pdf = Pdf::loadView('pdf.customer-invoice', compact('customerInvoice'));
        return $pdf->stream('Invoice-' . $customerInvoice->invoice_number . '.pdf');
    }

    public function supplierInvoice(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['vendor', 'lines.product']);
        $pdf = Pdf::loadView('pdf.supplier-invoice', compact('supplierInvoice'));
        return $pdf->stream('Tagihan-' . $supplierInvoice->invoice_number . '.pdf');
    }

    public function labaRugi(Request $request)
    {
        $startDate = $request->query('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

        $revenueAccounts = Account::where('type', 'revenue')->with(['journalEntryLines' => function($q) use ($startDate, $endDate) {
            $q->whereHas('journalEntry', function($q2) use ($startDate, $endDate) {
                $q2->whereBetween('date', [$startDate, $endDate]);
            });
        }])->get();

        $expenseAccounts = Account::where('type', 'expense')->with(['journalEntryLines' => function($q) use ($startDate, $endDate) {
            $q->whereHas('journalEntry', function($q2) use ($startDate, $endDate) {
                $q2->whereBetween('date', [$startDate, $endDate]);
            });
        }])->get();

        $totalRevenue = 0;
        foreach ($revenueAccounts as $account) {
            $sum = $account->journalEntryLines->sum('credit') - $account->journalEntryLines->sum('debit');
            $account->calculated_balance = $sum;
            $totalRevenue += $sum;
        }

        $totalExpense = 0;
        foreach ($expenseAccounts as $account) {
            $sum = $account->journalEntryLines->sum('debit') - $account->journalEntryLines->sum('credit');
            $account->calculated_balance = $sum;
            $totalExpense += $sum;
        }

        $netIncome = $totalRevenue - $totalExpense;

        $pdf = Pdf::loadView('pdf.laba-rugi', compact('revenueAccounts', 'expenseAccounts', 'totalRevenue', 'totalExpense', 'netIncome', 'startDate', 'endDate'));
        return $pdf->stream('Laba-Rugi-' . $startDate . '.pdf');
    }

    public function neraca(Request $request)
    {
        $asOfDate = $request->query('as_of_date', \Carbon\Carbon::now()->toDateString());

        $assetAccounts = Account::where('type', 'asset')->get();
        $liabilityAccounts = Account::where('type', 'liability')->get();
        $equityAccounts = Account::where('type', 'equity')->get();

        $balanceUntil = function (Account $account, string $date): float {
            $lines = \App\Models\JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->whereDate('date', '<=', $date))
                ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                ->first();
            $debit = (float) ($lines->debit ?? 0);
            $credit = (float) ($lines->credit ?? 0);
            if (in_array($account->type, ['asset', 'expense'])) {
                return (float) $account->initial_balance + $debit - $credit;
            }
            return (float) $account->initial_balance + $credit - $debit;
        };

        $totalAsset = 0;
        foreach ($assetAccounts as $account) {
            $account->calculated_balance = $balanceUntil($account, $asOfDate);
            $totalAsset += $account->calculated_balance;
        }

        $totalLiability = 0;
        foreach ($liabilityAccounts as $account) {
            $account->calculated_balance = $balanceUntil($account, $asOfDate);
            $totalLiability += $account->calculated_balance;
        }

        $totalEquity = 0;
        foreach ($equityAccounts as $account) {
            $account->calculated_balance = $balanceUntil($account, $asOfDate);
            $totalEquity += $account->calculated_balance;
        }

        // Laba berjalan
        $revenueAccounts = Account::where('type', 'revenue')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();
        $totalRevenue = 0;
        $totalExpense = 0;
        foreach ($revenueAccounts as $account) {
            $totalRevenue += $balanceUntil($account, $asOfDate);
        }
        foreach ($expenseAccounts as $account) {
            $totalExpense += $balanceUntil($account, $asOfDate);
        }
        $currentYearIncome = $totalRevenue - $totalExpense;
        $totalEquity += $currentYearIncome;

        $totalLiabilityEquity = $totalLiability + $totalEquity;

        $pdf = Pdf::loadView('pdf.neraca', compact('assetAccounts', 'liabilityAccounts', 'equityAccounts', 'totalAsset', 'totalLiability', 'totalEquity', 'currentYearIncome', 'totalLiabilityEquity', 'asOfDate'));
        return $pdf->stream('Neraca-' . $asOfDate . '.pdf');
    }

    public function bukuBesar(Request $request)
    {
        $accountId = $request->query('account_id');
        $startDate = $request->query('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString());

        $account = Account::findOrFail($accountId);

        // Saldo awal
        $opening = \App\Models\JournalEntryLine::where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->whereDate('date', '<', $startDate))
            ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
            ->first();
        $openingDebit = (float) ($opening->debit ?? 0);
        $openingCredit = (float) ($opening->credit ?? 0);
        if (in_array($account->type, ['asset', 'expense'])) {
            $openingBalance = (float) $account->initial_balance + $openingDebit - $openingCredit;
        } else {
            $openingBalance = (float) $account->initial_balance + $openingCredit - $openingDebit;
        }

        $lines = \App\Models\JournalEntryLine::where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$startDate, $endDate]))
            ->with('journalEntry')
            ->get()
            ->sortBy(fn ($line) => $line->journalEntry->date);

        $pdf = Pdf::loadView('pdf.buku-besar', compact('account', 'lines', 'openingBalance', 'startDate', 'endDate'));
        return $pdf->stream('Buku-Besar-' . $account->code . '.pdf');
    }

    public function arusKas(Request $request)
    {
        $startDate = $request->query('start_date', \Carbon\Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->query('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString());

        $cashBalanceUntil = function (string $asOfDate): float {
            $cashAccounts = Account::where('type', 'asset')->whereIn('code', ['111', '112'])->get();
            $total = 0;
            foreach ($cashAccounts as $account) {
                $lines = \App\Models\JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', fn ($q) => $q->whereDate('date', '<=', $asOfDate))
                    ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                    ->first();
                $total += (float) $account->initial_balance + (float) ($lines->debit ?? 0) - (float) ($lines->credit ?? 0);
            }
            return $total;
        };

        $openingCash = $cashBalanceUntil(\Carbon\Carbon::parse($startDate)->subDay()->toDateString());
        $closingCash = $cashBalanceUntil($endDate);

        $accountFlow = function (Account $account) use ($startDate, $endDate): float {
            $lines = \App\Models\JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$startDate, $endDate]))
                ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                ->first();
            $debit = (float) ($lines->debit ?? 0);
            $credit = (float) ($lines->credit ?? 0);
            if (in_array($account->type, ['asset', 'expense'])) {
                return $debit - $credit;
            }
            return $credit - $debit;
        };

        // Operasi
        $operatingDetails = collect();
        $operatingFlow = 0;
        foreach (Account::where('type', 'revenue')->get() as $account) {
            $flow = $accountFlow($account);
            if (abs($flow) > 0) {
                $operatingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $operatingFlow += $flow;
            }
        }
        foreach (Account::where('type', 'expense')->get() as $account) {
            $flow = $accountFlow($account);
            if (abs($flow) > 0) {
                $operatingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $operatingFlow += $flow;
            }
        }
        foreach (Account::whereIn('code', ['113', '114', '211', '212'])->get() as $account) {
            $flow = $accountFlow($account);
            if (in_array($account->type, ['asset'])) {
                $flow = -$flow;
            }
            if (abs($flow) > 0) {
                $operatingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $operatingFlow += $flow;
            }
        }

        // Pendanaan
        $financingDetails = collect();
        $financingFlow = 0;
        foreach (Account::where('type', 'equity')->get() as $account) {
            $flow = $accountFlow($account);
            if (abs($flow) > 0) {
                $financingDetails->push(['name' => $account->name, 'code' => $account->code, 'amount' => $flow]);
                $financingFlow += $flow;
            }
        }

        $netChange = $operatingFlow + $financingFlow;

        $pdf = Pdf::loadView('pdf.arus-kas', compact('openingCash', 'closingCash', 'operatingFlow', 'financingFlow', 'netChange', 'operatingDetails', 'financingDetails', 'startDate', 'endDate'));
        return $pdf->stream('Arus-Kas-' . $startDate . '.pdf');
    }
}

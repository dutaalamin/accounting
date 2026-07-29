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
}

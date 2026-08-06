<?php

namespace App\Services;

use App\Models\Account;
use App\Models\CustomerInvoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SupplierInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk auto-generate JournalEntry dari Invoice.
 *
 * Konvensi akun (berdasarkan COASeeder):
 *  - 113 Piutang Usaha (asset)
 *  - 211 Utang Usaha (liability)
 *  - 411/412 Pendapatan (revenue)
 *  - 511-514 Beban (expense) -> untuk pembelian, kita pakai akun beban material
 *  - 111/112 Kas/Bank (asset)
 *
 * Jurnal default:
 *  Customer Invoice (unpaid):  Dr Piutang Usaha | Cr Pendapatan
 *  Customer Invoice (paid):    Dr Kas/Bank      | Cr Pendapatan
 *  Supplier Invoice (unpaid):  Dr Beban Pembelian | Cr Utang Usaha
 *  Supplier Invoice (paid):    Dr Beban Pembelian | Cr Kas/Bank
 */
class JournalPostingService
{
    /** Akun default berdasarkan kode COA. Bisa di-override via env. */
    protected function accountByCode(string $code): ?Account
    {
        return Account::where('code', $code)->first();
    }

    protected function firstAssetCashAccount(): ?Account
    {
        // Prioritas: Kas Proyek (111), lalu Bank BCA (112)
        return $this->accountByCode('111') ?? $this->accountByCode('112')
            ?? Account::where('type', 'asset')->first();
    }

    protected function firstRevenueAccount(): ?Account
    {
        return $this->accountByCode('411') ?? $this->accountByCode('412')
            ?? Account::where('type', 'revenue')->first();
    }

    protected function firstExpenseAccount(): ?Account
    {
        return $this->accountByCode('511') ?? $this->accountByCode('512')
            ?? Account::where('type', 'expense')->first();
    }

    protected function arAccount(): ?Account
    {
        return $this->accountByCode('113') ?? Account::where('type', 'asset')->where('name', 'like', '%Piutang%')->first();
    }

    protected function apAccount(): ?Account
    {
        return $this->accountByCode('211') ?? Account::where('type', 'liability')->first();
    }

    /**
     * Generate / update jurnal untuk CustomerInvoice.
     */
    public function postCustomerInvoice(CustomerInvoice $invoice): ?JournalEntry
    {
        $cash = $this->firstAssetCashAccount();
        $revenue = $this->firstRevenueAccount();
        $ar = $this->arAccount();

        if (! $revenue || ! $cash || ! $ar) {
            Log::warning('JournalPostingService: akun default belum lengkap (revenue/cash/AR).');
            return null;
        }

        return DB::transaction(function () use ($invoice, $cash, $revenue, $ar) {
            // Hapus jurnal lama yang terkait (jika ada) untuk di-regenerate
            $this->deleteExistingJournal('customer_invoice', $invoice->id);

            $subtotal = (float) $invoice->lines()->sum('subtotal');
            $taxAmount = (float) $invoice->tax_amount;
            $total = (float) $invoice->total_amount;

            $journal = JournalEntry::create([
                'reference_number' => 'INV-CUST-' . $invoice->invoice_number,
                'date' => $invoice->invoice_date,
                'description' => 'Penjualan ke ' . ($invoice->customer->name ?? 'Pelanggan') . ' (' . $invoice->invoice_number . ')',
                'source_type' => 'customer_invoice',
                'source_id' => $invoice->id,
                'is_posted' => true,
                'posted_at' => now(),
            ]);

            // Debit: Piutang (jika unpaid) atau Kas (jika paid)
            $debitAccount = $invoice->status === 'paid' ? $cash : $ar;
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $debitAccount->id,
                'debit' => $total,
                'credit' => 0,
                'description' => 'Total tagihan ' . $invoice->invoice_number,
            ]);

            // Kredit: Pendapatan (subtotal)
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $revenue->id,
                'debit' => 0,
                'credit' => $subtotal,
                'description' => 'Pendapatan penjualan',
            ]);

            // Kredit: PPN keluaran (jika ada pajak)
            if ($taxAmount > 0) {
                $taxAccount = Account::where('type', 'liability')->where('name', 'like', '%PPN%')->first()
                    ?? $this->apAccount();
                if ($taxAccount) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $journal->id,
                        'account_id' => $taxAccount->id,
                        'debit' => 0,
                        'credit' => $taxAmount,
                        'description' => 'PPN Keluaran ' . $invoice->tax_percentage . '%',
                    ]);
                }
            }

            return $journal;
        });
    }

    /**
     * Generate / update jurnal untuk SupplierInvoice.
     */
    public function postSupplierInvoice(SupplierInvoice $invoice): ?JournalEntry
    {
        $cash = $this->firstAssetCashAccount();
        $expense = $this->firstExpenseAccount();
        $ap = $this->apAccount();

        if (! $expense || ! $cash || ! $ap) {
            Log::warning('JournalPostingService: akun default belum lengkap (expense/cash/AP).');
            return null;
        }

        return DB::transaction(function () use ($invoice, $cash, $expense, $ap) {
            $this->deleteExistingJournal('supplier_invoice', $invoice->id);

            $subtotal = (float) $invoice->lines()->sum('subtotal');
            $taxAmount = (float) $invoice->tax_amount;
            $total = (float) $invoice->total_amount;

            $journal = JournalEntry::create([
                'reference_number' => 'INV-SUPP-' . $invoice->invoice_number,
                'date' => $invoice->invoice_date,
                'description' => 'Pembelian dari ' . ($invoice->vendor->name ?? 'Pemasok') . ' (' . $invoice->invoice_number . ')',
                'source_type' => 'supplier_invoice',
                'source_id' => $invoice->id,
                'is_posted' => true,
                'posted_at' => now(),
            ]);

            // Debit: Beban Pembelian (subtotal)
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $expense->id,
                'debit' => $subtotal,
                'credit' => 0,
                'description' => 'Pembelian barang',
            ]);

            // Debit: PPN masukan (jika ada pajak)
            if ($taxAmount > 0) {
                $taxAccount = Account::where('type', 'asset')->where('name', 'like', '%PPN%')->first()
                    ?? $cash;
                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $taxAccount->id,
                    'debit' => $taxAmount,
                    'credit' => 0,
                    'description' => 'PPN Masukan ' . $invoice->tax_percentage . '%',
                ]);
            }

            // Kredit: Utang Usaha (jika unpaid) atau Kas (jika paid)
            $creditAccount = $invoice->status === 'paid' ? $cash : $ap;
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $creditAccount->id,
                'debit' => 0,
                'credit' => $total,
                'description' => 'Total tagihan ' . $invoice->invoice_number,
            ]);

            return $journal;
        });
    }

    /**
     * Hapus jurnal yang terkait dengan source tertentu (untuk regenerate).
     */
    public function deleteExistingJournal(string $sourceType, int $sourceId): void
    {
        $journals = JournalEntry::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->get();

        foreach ($journals as $journal) {
            $journal->lines()->delete();
            $journal->delete();
        }
    }
}

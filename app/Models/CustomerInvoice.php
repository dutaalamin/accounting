<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        // Setelah invoice disimpan (create/update), auto-generate jurnal akuntansi.
        static::saved(function ($invoice) {
            // Pastikan totals sudah ter-recalc sebelum posting jurnal.
            $invoice->recalculateTotals();
            $invoice->refresh();

            try {
                app(\App\Services\JournalPostingService::class)->postCustomerInvoice($invoice);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal posting jurnal customer invoice: ' . $e->getMessage());
            }
        });

        // Saat invoice dihapus, hapus juga jurnal terkait.
        static::deleted(function ($invoice) {
            app(\App\Services\JournalPostingService::class)->deleteExistingJournal('customer_invoice', $invoice->id);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lines()
    {
        return $this->hasMany(CustomerInvoiceLine::class);
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'source_id')
            ->where('source_type', 'customer_invoice');
    }

    public function recalculateTotals()
    {
        $subtotal = (float) $this->lines()->sum('subtotal');
        $taxAmount = $subtotal * ((float) $this->tax_percentage / 100);
        $totalAmount = $subtotal + $taxAmount;

        // Update via model agar timestamps & events tetap jalan
        $this->update([
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }
}

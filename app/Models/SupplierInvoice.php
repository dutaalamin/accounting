<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        // Setelah invoice disimpan (create/update), auto-generate jurnal akuntansi.
        static::saved(function ($invoice) {
            $invoice->recalculateTotals();
            $invoice->refresh();

            try {
                app(\App\Services\JournalPostingService::class)->postSupplierInvoice($invoice);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal posting jurnal supplier invoice: ' . $e->getMessage());
            }
        });

        // Saat invoice dihapus, hapus juga jurnal terkait.
        static::deleted(function ($invoice) {
            app(\App\Services\JournalPostingService::class)->deleteExistingJournal('supplier_invoice', $invoice->id);
        });
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lines()
    {
        return $this->hasMany(SupplierInvoiceLine::class);
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'source_id')
            ->where('source_type', 'supplier_invoice');
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

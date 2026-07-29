<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lines()
    {
        return $this->hasMany(SupplierInvoiceLine::class);
    }

    public function recalculateTotals()
    {
        $subtotal = $this->lines()->sum('subtotal');
        $taxAmount = $subtotal * ($this->tax_percentage / 100);
        $totalAmount = $subtotal + $taxAmount;
        
        \DB::table('supplier_invoices')->where('id', $this->id)->update([
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);
    }
}

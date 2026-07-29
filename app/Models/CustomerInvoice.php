<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lines()
    {
        return $this->hasMany(CustomerInvoiceLine::class);
    }

    public function recalculateTotals()
    {
        $subtotal = $this->lines()->sum('subtotal');
        $taxAmount = $subtotal * ($this->tax_percentage / 100);
        $totalAmount = $subtotal + $taxAmount;
        
        \DB::table('customer_invoices')->where('id', $this->id)->update([
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);
    }
}

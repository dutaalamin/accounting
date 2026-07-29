<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoiceLine extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customerInvoice()
    {
        return $this->belongsTo(CustomerInvoice::class);
    }

    protected static function booted()
    {
        static::saving(function ($line) {
            $line->subtotal = $line->quantity * $line->unit_price;
        });

        static::saved(function ($line) {
            if ($line->customerInvoice) {
                $line->customerInvoice->recalculateTotals();
            }
        });

        static::deleted(function ($line) {
            if ($line->customerInvoice) {
                $line->customerInvoice->recalculateTotals();
            }
        });

        static::created(function ($line) {
            if ($line->product_id) {
                $product = Product::find($line->product_id);
                if ($product) {
                    $product->stock -= $line->quantity;
                    $product->save();
                }
            }
        });

        static::updated(function ($line) {
            if ($line->product_id) {
                $oldQuantity = $line->getOriginal('quantity');
                $newQuantity = $line->quantity;
                
                $product = Product::find($line->product_id);
                if ($product) {
                    $product->stock += $oldQuantity; // revert old
                    $product->stock -= $newQuantity; // apply new
                    $product->save();
                }
            }
        });

        static::deleted(function ($line) {
            if ($line->product_id) {
                $product = Product::find($line->product_id);
                if ($product) {
                    $product->stock += $line->quantity;
                    $product->save();
                }
            }
        });
    }
}

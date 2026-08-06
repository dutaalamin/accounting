<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoiceLine extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplierInvoice()
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    protected static function booted()
    {
        static::saving(function ($line) {
            $line->subtotal = ($line->quantity ?? 0) * ($line->unit_price ?? 0);
        });

        static::saved(function ($line) {
            if ($line->supplierInvoice) {
                $line->supplierInvoice->recalculateTotals();
            }
        });

        // Saat baris invoice pemasok dibuat: tambah stok produk
        static::created(function ($line) {
            if ($line->product_id) {
                \DB::transaction(function () use ($line) {
                    $product = Product::lockForUpdate()->find($line->product_id);
                    if ($product) {
                        $product->stock += $line->quantity;
                        $product->save();
                    }
                });
            }
        });

        // Saat baris invoice pemasok diupdate: kembalikan stok lama, tambah stok baru
        static::updated(function ($line) {
            $oldProductId = $line->getOriginal('product_id');
            $newProductId = $line->product_id;
            $oldQuantity = $line->getOriginal('quantity');
            $newQuantity = $line->quantity;

            \DB::transaction(function () use ($oldProductId, $newProductId, $oldQuantity, $newQuantity) {
                // Kembalikan stok produk lama (kurangi karena pembelian lama dibatalkan)
                if ($oldProductId) {
                    $oldProduct = Product::lockForUpdate()->find($oldProductId);
                    if ($oldProduct) {
                        $oldProduct->stock -= $oldQuantity;
                        $oldProduct->save();
                    }
                }
                // Tambah stok produk baru
                if ($newProductId) {
                    $newProduct = Product::lockForUpdate()->find($newProductId);
                    if ($newProduct) {
                        $newProduct->stock += $newQuantity;
                        $newProduct->save();
                    }
                }
            });
        });

        // Saat baris invoice pemasok dihapus: kurangi stok + recalc total
        static::deleted(function ($line) {
            if ($line->product_id) {
                \DB::transaction(function () use ($line) {
                    $product = Product::lockForUpdate()->find($line->product_id);
                    if ($product) {
                        $product->stock -= $line->quantity;
                        $product->save();
                    }
                });
            }
            if ($line->supplierInvoice) {
                $line->supplierInvoice->recalculateTotals();
            }
        });
    }
}

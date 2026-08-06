<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return 'Cache cleared! <a href="/admin">Go to Admin</a>';
});

Route::get('/customer-invoice/{customerInvoice}/pdf', [\App\Http\Controllers\PdfController::class, 'customerInvoice'])->name('customer-invoice.pdf');
Route::get('/supplier-invoice/{supplierInvoice}/pdf', [\App\Http\Controllers\PdfController::class, 'supplierInvoice'])->name('supplier-invoice.pdf');
Route::get('/laba-rugi/pdf', [\App\Http\Controllers\PdfController::class, 'labaRugi'])->name('laba-rugi.pdf');
Route::get('/neraca/pdf', [\App\Http\Controllers\PdfController::class, 'neraca'])->name('neraca.pdf');
Route::get('/buku-besar/pdf', [\App\Http\Controllers\PdfController::class, 'bukuBesar'])->name('buku-besar.pdf');
Route::get('/arus-kas/pdf', [\App\Http\Controllers\PdfController::class, 'arusKas'])->name('arus-kas.pdf');

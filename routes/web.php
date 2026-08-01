<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return 'Cache cleared! <a href="/admin">Go to Admin</a>';
});

Route::get('/setup-db', function () {
    try {
        $output = '';
        
        // 1. Run migrations
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh --force');
        $output .= 'Migrations (fresh): ' . nl2br(\Illuminate\Support\Facades\Artisan::output()) . '<br>';
        
        // 2. Run seeders
        \Illuminate\Support\Facades\Artisan::call('db:seed --force');
        $output .= 'Seeding: ' . nl2br(\Illuminate\Support\Facades\Artisan::output()) . '<br>';
        
        // 3. Clear cache
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $output .= 'Cache: ' . nl2br(\Illuminate\Support\Facades\Artisan::output()) . '<br>';
        
        return 'Database setup completed successfully!<br><br>' . $output . '<br><a href="/admin">Go to Admin</a>';
    } catch (\Throwable $e) {
        return 'Error during setup: ' . $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
    }
});

Route::get('/customer-invoice/{customerInvoice}/pdf', [\App\Http\Controllers\PdfController::class, 'customerInvoice'])->name('customer-invoice.pdf');
Route::get('/supplier-invoice/{supplierInvoice}/pdf', [\App\Http\Controllers\PdfController::class, 'supplierInvoice'])->name('supplier-invoice.pdf');
Route::get('/laba-rugi/pdf', [\App\Http\Controllers\PdfController::class, 'labaRugi'])->name('laba-rugi.pdf');

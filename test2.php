<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function testSchema($class) {
    try {
        $resource = new $class();
        $livewire = new class extends \Livewire\Component implements \Filament\Forms\Contracts\HasForms {
            use \Filament\Forms\Concerns\InteractsWithForms;
        };
        $form = $resource::form(new \Filament\Forms\Form($livewire));
        $components = $form->getComponents();
        echo $class . " - SUCCESS\n";
    } catch (\Throwable $e) {
        echo $class . " - ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    }
}

testSchema(\App\Filament\Resources\ProductResource::class);
testSchema(\App\Filament\Resources\CustomerInvoiceResource::class);
testSchema(\App\Filament\Resources\SupplierInvoiceResource::class);
testSchema(\App\Filament\Resources\JournalEntryResource::class);
testSchema(\App\Filament\Resources\AccountResource::class);

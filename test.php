<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $resource = new \App\Filament\Resources\ProductResource();
    $form = $resource::form(new \Filament\Forms\Form(new class extends \Livewire\Component {}));
    echo "Success!";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

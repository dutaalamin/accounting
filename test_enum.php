<?php
require __DIR__.'/vendor/autoload.php';
foreach(\Filament\Support\Enums\MaxWidth::cases() as $c) {
    echo $c->name . ' = ' . $c->value . "\n";
}

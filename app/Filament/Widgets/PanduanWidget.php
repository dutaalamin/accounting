<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PanduanWidget extends Widget
{
    protected static string $view = 'filament.widgets.panduan-widget';
    
    // Pastikan widget ini tampil paling atas
    protected static ?int $sort = 0; 
    
    // Buat widget memanjang memenuhi layar (full width)
    protected int | string | array $columnSpan = 'full';
}

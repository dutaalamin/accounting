<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->size(\Filament\Support\Enums\ActionSize::ExtraLarge)
                ->extraAttributes(['class' => 'px-6 py-3 text-xl font-bold']),
        ];
    }
}

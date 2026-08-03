<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->size(\Filament\Support\Enums\ActionSize::ExtraLarge)
                ->extraAttributes(['class' => 'px-6 py-3 text-xl font-bold']),
        ];
    }
}


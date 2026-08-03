<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->size(\Filament\Support\Enums\ActionSize::ExtraLarge)
                ->extraAttributes(['class' => 'px-6 py-3 text-xl font-bold']),
        ];
    }
}


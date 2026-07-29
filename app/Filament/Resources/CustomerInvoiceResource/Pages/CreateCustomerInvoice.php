<?php

namespace App\Filament\Resources\CustomerInvoiceResource\Pages;

use App\Filament\Resources\CustomerInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerInvoice extends CreateRecord
{

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('kembali')
                ->label('Kembali')
                ->url(fn (): string => static::getResource()::getUrl('index'))
                ->color('gray')
                ->size('sm'),
        ];
    }

    protected static string $resource = CustomerInvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

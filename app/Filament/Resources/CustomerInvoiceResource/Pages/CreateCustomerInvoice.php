<?php

namespace App\Filament\Resources\CustomerInvoiceResource\Pages;

use App\Filament\Resources\CustomerInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerInvoice extends CreateRecord
{
    protected static string $resource = CustomerInvoiceResource::class;
}

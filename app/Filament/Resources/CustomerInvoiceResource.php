<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerInvoiceResource\Pages;
use App\Models\CustomerInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerInvoiceResource extends Resource
{
    protected static ?string $model = CustomerInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationGroup = 'Piutang Usaha (AR)';
    protected static ?string $modelLabel = 'Tagihan Pelanggan';
    protected static ?string $pluralModelLabel = 'Tagihan Pelanggan (AR)';
    protected static ?string $navigationLabel = 'Tagihan Pelanggan (AR)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Pilih Pelanggan')
                    ->relationship('customer', 'name')
                    ->required(),
                Forms\Components\TextInput::make('invoice_number')
                    ->label('Nomor Faktur')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('invoice_date')
                    ->label('Tanggal Faktur')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Jatuh Tempo (Opsional)'),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Total Tagihan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid' => 'Belum Lunas (Unpaid)',
                        'paid' => 'Lunas (Paid)',
                    ])
                    ->default('unpaid')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Faktur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerInvoices::route('/'),
            'create' => Pages\CreateCustomerInvoice::route('/create'),
            'edit' => Pages\EditCustomerInvoice::route('/{record}/edit'),
        ];
    }
}

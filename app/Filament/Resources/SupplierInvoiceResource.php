<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierInvoiceResource\Pages;
use App\Models\SupplierInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierInvoiceResource extends Resource
{
    protected static ?string $model = SupplierInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-minus';
    protected static ?string $navigationGroup = 'Hutang Usaha (AP)';
    protected static ?string $modelLabel = 'Tagihan Pemasok';
    protected static ?string $pluralModelLabel = 'Tagihan Pemasok (AP)';
    protected static ?string $navigationLabel = 'Tagihan Pemasok (AP)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vendor_id')
                    ->label('Pilih Pemasok')
                    ->relationship('vendor', 'name')
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
                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Pemasok')
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
            'index' => Pages\ListSupplierInvoices::route('/'),
            'create' => Pages\CreateSupplierInvoice::route('/create'),
            'edit' => Pages\EditSupplierInvoice::route('/{record}/edit'),
        ];
    }
}

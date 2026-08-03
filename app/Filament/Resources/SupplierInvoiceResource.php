<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierInvoiceResource\Pages;
use App\Models\SupplierInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->label('Pilih Pemasok (Vendor)')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Nomor Faktur Pemasok')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('invoice_date')
                            ->label('Tanggal Faktur')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Jatuh Tempo (Opsional)')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'unpaid' => 'Belum Lunas (Unpaid)',
                                'paid' => 'Lunas (Paid)',
                            ])
                            ->default('unpaid')
                            ->required(),
                        Forms\Components\TextInput::make('tax_percentage')
                            ->label('Pajak (PPN %)')
                            ->numeric()
                            ->placeholder('0')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Rincian Barang')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->relationship()
                            ->label('Daftar Barang yang Dibeli')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Pilih Produk')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->price);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('description')
                                    ->label('Keterangan')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Jumlah (Qty)')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->required(),
                            ])
                            ->columns(4)
                    ]),

                Forms\Components\Section::make('Total Tagihan')
                    ->description('Total dan Pajak akan dihitung secara otomatis setelah tagihan disimpan.')
                    ->schema([
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Total Pajak (Dihitung Otomatis)')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Akhir (Dihitung Otomatis)')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                    ])->columns(2)
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
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'Lunas (Paid)',
                        'unpaid' => 'Belum Lunas (Unpaid)',
                    ]),
                Tables\Filters\Filter::make('invoice_date')
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('date_from')
                                ->label('Dari Tanggal')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->default(now()->startOfMonth()),
                            Forms\Components\DatePicker::make('date_until')
                                ->label('Sampai Tanggal')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->default(now()->endOfMonth()),
                        ])
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '<=', $date),
                            );
                    })
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('Cetak PDF')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->url(fn (SupplierInvoice $record) => route('supplier-invoice.pdf', $record))
                    ->openUrlInNewTab(),
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


<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventaris / Gudang';
    protected static ?string $modelLabel = 'Barang / Produk';
    protected static ?string $pluralModelLabel = 'Daftar Barang & Produk';
    protected static ?string $navigationLabel = 'Daftar Barang & Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->label('Kode Barang (SKU)')
                    ->helperText('Misal: BRG-001')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->label('Harga Jual')
                    ->helperText('Ketik angka tanpa koma/titik')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\TextInput::make('stock')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan Singkat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

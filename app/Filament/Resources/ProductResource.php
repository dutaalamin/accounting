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

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Layanan / Item';
    protected static ?string $pluralModelLabel = 'Katalog Layanan & Item';
    protected static ?string $navigationLabel = 'Katalog Layanan & Item';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->label('Kode (Opsional)')
                    ->helperText('Misal: JASA-001')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Layanan / Item')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->label('Tarif / Harga Dasar')
                    ->helperText('Otomatis diberi format titik')
                    ->prefix('Rp')
                    ->numeric()
                    ->placeholder('0')
                    ->required(),
                Forms\Components\TextInput::make('stock')
                    ->label('Stok (Abaikan jika berupa Jasa)')
                    ->numeric()
                    ->placeholder('0')
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
                    ->label('Nama Layanan / Item')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Tarif / Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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


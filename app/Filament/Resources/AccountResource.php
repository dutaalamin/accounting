<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Buku Besar';
    protected static ?string $modelLabel = 'Akun / Dompet';
    protected static ?string $pluralModelLabel = 'Daftar Akun / Dompet';
    protected static ?string $navigationLabel = 'Daftar Akun / Dompet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Akun')
                    ->helperText('Contoh: 101, 102. Gunakan angka.')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Akun / Dompet')
                    ->helperText('Misal: Kas Kecil, Rekening BCA, Modal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Kategori (Tipe)')
                    ->helperText('Pilih Asset (Untuk Kas/Harta) atau Expense (Pengeluaran)')
                    ->options([
                        'asset' => 'Asset (Harta / Kas)',
                        'liability' => 'Liability (Hutang)',
                        'equity' => 'Equity (Modal)',
                        'revenue' => 'Revenue (Pemasukan)',
                        'expense' => 'Expense (Pengeluaran)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('initial_balance')
                    ->label('Saldo Awal')
                    ->helperText('Otomatis diberi format titik')
                    ->prefix('Rp')
                    ->numeric()
                    ->placeholder('0')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Akun / Dompet')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'asset' => 'Harta / Kas',
                        'liability' => 'Hutang',
                        'equity' => 'Modal',
                        'revenue' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'asset' => 'success',
                        'liability' => 'danger',
                        'equity' => 'info',
                        'revenue' => 'success',
                        'expense' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('initial_balance')
                    ->label('Saldo Awal')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_balance')
                    ->label('Saldo Saat Ini')
                    ->money('IDR')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Account $record) => $record->journalEntryLines()->exists()),
            ])
            ->bulkActions([
                // Menonaktifkan hapus massal untuk mencegah penghapusan akun ber-history
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\JournalEntryLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}


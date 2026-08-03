<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Filament\Resources\JournalEntryResource\RelationManagers;
use App\Models\JournalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Buku Besar';
    protected static ?string $modelLabel = 'Catatan Transaksi';
    protected static ?string $pluralModelLabel = 'Catat Transaksi Harian';
    protected static ?string $navigationLabel = 'Catat Transaksi Harian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->description('Isi data umum transaksi ini')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Nomor Bukti / Referensi')
                            ->helperText('Misal: INV-001, KAS-001, atau Nota-123')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal Transaksi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan / Deskripsi Umum')
                            ->helperText('Contoh: Beli perlengkapan kantor dari Toko ABC')
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Rincian Keluar/Masuk Uang')
                    ->description('Pastikan total Uang Masuk (Debit) sama dengan total Uang Keluar (Kredit)')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->label('Daftar Rincian')
                            ->relationship()
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    $totalDebit = collect($value)->sum('debit');
                                    $totalCredit = collect($value)->sum('credit');
                                    
                                    if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                                        $fail('Transaksi gagal disimpan! Total Uang Masuk (Debit) dan Total Uang Keluar (Kredit) harus memiliki jumlah yang sama persis (Balance).');
                                    }
                                };
                            })
                            ->schema([
                                Forms\Components\Select::make('account_id')
                                    ->label('Pilih Akun / Dompet')
                                    ->relationship('account', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('description')
                                    ->label('Keterangan Detail')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('debit')
                                    ->label('Uang Masuk (Debit)')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->required(),
                                Forms\Components\TextInput::make('credit')
                                    ->label('Uang Keluar (Kredit)')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Nomor Bukti')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Transaksi')
                    ->money('IDR')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
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
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // 
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }
}


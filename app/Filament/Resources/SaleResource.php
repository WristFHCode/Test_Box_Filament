<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Sale;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\SaleResource\Pages;
use Filament\Notifications\Notification;
use Closure;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Selling Report TIKI';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tanggal Penjualan')
                    ->schema([
                        DatePicker::make('tanggal_penjualan')
                            ->label('Tanggal Penjualan')
                            ->required()
                            // Validasi agar tidak lebih dari hari ini
                            ->maxDate(now()->format('Y-m-d'))
                            // Validasi agar tidak ada duplikasi tanggal
                            ->afterStateUpdated(function ($state, $set) {
                                // Cek apakah tanggal sudah ada
                                $existingSale = Sale::where('tanggal_penjualan', $state)->first();
                                if ($existingSale) {
                                    $set('tanggal_penjualan', null);  // Reset jika tanggal sudah ada
                                    // Menampilkan pesan kesalahan menggunakan Filament Notification
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Tanggal Penjualan sudah terdaftar.')
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ]),  
                Section::make('Totals')
                    ->schema([
                        TextInput::make('nominal_penjualan')
                            ->numeric(),
                    ]),             
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_penjualan')
                ->label('Tanggal Penjualan')
                ->formatStateUsing(function ($state) {
                    // Format tanggal sesuai yang diinginkan, misalnya 'd-m-Y'
                    return \Carbon\Carbon::parse($state)->format('d-m-Y');
                }),
            
            TextColumn::make('nominal_penjualan')
                ->label('Nominal Penjualan')
                ->formatStateUsing(function ($state) {
                    // Menghapus desimal jika tidak ada nilai setelah koma
                    $formatted = (int)$state === $state
                        ? number_format($state, 0, ',', '.')  // Format tanpa desimal
                        : number_format($state, 2, ',', '.'); // Format dengan dua desimal
                    return 'IDR ' . $formatted;  // Tambahkan IDR di depan
                })
                ->sortable(),
            
            
            
            ])
            ->filters([ 
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),  // Menambahkan aksi View
                Tables\Actions\DeleteAction::make(),  // Menambahkan aksi Delete
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan relasi jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}

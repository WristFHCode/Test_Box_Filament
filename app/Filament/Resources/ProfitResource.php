<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Sale;
use Filament\Tables;
use App\Models\Profit;
use App\Models\Deposit;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\ProfitResource\Pages;

class ProfitResource extends Resource
{
    protected static ?string $model = Profit::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-currency-dollar';

    protected static ?string $navigationLabel = 'Keungan TIKI';


    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Tanggal Penjualan')
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Keuntungan')
                        ->required()
                        ->reactive() // Reaktif agar bisa memicu perubahan data saat tanggal dipilih
                        ->afterStateUpdated(function (callable $set, $state) {
                            // Mengambil total penjualan berdasarkan tanggal yang dipilih
                            $totalPenjualan = Sale::whereDate('tanggal_penjualan', $state)->sum('nominal_penjualan');
                            $set('total_penjualan', $totalPenjualan);

                            // Mengambil total setoran berdasarkan tanggal yang dipilih
                            $totalSetoran = Deposit::whereDate('tanggal_setoran', $state)->sum('nominal_setoran');
                            $set('total_setoran', $totalSetoran);

                            // Menghitung keuntungan
                            $keuntungan = $totalPenjualan - $totalSetoran;
                            $set('keuntungan', $keuntungan); // Pastikan kolom ini sesuai dengan field di database
                        }),
                ]),  

                Section::make('Detail Keuangan')
                ->schema([
                    Forms\Components\TextInput::make('total_penjualan')
                        ->label('Total Penjualan')
                        ->disabled(), // Agar tidak bisa diubah secara manual

                    Forms\Components\TextInput::make('total_setoran')
                        ->label('Total Setoran')
                        ->disabled(), // Agar tidak bisa diubah secara manual
                ]),

                Section::make('Keuntungan')
                ->schema([
                    Forms\Components\TextInput::make('keuntungan')
                        ->label('Keuntungan')
                        ->readOnly(), // Agar tidak bisa diubah secara manual
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Keuntungan')
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('keuntungan')
                    ->label('Keuntungan')
                    ->sortable()
                    ->money('IDR'),
            ])
            ->actions([
                // Aksi View - Menggunakan modal untuk melihat data
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->modalHeading('Detail Keuntungan') // Judul Modal
                    ->modalWidth('lg') // Ukuran modal, Anda bisa menggunakan 'sm', 'md', 'lg', atau 'xl'
                    ->form([
                        TextInput::make('tanggal')
                            ->label('Tanggal Keuntungan')
                            ->disabled(),
                        TextInput::make('total_penjualan')
                            ->label('Total Penjualan')
                            ->disabled(),
                        TextInput::make('total_setoran')
                            ->label('Total Setoran')
                            ->disabled(),
                        TextInput::make('keuntungan')
                            ->label('Keuntungan')
                            ->disabled(),
                    ]),
                // Aksi Delete
                Tables\Actions\DeleteAction::make(),  
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfits::route('/'),
            'create' => Pages\CreateProfit::route('/create'),
            'edit' => Pages\EditProfit::route('/{record}/edit'),
        ];
    }
}

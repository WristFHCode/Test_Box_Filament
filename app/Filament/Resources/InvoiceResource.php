<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CardboardProduct;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Http\Controllers\InvoiceController;
use App\Filament\Resources\InvoiceResource\Pages;
use Filament\Tables\Actions\Action as TableAction;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Kardus';

    protected static ?string $navigationLabel = 'Nota | Transaksi';

    public static function form(Form $form): Form
    {
        $cardboardProducts = CardboardProduct::all();

        return $form
            ->schema([
                Section::make('Tanggal Penjualan')
                    ->schema([ 
                        Repeater::make('invoiceProducts')
                            ->relationship('invoiceProducts')
                            ->columns(4)
                            ->schema([ 
                                Select::make('cardboard_product_id')
                                    ->relationship('cardboardProduct', 'name')
                                    ->options(
                                        $cardboardProducts->mapWithKeys(function (CardboardProduct $product) {
                                            return [$product->id => sprintf('%s ($%s)', $product->name, $product->price)];
                                        })
                                    )
                                    ->searchable()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $pricePerUnit = optional(CardboardProduct::find($state))->price;
                                        $quantity = $get('quantity') ?? 1;
                                        $set('price_per_unit', $pricePerUnit);
                                        $set('total_price', $pricePerUnit * $quantity);
                                    }),
                                TextInput::make('quantity')
                                    ->integer()
                                    ->default(1)
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $pricePerUnit = $get('price_per_unit');
                                        $set('total_price', $pricePerUnit * $state);
                                    }),
                                TextInput::make('price_per_unit')
                                    ->label('Price per Unit')
                                    ->numeric()
                                    ->readOnly(),
                                TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->numeric()
                                    ->readOnly(),
                            ])
                            ->live()
                            ->afterStateUpdated(function ($get, $set) {
                                self::updateTotals($get, $set);
                            })
                            ->reorderable(false),
                    ]),

                Section::make('Total')
                    ->schema([ 
                        TextInput::make('nota')
                        ->label('Nomor Nota')
                        ->default(function() {
                            return self::generateNotaNumber();
                        })
                        ->readOnly()
                        ->required(),

                        TextInput::make('total_items')
                            ->label('Total Items')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                        TextInput::make('total')
                            ->numeric()
                            ->readOnly()
                            ->prefix('$'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Invoice')
                    ->date()
                    ->sortable()
                    ->searchable(),
            
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(function ($state) {
                        $formatted = (int)$state === $state
                            ? number_format($state, 0, ',', '.')
                            : number_format($state, 2, ',', '.');
                        return 'Rp ' . $formatted;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('nota')->label('Nomor Nota')->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Set default sort order here
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function updateTotals($get, $set): void
    {
        $selectedProducts = collect($get('invoiceProducts'))->filter(fn($item) => !empty($item['cardboard_product_id']) && !empty($item['quantity']));
        $prices = CardboardProduct::find($selectedProducts->pluck('cardboard_product_id'))->pluck('price', 'id');

        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['cardboard_product_id']] * $product['quantity']);
        }, 0);

        $totalItems = $selectedProducts->sum('quantity');
        
        $set('total_items', $totalItems);
        $set('total', number_format($total, 2, '.', ''));
    }

    public function printInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = PDF::loadView('invoices.print', compact('invoice'));
        return $pdf->stream('invoice_' . $id . '.pdf');
    }

    public static function toRoman($number)
    {
        $map = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I'
        ];

        $roman = '';
        foreach ($map as $value => $symbol) {
            while ($number >= $value) {
                $roman .= $symbol;
                $number -= $value;
            }
        }

        return $roman;
    }

    public static function generateNotaNumber()
    {
        $today = now()->toDateString();

        // Mengambil semua invoice yang pernah dibuat
        $allInvoices = Invoice::all(); 
        $totalInvoices = $allInvoices->count();

        // Pastikan minimal angka Romawi adalah I ketika tidak ada invoice
        $romanTotalInvoices = $totalInvoices === 0 ? 'I' : self::toRoman($totalInvoices + 1);

        // Mengambil urutan nota terbaru pada hari ini
        $todayInvoices = Invoice::whereDate('created_at', $today)->get();
        $lastInvoice = $todayInvoices->last();
        $lastNumber = $lastInvoice ? (int)substr($lastInvoice->nota, -3) : 0;

        // Gabungkan angka Romawi dengan urutan nota
        return $romanTotalInvoices . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }
}
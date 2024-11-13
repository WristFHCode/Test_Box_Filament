<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Sale;
use App\Models\Profit;
use App\Models\Deposit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class LatestRecord extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int|string|array $columnSpan = 'full';

    protected static int $maxRecords = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Union all tables with required columns
                $invoices = Invoice::query()
                    ->select([
                        'id',
                        // 'total_items  as reference', // Mengasumsikan nama kolom 'number'
                        'total as amount',     // Mengasumsikan nama kolom 'total'
                        'created_at',
                        \DB::raw('"Invoice" as type')
                    ]);

                $sales = Sale::query()
                    ->select([
                        'id',
                        // 'tanggal_penjualan as reference', // Mengasumsikan nama kolom 'number'
                        'nominal_penjualan as amount',     // Mengasumsikan nama kolom 'total'
                        'created_at',
                        \DB::raw('"Sale" as type')
                    ]);

                $profits = Profit::query()
                    ->select([
                        'id',
                        // 'tanggal as reference', // Mengasumsikan nama kolom 'number'
                        'keuntungan as amount',     // Mengasumsikan nama kolom 'total'
                        'created_at',
                        \DB::raw('"Profit" as type')
                    ]);

                $deposits = Deposit::query()
                    ->select([
                        'id',
                        // 'tanggal_setoran as reference', // Mengasumsikan nama kolom 'number'
                        'nominal_setoran as amount',     // Mengasumsikan nama kolom 'total'
                        'created_at',
                        \DB::raw('"Deposit" as type')
                    ]);

                return $invoices
                    ->union($sales)
                    ->union($profits)
                    ->union($deposits)
                    ->orderBy('created_at', 'desc')
                    ->limit(static::$maxRecords);
            })
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Invoice' => 'info',
                        'Sale' => 'success',
                        'Profit' => 'warning',
                        'Deposit' => 'primary',
                    }),
                // Tables\Columns\TextColumn::make('reference')
                //     ->searchable()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('idr'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
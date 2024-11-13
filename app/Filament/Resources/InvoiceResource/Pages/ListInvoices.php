<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\InvoiceResource\Widgets\JumlahTransaksi;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            JumlahTransaksi::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Today' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereDate('created_at', now()->toDateString())
                ),
            'Month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                ),
        ];
    }
    
}

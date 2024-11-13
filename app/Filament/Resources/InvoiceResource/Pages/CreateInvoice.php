<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Services\PrinterService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Log;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
    
    protected function afterCreate(): void
    {
        $invoice = $this->record;
        $printService = new PrinterService();

        try {
            // Print first receipt
            $printService->printFirstReceipt($invoice);

            // Show notification for second receipt
            Notification::make('print-second')
                ->title('Nota pertama telah dicetak')
                ->body('Silakan sobek nota pertama, kemudian klik tombol "Cetak Nota Kedua"')
                ->persistent() // Membuat notifikasi tetap ada
                ->actions([
                    Action::make('printSecond')
                        ->label('Cetak Nota Kedua')
                        ->button()
                        ->color('primary')
                        ->url(route('print.second', ['invoice' => $invoice->id]))
                ])
                ->success()
                ->persistent() // Menambahkan persistent() lagi untuk memastikan
                ->send();

        } catch (\Exception $e) {
            Log::error('Error saat mencetak nota pertama: ' . $e->getMessage());
            
            Notification::make('error')
                ->title('Gagal mencetak struk')
                ->danger()
                ->body($e->getMessage())
                ->persistent() // Membuat notifikasi error juga tetap ada
                ->send();
        }
    }
}
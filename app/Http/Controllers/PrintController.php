<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\PrinterService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PrintController extends Controller
{
    public function printSecond(Invoice $invoice)
    {
        try {
            Log::info('Memulai pencetakan nota kedua');
            
            $printService = new PrinterService();
            $printService->printSecondReceipt($invoice);
            
            Log::info('Nota kedua berhasil dicetak');
            
            Notification::make('success-print')
                ->title('Nota kedua telah dicetak')
                ->success()
                ->persistent()
                ->send();

            return redirect('/admin/invoices');
        } catch (\Exception $e) {
            Log::error('Error saat mencetak nota kedua: ' . $e->getMessage());
            
            Notification::make('error-print')
                ->title('Gagal mencetak nota kedua')
                ->danger()
                ->body($e->getMessage())
                ->persistent()
                ->send();

            return redirect('/admin/invoices');
        }
    }
}
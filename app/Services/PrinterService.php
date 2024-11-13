<?php

namespace App\Services;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PrinterService
{
    protected $printer;

    private function initializePrinter()
    {
        try {
            Log::info('Mencoba menginisialisasi printer');
            $connector = new WindowsPrintConnector('POS-58');
            $this->printer = new Printer($connector);
            Log::info('Printer berhasil diinisialisasi');
            return true;
        } catch (\Exception $e) {
            Log::error('Gagal inisialisasi printer: ' . $e->getMessage());
            throw $e;
        }
    }

    private function printReceiptContent(Invoice $invoice, bool $isFirst = true)
    {
        try {
            Log::info('Mulai mencetak nota: ' . ($isFirst ? 'ASLI' : 'COPY'));
            
            // Header
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text("TOKO KITA\n");
            $this->printer->text("Jl. Jalan Kemana-mana No. 123\n");
            $this->printer->text("Telp: 08123456789\n");
            $this->printer->text("--------------------------------\n");
            
            // Invoice Info
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text("Tanggal: " . $invoice->created_at->format('d/m/Y') . "\n");
            $this->printer->text("--------------------------------\n");
            
            // Keterangan Copy
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text($isFirst ? "STRUK CUSTOMER\n" : "STRUK TOKO\n");
            $this->printer->text("--------------------------------\n");

            // Items
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            
            $totalItems = 0; // Initialize total items counter

            foreach ($invoice->invoiceProducts as $item) {
                $productName = $item->cardboardProduct->name;
                $quantity = $item->quantity;
                $pricePerUnit = $item->cardboardProduct->price;
                $totalPrice = $pricePerUnit * $quantity;
                
                $totalItems += $quantity;

                // Product name
                $this->printer->text($productName . "\n");
                
                // Quantity, price per unit, and total
                $this->printer->text(
                    $quantity . ' x ' . 
                    number_format($pricePerUnit, 0, ',', '.') . ' = ' . 
                    number_format($totalPrice, 0, ',', '.') . "\n"
                );
            }

            // Footer totals
            $this->printer->text("--------------------------------\n");
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text("Total Items: " . $totalItems . "\n");
            
            // Menghitung total price
            $totalPrice = $invoice->invoiceProducts->sum(function($item) {
                return $item->quantity * $item->cardboardProduct->price;
            });
            
            $this->printer->text("Total: Rp. " . number_format($totalPrice, 0, ',', '.') . "\n");

            // Tambahkan total penjualan hari ini untuk nota kedua
            if (!$isFirst) {
                $todayTotal = $this->calculateTodayTotal();
                $this->printer->text("--------------------------------\n");
                $this->printer->text("Total Penjualan Hari Ini:\n");
                $this->printer->text("Rp. " . number_format($todayTotal, 0, ',', '.') . "\n");
            }

            $this->printer->text("--------------------------------\n");
            
            // Thank you message
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text("Terima Kasih Atas Kunjungan Anda\n");
            $this->printer->text("\n\n");

            $this->printer->cut();
            Log::info('Berhasil mencetak nota: ' . ($isFirst ? 'ASLI' : 'COPY'));
        } catch (\Exception $e) {
            Log::error('Error saat mencetak konten: ' . $e->getMessage());
            throw $e;
        }
    }

    private function calculateTodayTotal()
    {
        $today = Carbon::today();
        
        return Invoice::whereDate('created_at', $today)
            ->with('invoiceProducts.cardboardProduct')
            ->get()
            ->sum(function($invoice) {
                return $invoice->invoiceProducts->sum(function($item) {
                    return $item->quantity * $item->cardboardProduct->price;
                });
            });
    }

    public function printFirstReceipt(Invoice $invoice)
    {
        try {
            $this->initializePrinter();
            $this->printReceiptContent($invoice, true);
            $this->printer->close();
            Log::info('Nota customer berhasil dicetak');
            return true;
        } catch (\Exception $e) {
            Log::error('Error saat mencetak nota customer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function printSecondReceipt(Invoice $invoice)
    {
        try {
            $this->initializePrinter();
            $this->printReceiptContent($invoice, false);
            $this->printer->close();
            Log::info('Nota toko berhasil dicetak');
            return true;
        } catch (\Exception $e) {
            Log::error('Error saat mencetak nota toko: ' . $e->getMessage());
            throw $e;
        }
    }
}
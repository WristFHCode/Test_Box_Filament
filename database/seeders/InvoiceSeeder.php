<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use App\Models\CardboardProduct;
use App\Models\InvoiceProduct;
use App\Filament\Resources\InvoiceResource;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        // Memastikan ada CardboardProduct
        if (CardboardProduct::count() === 0) {
            // Buat beberapa CardboardProduct jika belum ada
            CardboardProduct::create(['name' => 'Kardus Kecil', 'price' => 5000]);
            CardboardProduct::create(['name' => 'Kardus Sedang', 'price' => 8000]);
            CardboardProduct::create(['name' => 'Kardus Besar', 'price' => 12000]);
            CardboardProduct::create(['name' => 'Kardus Extra Besar', 'price' => 15000]);
        }

        $products = CardboardProduct::all();

        // Generate 100 Invoice dengan tanggal berbeda
        for ($i = 0; $i < 100; $i++) {
            // Generate random date dalam 3 bulan terakhir
            $date = Carbon::now()->subDays(rand(0, 90));
            
            // Buat Invoice
            $invoice = new Invoice();
            $invoice->created_at = $date;
            $invoice->updated_at = $date;
            
            // Generate nomor nota
            $invoice->nota = InvoiceResource::generateNotaNumber();
            
            // Hitung total sementara
            $totalItems = 0;
            $totalAmount = 0;
            
            // Simpan invoice terlebih dahulu untuk mendapatkan ID
            $invoice->total_items = $totalItems;
            $invoice->total = $totalAmount;
            $invoice->save();
            
            // Generate 1-5 produk untuk setiap invoice
            $numberOfProducts = rand(1, 5);
            
            // Array untuk menyimpan detail produk
            $invoiceProducts = [];
            
            for ($j = 0; $j < $numberOfProducts; $j++) {
                $product = $products->random();
                $quantity = rand(1, 10);
                
                $invoiceProduct = new InvoiceProduct([
                    'invoice_id' => $invoice->id,
                    'cardboard_product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_per_unit' => $product->price,
                    'total_price' => $product->price * $quantity
                ]);
                
                $invoiceProduct->save();
                
                $totalItems += $quantity;
                $totalAmount += ($product->price * $quantity);
            }
            
            // Update invoice dengan total yang sebenarnya
            $invoice->total_items = $totalItems;
            $invoice->total = $totalAmount;
            $invoice->save();
        }
    }
}
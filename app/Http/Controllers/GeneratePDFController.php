<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Anda sudah menginstal dompdf

class GeneratePDFController extends Controller
{
    public function invoiceReport($id)
    {
        $invoice = Invoice::findOrFail($id); // Ambil data invoice berdasarkan ID
        $pdf = Pdf::loadView('pdf.invoice_pdf', compact('invoice')); // Muat view PDF

        return $pdf->stream('invoice_'.$id.'.pdf'); // Menghasilkan PDF di browser
    }
}

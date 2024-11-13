<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function print($id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = Pdf::loadView('invoices.print', compact('invoice'));
        
        // Menampilkan PDF langsung di browser
        return $pdf->stream('invoice_' . $invoice->id . '.pdf');
    }
}

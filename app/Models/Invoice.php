<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['subtotal', 'taxes', 'total', 'nota', 'total_items'];

    public function invoiceProducts()
    {
        return $this->hasMany(InvoiceProduct::class);
    }

    
    
}

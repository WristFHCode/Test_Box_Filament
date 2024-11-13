<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'invoice_id',
        'cardboard_product_id',
        'quantity',
        'price_per_unit',
        'total_price'
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function cardboardProduct()
    {
        return $this->belongsTo(CardboardProduct::class);
    }
}
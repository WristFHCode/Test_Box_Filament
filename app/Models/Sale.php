<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if table name is default 'sales')
    protected $table = 'sales';

    // Define the fillable fields (attributes that can be mass-assigned)
    protected $fillable = [
        'tanggal_penjualan',
        'nominal_penjualan',
    ];

    // Optional: If you want to cast date fields to Carbon instances
    protected $dates = ['tanggal_penjualan'];
}

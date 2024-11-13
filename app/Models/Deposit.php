<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory;

    // Define the table name (optional if table name is 'deposits')
    protected $table = 'deposits';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'tanggal_setoran',  // Date of the deposit
        'nominal_setoran',  // Amount of the deposit
    ];

    // Optional: If you want to cast the date field to a Carbon instance
    protected $dates = ['tanggal_setoran'];
}

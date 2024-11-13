<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintController;
use App\Filament\Resources\InvoiceResource;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\GeneratePDFController;




Route::get('/', function () {
    return redirect('/admin');
});


Route::get('/print/second/{invoice}', [PrintController::class, 'printSecond'])
    ->name('print.second');
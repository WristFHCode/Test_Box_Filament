<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf; // Menggunakan DomPDF untuk generate PDF

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;


}

<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class JumlahTransaksi extends BaseWidget
{

    protected function getStats(): array
    {
        return [
            // Stat::make('', $this->getGreetingMessage())
            //     ->description('')
            //     ->color('success')
            //     ->extraAttributes([
            //         'class' => 'flex justify-center items-center text-center',
            //         'style' => 'font-size: 1.25rem; font-weight: 600;'
            //     ]),
            
            Stat::make('Total Transaksi Hari Ini', $this->getTodayTransactionCount())
                ->descriptionIcon('heroicon-o-banknotes'),
            
            // Stat::make('Total Transaksi Kemarin', $this->getYesterdayTransactionCount())
            //     ->descriptionIcon('heroicon-o-banknotes'),
            
            // Stat::make('Total Transaksi Bulan Ini', $this->getMonthlyTransactionCount())
            //     ->descriptionIcon('heroicon-o-banknotes'),
            
            Stat::make('Total Transaksi Keseluruhan', $this->getTotalTransactionCount())
                ->descriptionIcon('heroicon-o-banknotes'),
            
            Stat::make('Total Transaksi Hari Ini (Rp)', $this->formatToRupiah($this->getTodayTransactionAmount()))
                ->descriptionIcon('heroicon-o-banknotes'),
            
            Stat::make('Total Transaksi Bulan Ini', $this->formatToRupiah($this->getMonthlyTransactionAmount()))
                ->descriptionIcon('heroicon-o-banknotes'),
        ];
    }

    protected function getTodayTransactionCount(): int
    {
        return Invoice::whereDate('created_at', today())->count();
    }

    protected function getYesterdayTransactionCount(): int
    {
        return Invoice::whereDate('created_at', today()->subDay())->count();
    }

    protected function getMonthlyTransactionCount(): int
    {
        // return Invoice::whereBetween('created_at', [
        //     now()->startOfMonth(),
        //     now()->endOfMonth()
        // ])->count();

        return Invoice::whereMonth('created_at', now()->month)->count();
    }

    protected function getTotalTransactionCount(): int
    {
        return Invoice::count();
        // Test 
    }

    protected function getTodayTransactionAmount(): float
    {
        return Invoice::whereDate('created_at', today())->sum('total');
    }

    protected function getMonthlyTransactionAmount(): float
    {
        return Invoice::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->sum('total');
    }

    protected function formatToRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    protected function getGreetingMessage(): string
    {
        $hour = now()->hour;
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Hai, Selamat Pagi',
            $hour >= 12 && $hour < 18 => 'Hai, Selamat Siang',
            $hour >= 18 && $hour < 21 => 'Hai, Selamat Sore',
            default => 'Hai, Selamat Malam',
        };

        return $greeting . ', Bismillah untuk rezeki hari ini!';
    }
}
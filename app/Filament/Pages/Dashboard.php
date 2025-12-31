<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    // HAPUS BARIS INI (Penyebab Blank/Error 500 jika file view tidak ada)
    // protected static string $view = 'filament.pages.dashboard';
    
    protected static ?string $title = 'Dashboard Analytics';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    // Ini widget yang muncul di BODY (Grid)
    public function getWidgets(): array
    {
        return [
            // Row 1: Charts Utama
            \App\Filament\Widgets\OrdersChart::class,
            \App\Filament\Widgets\RevenueChart::class,
            
            // Row 2: Customer Growth
            \App\Filament\Widgets\CustomerGrowthChart::class,
            
            // Row 3: Produk & Kategori
            \App\Filament\Widgets\ProductCategoryChart::class,
            \App\Filament\Widgets\TopProductsChart::class,
            
            // Row 4: Status & Payment
            \App\Filament\Widgets\OrderStatusChart::class,
            \App\Filament\Widgets\PaymentMethodsChart::class,
            
            // Row 5: Tabel Order Terbaru
            \App\Filament\Widgets\LatestOrdersWidget::class,
        ];
    }
    
    public function getColumns(): int | array
    {
        return 2;
    }
    
    // Ini widget yang muncul di HEADER (Atas)
    public function getHeaderWidgets(): array
    {
        return [
            // StatsOverview cukup ditaruh di sini agar rapi di atas
            \App\Filament\Widgets\StatsOverview::class,
        ];
    }
}
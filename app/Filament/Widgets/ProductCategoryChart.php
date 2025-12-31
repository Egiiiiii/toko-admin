<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Produk per Kategori';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $categories = Category::withCount('products')->get();
        
        // Generate warna rainbow untuk setiap kategori
        $colors = $this->generateColors($categories->count());
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Produk',
                    'data' => $categories->pluck('products_count')->toArray(),
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
    
    private function generateColors(int $count): array
    {
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(34, 197, 94, 0.8)',    // Green
            'rgba(234, 179, 8, 0.8)',    // Yellow
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(168, 85, 247, 0.8)',   // Purple
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(14, 165, 233, 0.8)',   // Sky
            'rgba(249, 115, 22, 0.8)',   // Orange
            'rgba(20, 184, 166, 0.8)',   // Teal
            'rgba(163, 163, 163, 0.8)',  // Gray
        ];
        
        // Jika kategori lebih banyak dari warna yang tersedia, ulangi warna
        while (count($colors) < $count) {
            $colors = array_merge($colors, $colors);
        }
        
        return array_slice($colors, 0, $count);
    }
}
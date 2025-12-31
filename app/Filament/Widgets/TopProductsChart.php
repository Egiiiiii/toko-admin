<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Produk Terlaris';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $maxHeight = '300px';
    
    public ?string $filter = 'month';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        $query = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10);
        
        // Filter berdasarkan periode
        $query = match ($activeFilter) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year' => $query->whereYear('created_at', now()->year),
            default => $query->whereMonth('created_at', now()->month),
        };
        
        $topProducts = $query->get();
        
        $productNames = [];
        $quantities = [];
        
        foreach ($topProducts as $item) {
            $product = Product::find($item->product_id);
            $productNames[] = $product ? substr($product->name, 0, 20) : 'Unknown';
            $quantities[] = $item->total_sold;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $quantities,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(163, 163, 163, 0.8)',
                    ],
                ],
            ],
            'labels' => $productNames,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
    
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
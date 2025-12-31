<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Order Status Distribution';
    
    protected static ?int $sort = 7;
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $maxHeight = '300px';
    
    public ?string $filter = 'all';

    protected function getData(): array
    {
        $query = Order::query();
        
        // Filter berdasarkan periode
        $query = match ($this->filter) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year' => $query->whereYear('created_at', now()->year),
            default => $query,
        };
        
        $pending = $query->clone()->where('status', 'pending')->count();
        $processing = $query->clone()->where('status', 'processing')->count();
        $shipped = $query->clone()->where('status', 'shipped')->count();
        $completed = $query->clone()->where('status', 'completed')->count();
        $cancelled = $query->clone()->where('status', 'cancelled')->count();
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Order',
                    'data' => [$pending, $processing, $shipped, $completed, $cancelled],
                    'backgroundColor' => [
                        'rgba(234, 179, 8, 0.8)',   // Pending - Yellow
                        'rgba(59, 130, 246, 0.8)',  // Processing - Blue
                        'rgba(168, 85, 247, 0.8)',  // Shipped - Purple
                        'rgba(34, 197, 94, 0.8)',   // Completed - Green
                        'rgba(239, 68, 68, 0.8)',   // Cancelled - Red
                    ],
                    'borderColor' => [
                        'rgb(234, 179, 8)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
    
    protected function getFilters(): ?array
    {
        return [
            'all' => 'Semua',
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
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
}
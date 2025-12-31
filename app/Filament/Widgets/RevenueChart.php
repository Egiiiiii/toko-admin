<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class RevenueChart extends ChartWidget
{
    protected static bool $isLazy = true;
    
    protected static ?string $heading = 'Revenue Analytics';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'month';
    
    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        $start = match ($activeFilter) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
        
        $end = now();
        
        $perPeriod = match ($activeFilter) {
            'today' => 'perHour',
            'week' => 'perDay',
            'month' => 'perDay',
            'year' => 'perMonth',
            default => 'perDay',
        };
        
        // Revenue dari completed orders
        $completedRevenue = Trend::query(
            Order::query()->where('status', 'completed')
        )
            ->between(start: $start, end: $end)
            ->$perPeriod()
            ->sum('total_price');
        
        // Revenue dari pending orders (potential revenue)
        $pendingRevenue = Trend::query(
            Order::query()->where('status', 'pending')
        )
            ->between(start: $start, end: $end)
            ->$perPeriod()
            ->sum('total_price');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue Terkonfirmasi (Rp)',
                    'data' => $completedRevenue->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Potensi Revenue (Pending) (Rp)',
                    'data' => $pendingRevenue->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(234, 179, 8)',
                    'backgroundColor' => 'rgba(234, 179, 8, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $completedRevenue->map(fn (TrendValue $value) => 
                match ($activeFilter) {
                    'today' => Carbon::parse($value->date)->format('H:00'),
                    'week', 'month' => Carbon::parse($value->date)->format('d M'),
                    'year' => Carbon::parse($value->date)->format('M Y'),
                    default => Carbon::parse($value->date)->format('d M'),
                }
            ),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.dataset.label + ": Rp " + context.parsed.y.toLocaleString("id-ID");
                        }'
                    ]
                ]
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) {
                            return "Rp " + value.toLocaleString("id-ID");
                        }'
                    ],
                ],
            ],
        ];
    }
}
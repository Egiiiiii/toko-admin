<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class CustomerGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Customer';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'year';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        $start = match ($activeFilter) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfYear(),
        };
        
        $end = now();
        
        $perPeriod = match ($activeFilter) {
            'week' => 'perDay',
            'month' => 'perDay',
            'year' => 'perMonth',
            default => 'perMonth',
        };
        
        $customerTrend = Trend::model(Customer::class)
            ->between(start: $start, end: $end)
            ->$perPeriod()
            ->count();
        
        // Hitung kumulatif
        $cumulativeData = [];
        $total = Customer::where('created_at', '<', $start)->count();
        
        foreach ($customerTrend as $value) {
            $total += $value->aggregate;
            $cumulativeData[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Customer Baru',
                    'data' => $customerTrend->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'type' => 'bar',
                ],
                [
                    'label' => 'Total Kumulatif',
                    'data' => $cumulativeData,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'type' => 'line',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $customerTrend->map(fn (TrendValue $value) => 
                match ($activeFilter) {
                    'week', 'month' => Carbon::parse($value->date)->format('d M'),
                    'year' => Carbon::parse($value->date)->format('M Y'),
                    default => Carbon::parse($value->date)->format('M Y'),
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
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
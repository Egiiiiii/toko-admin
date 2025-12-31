<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChart extends ChartWidget
{
    protected static bool $isLazy = true;
    
    protected static ?string $heading = 'Order Statistics';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'month';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        // Tentukan rentang waktu dan format berdasarkan filter
        $data = match ($activeFilter) {
            'today' => $this->getDataForToday(),
            'week' => $this->getDataForWeek(),
            'month' => $this->getDataForMonth(),
            'year' => $this->getDataForYear(),
            default => $this->getDataForMonth(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Total Orders',
                    'data' => $data['total'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Completed',
                    'data' => $data['completed'],
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pending',
                    'data' => $data['pending'],
                    'borderColor' => 'rgb(234, 179, 8)',
                    'backgroundColor' => 'rgba(234, 179, 8, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Cancelled',
                    'data' => $data['cancelled'],
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }
    
    protected function getDataForToday(): array
    {
        $labels = [];
        $total = [];
        $completed = [];
        $pending = [];
        $cancelled = [];
        
        // Data per jam untuk hari ini (24 jam terakhir)
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $labels[] = $hour->format('H:00');
            
            $total[] = Order::whereDate('created_at', $hour->toDateString())
                ->whereHour('created_at', $hour->hour)
                ->count();
                
            $completed[] = Order::whereDate('created_at', $hour->toDateString())
                ->whereHour('created_at', $hour->hour)
                ->where('status', 'completed')
                ->count();
                
            $pending[] = Order::whereDate('created_at', $hour->toDateString())
                ->whereHour('created_at', $hour->hour)
                ->where('status', 'pending')
                ->count();
                
            $cancelled[] = Order::whereDate('created_at', $hour->toDateString())
                ->whereHour('created_at', $hour->hour)
                ->where('status', 'cancelled')
                ->count();
        }
        
        return compact('labels', 'total', 'completed', 'pending', 'cancelled');
    }
    
    protected function getDataForWeek(): array
    {
        $labels = [];
        $total = [];
        $completed = [];
        $pending = [];
        $cancelled = [];
        
        // Data 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $total[] = Order::whereDate('created_at', $date->toDateString())->count();
            $completed[] = Order::whereDate('created_at', $date->toDateString())
                ->where('status', 'completed')->count();
            $pending[] = Order::whereDate('created_at', $date->toDateString())
                ->where('status', 'pending')->count();
            $cancelled[] = Order::whereDate('created_at', $date->toDateString())
                ->where('status', 'cancelled')->count();
        }
        
        return compact('labels', 'total', 'completed', 'pending', 'cancelled');
    }
    
    protected function getDataForMonth(): array
    {
        $labels = [];
        $total = [];
        $completed = [];
        $pending = [];
        $cancelled = [];
        
        // Data 30 hari terakhir
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $total[] = Order::whereDate('created_at', $date->toDateString())->count();
            $completed[] = Order::whereDate('created_at', $date->toDateString())
                ->where('status', 'completed')->count();
            $pending[] = Order::whereDate('created_at', $date->toDateString())
                ->where('status', 'pending')->count();
            $cancelled[] = Order::whereDate('created_at', $date->toDateString())
                ->where('status', 'cancelled')->count();
        }
        
        return compact('labels', 'total', 'completed', 'pending', 'cancelled');
    }
    
    protected function getDataForYear(): array
    {
        $labels = [];
        $total = [];
        $completed = [];
        $pending = [];
        $cancelled = [];
        
        // Data 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');
            
            $total[] = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
                
            $completed[] = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'completed')
                ->count();
                
            $pending[] = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'pending')
                ->count();
                
            $cancelled[] = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'cancelled')
                ->count();
        }
        
        return compact('labels', 'total', 'completed', 'pending', 'cancelled');
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            'year' => 'Last 12 Months',
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
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }
}
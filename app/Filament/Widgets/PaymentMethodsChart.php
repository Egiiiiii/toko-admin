<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentMethodsChart extends ChartWidget
{
    protected static ?string $heading = 'Metode Pembayaran Populer';
    
    protected static ?int $sort = 8;
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $maxHeight = '300px';
    
    public ?string $filter = 'month';

    protected function getData(): array
    {
        // PERBAIKAN: Gunakan whereNotNull('paid_at') karena tabel payment tidak punya kolom status
        $query = Payment::select('payment_method', DB::raw('COUNT(*) as total'))
            ->whereNotNull('paid_at') 
            ->groupBy('payment_method')
            ->orderByDesc('total');
        
        // Filter berdasarkan periode
        $query = match ($this->filter) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year' => $query->whereYear('created_at', now()->year),
            default => $query->whereMonth('created_at', now()->month),
        };
        
        $payments = $query->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $payments->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ],
                ],
            ],
            // Mempercantik label (transfer -> Transfer)
            'labels' => $payments->pluck('payment_method')->map(function($method) {
                return ucfirst($method);
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
}
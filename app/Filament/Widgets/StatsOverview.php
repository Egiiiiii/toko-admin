<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\CarbonInterval;

class StatsOverview extends BaseWidget
{   
    protected static bool $isLazy = true;
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = null;
    protected function getCacheTTL(): ?\DateInterval
    {
        return CarbonInterval::minutes(10);
    }

    protected function getStats(): array
    {
        // Logic Order Change
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $ordersLastMonth = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)->count();
        $orderChange = $ordersLastMonth > 0 
            ? (($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100 
            : 0;

        // Logic New Customer
        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        return [
            Stat::make('Total Revenue', 'Rp ' . number_format($this->getTotalRevenue(), 0, ',', '.'))
                ->description('Pendapatan bersih')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart($this->getTrendData(Order::class, 'total_price', 'sum')),
            
            Stat::make('Total Orders', Order::count())
                ->description(($orderChange >= 0 ? '+' : '') . number_format($orderChange, 1) . '% dari bulan lalu')
                ->descriptionIcon($orderChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($orderChange >= 0 ? 'success' : 'danger')
                ->chart($this->getTrendData(Order::class)),
            
            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Menunggu diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Total Customers', Customer::count())
                ->description($newCustomersThisMonth . ' baru bulan ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart($this->getTrendData(Customer::class)),
                
            // FIX LOGIC PAYMENT
            Stat::make('Pembayaran Sukses', Payment::whereNotNull('paid_at')->count())
                ->description('Transaksi terverifikasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
    
    protected function getTotalRevenue(): float
    {
        return Order::where('status', 'completed')->sum('total_price') ?? 0;
    }

    protected function getTrendData(string $model, string $column = null, string $aggregate = 'count'): array
    {
        $query = $model::query();
        if ($model === Order::class && $aggregate === 'sum') {
            $query->where('status', 'completed');
        }

        $trend = Trend::query($query)
            ->between(start: now()->subDays(7), end: now())
            ->perDay();

        $result = $aggregate === 'sum' ? $trend->sum($column) : $trend->count();
        return $result->map(fn (TrendValue $value) => $value->aggregate)->toArray();
    }
}
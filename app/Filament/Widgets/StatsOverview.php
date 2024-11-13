<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    /**
     * Sort
     */
    protected static ?int $sort = 1;

    /**
     * Widget content height
     */
    protected static ?int $contentHeight = 270;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_amount');

        $newOrdersCount = Order::where('status', 'pending')
            ->count();

        $newCustomersCount = Order::select('user_id')
            ->distinct()
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->count();

        return [
            Stat::make('Revenue', '₱' . number_format($totalRevenue, 2))
                ->description('Total completed orders revenue')
                ->descriptionIcon('heroicon-m-information-circle')
                ->color('success'),
            Stat::make('New Orders', $newOrdersCount)
                ->description('Pending orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
            Stat::make('New Customers', $newCustomersCount)
                ->description('In the last 30 days')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }
}

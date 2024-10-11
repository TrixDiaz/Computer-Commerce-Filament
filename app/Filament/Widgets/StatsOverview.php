<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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
        return [
            Stat::make('Revenue', '₱192,100')
                ->description('Total revenue')
                ->descriptionIcon('heroicon-m-information-circle')
                ->color('success'),
            Stat::make('New Orders', '1,234')
                ->description('Total new orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
            Stat::make('New Customers', '123')
                ->description('Total new customers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }
}

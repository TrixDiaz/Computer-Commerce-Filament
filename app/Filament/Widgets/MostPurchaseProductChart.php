<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\OrderItem;
use App\Models\Order;

class MostPurchaseProductChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'mostPurchaseProductChart';

    
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Most Purchase Product Chart';

     /**
     * Sort
     */
    protected static ?int $sort = 5;

    /**
     * Widget content height
     */
    protected static ?int $contentHeight = 260;

    /**
     * Year
     */
    protected static int $year = 2024;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Get the top 5 products based on frequency of orders
        $topProducts = OrderItem::selectRaw('
                products.id,
                products.name,
                COUNT(*) as order_frequency
            ')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('orders.created_at', self::$year)
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->groupBy('products.id', 'products.name')
            ->orderBy('order_frequency', 'desc')
            ->limit(3)
            ->pluck('name')
            ->toArray();

        // Get all months that have sales in the current year
        $activeMonths = Order::selectRaw('DISTINCT MONTH(created_at) as month')
            ->whereYear('created_at', self::$year)
            ->where('status', Order::STATUS_COMPLETED)
            ->orderBy('month')
            ->pluck('month')
            ->toArray();

        $mostFrequentProducts = OrderItem::selectRaw('
                products.name,
                MONTH(orders.created_at) as month,
                COUNT(*) as order_frequency
            ')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('orders.created_at', self::$year)
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->whereIn('products.name', $topProducts)
            ->groupBy('products.id', 'products.name', 'month')
            ->orderBy('month')
            ->get();

        $productData = [];
        $productColors = [];
        $colorMap = [
            0 => '#f59e0b',
            1 => '#3b82f6',
            2 => '#10b981',
            3 => '#ef4444',
            4 => '#8b5cf6',
        ];

        // Assign colors based on the fixed top products order
        foreach ($topProducts as $index => $productName) {
            $productColors[$productName] = $colorMap[$index];
        }

        foreach ($mostFrequentProducts as $item) {
            $productData[$item->name][$item->month] = $item->order_frequency;
        }

        $series = [];
        $colors = [];

        // Get month names for only active months
        $monthNames = array_map(function($month) {
            return date('M', mktime(0, 0, 0, $month, 1));
        }, $activeMonths);

        // Maintain order based on $topProducts array
        foreach ($topProducts as $productName) {
            $monthlyData = $productData[$productName] ?? [];
            $data = array_map(function($month) use ($monthlyData) {
                return $monthlyData[$month] ?? 0;
            }, $activeMonths);

            $series[] = [
                'name' => $productName,
                'data' => $data,
            ];
            $colors[] = $productColors[$productName];
        }

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'animations' => [
                    'enabled' => false,
                ],
                'toolbar' => [
                    'show' => false,
                ],
                'zoom' => [
                    'enabled' => false,
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $monthNames,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => $colors,
            'stroke' => [
                'curve' => 'smooth',
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'states' => [
                'hover' => [
                    'filter' => [
                        'type' => 'none',
                    ],
                ],
            ],
        ];
    }
}

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
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Get the top 5 products based on the first month or cached order
        $topProducts = OrderItem::selectRaw('
                products.id,
                products.name,
                SUM(order_items.quantity) as total_quantity
            ')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('order_items.created_at', now()->year)
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->pluck('name')
            ->toArray();

        $mostPurchasedProducts = OrderItem::selectRaw('
                products.name,
                MONTH(order_items.created_at) as month,
                SUM(order_items.quantity) as total_quantity
            ')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('order_items.created_at', now()->year)
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->whereIn('products.name', $topProducts)  // Only get data for our top products
            ->groupBy('products.id', 'products.name', 'month')
            ->orderBy('month')
            ->get();

        $productData = [];
        $productColors = [];
        $colorMap = [
            0 => '#f59e0b', // Orange
            1 => '#3b82f6', // Blue
            2 => '#10b981', // Green
            3 => '#ef4444', // Red
            4 => '#8b5cf6', // Purple
        ];

        // Assign colors based on the fixed top products order
        foreach ($topProducts as $index => $productName) {
            $productColors[$productName] = $colorMap[$index];
        }

        foreach ($mostPurchasedProducts as $item) {
            $productData[$item->name][$item->month] = $item->total_quantity;
        }

        $series = [];
        $colors = [];
        foreach ($productData as $productName => $monthlyData) {
            $data = array_map(function($month) use ($monthlyData) {
                return $monthlyData[$month] ?? 0;
            }, range(1, 12));

            $series[] = [
                'name' => $productName,
                'data' => $data,
            ];
            $colors[] = $productColors[$productName];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
                'animations' => [
                    'enabled' => false,
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
        ];
    }
}

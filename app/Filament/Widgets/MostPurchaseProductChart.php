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
        // Get the top 5 products based on TOTAL quantity for the year
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
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $mostPurchasedProducts = OrderItem::selectRaw('
                products.id,
                products.name,
                MONTH(order_items.created_at) as month,
                SUM(order_items.quantity) as total_quantity
            ')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('order_items.created_at', now()->year)
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->whereIn('products.id', $topProducts->pluck('id'))
            ->groupBy('products.id', 'products.name', 'month')
            ->orderBy('total_quantity', 'desc')
            ->get();

        // Prepare series data maintaining the original order from topProducts
        $series = [];
        $colors = [];
        $colorMap = [
            0 => '#f59e0b',
            1 => '#3b82f6',
            2 => '#10b981',
            3 => '#ef4444',
            4 => '#8b5cf6',
        ];

        foreach ($topProducts as $index => $product) {
            $monthlyData = array_fill(1, 12, 0); // Initialize all months with 0
            
            // Fill in actual data
            foreach ($mostPurchasedProducts as $item) {
                if ($item->id === $product->id) {
                    $monthlyData[$item->month] = $item->total_quantity;
                }
            }

            $series[] = [
                'name' => $product->name,
                'data' => array_values($monthlyData),
            ];
            $colors[] = $colorMap[$index];
        }

        return [
            'chart' => [
                'type' => 'line',
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
                'selection' => [
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
                'tooltip' => [
                    'enabled' => false,
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
                'width' => 2,
            ],
            'tooltip' => [
                'enabled' => false,
            ],
            'states' => [
                'hover' => [
                    'filter' => [
                        'type' => 'none',
                    ],
                ],
                'active' => [
                    'filter' => [
                        'type' => 'none',
                    ],
                ],
            ],
        ];
    }
}

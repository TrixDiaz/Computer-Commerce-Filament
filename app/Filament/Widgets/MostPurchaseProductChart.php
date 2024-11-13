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
        $mostPurchasedProducts = OrderItem::selectRaw('
                products.name,
                MONTH(order_items.created_at) as month,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.quantity) as yearly_total
            ')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('order_items.created_at', now()->year)
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->groupBy('products.id', 'products.name', 'month')
            ->orderBy('month')
            ->get();

        // Group and calculate yearly totals for each product
        $yearlyTotals = [];
        foreach ($mostPurchasedProducts as $item) {
            if (!isset($yearlyTotals[$item->name])) {
                $yearlyTotals[$item->name] = 0;
            }
            $yearlyTotals[$item->name] += $item->total_quantity;
        }

        // Get top 5 products by yearly total
        arsort($yearlyTotals);
        $top5Products = array_slice($yearlyTotals, 0, 5, true);

        $productData = [];
        foreach ($mostPurchasedProducts as $item) {
            // Only include data for top 5 products
            if (isset($top5Products[$item->name])) {
                $productData[$item->name][$item->month] = $item->total_quantity;
            }
        }

        $series = [];
        foreach ($productData as $productName => $monthlyData) {
            $data = array_map(function($month) use ($monthlyData) {
                return $monthlyData[$month] ?? 0;
            }, range(1, 12));

            $series[] = [
                'name' => $productName,
                'data' => $data,
            ];
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
            'colors' => ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6'], // Added more colors for multiple products
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }
}

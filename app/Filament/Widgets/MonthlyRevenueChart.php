<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MonthlyRevenueChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'monthlyRevenueChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Revenue per month';

    /**
     * Sort
     */
    protected static ?int $sort = 3;

    /**
     * Widget content height
     */
    protected static ?int $contentHeight = 275;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $revenueData = $this->getRevenueData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 260,
                'parentHeightOffset' => 2,
                'stacked' => true,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Earning',
                    'data' => $revenueData['earnings'],
                ],
                [
                    'name' => 'Expense',
                    'data' => $revenueData['expenses'],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '50%',
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'legend' => [
                'show' => true,
                'horizontalAlign' => 'right',
                'position' => 'top',
                'fontFamily' => 'inherit',
                'markers' => [
                    'height' => 12,
                    'width' => 12,
                    'radius' => 12,
                    'offsetX' => -3,
                    'offsetY' => 2,
                ],
                'itemMargin' => [
                    'horizontal' => 5,
                ],
            ],
            'grid' => [
                'show' => false,

            ],
            'xaxis' => [
                'categories' => [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Oct',
                    'Nov',
                    'Dec',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'axisTicks' => [
                    'show' => false,
                ],
                'axisBorder' => [
                    'show' => false,
                ],
            ],
            'yaxis' => [
                'offsetX' => -16,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'min' => $revenueData['min'],
                'max' => $revenueData['max'],
                'tickAmount' => 5,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#d97706', '#c2410c'],
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 1,
                'lineCap' => 'round',
            ],
            'colors' => ['#f59e0b', '#ea580c'],
        ];
    }

    protected function getRevenueData(): array
    {
        $currentYear = now()->year;

        $earnings = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', $currentYear)
            ->where('status', Order::STATUS_COMPLETED)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->all();

        // Simulate expenses as 40% of earnings (you should replace this with actual expense data)
        $expenses = array_map(function ($earning) {
            return -round($earning * 0.4);
        }, $earnings);

        $earningsData = array_map(function ($month) use ($earnings) {
            return $earnings[$month] ?? 0;
        }, range(1, 12));

        $expensesData = array_map(function ($month) use ($expenses) {
            return $expenses[$month] ?? 0;
        }, range(1, 12));

        $allValues = array_merge($earningsData, $expensesData);
        $min = floor(min($allValues) / 100) * 100;
        $max = ceil(max($allValues) / 100) * 100;

        return [
            'earnings' => $earningsData,
            'expenses' => $expensesData,
            'min' => $min,
            'max' => $max,
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            xaxis: {
                labels: {
                    formatter: function (val, timestamp, opts) {
                        return val
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val, index) {
                        return '₱' + val.toLocaleString()
                    }
                }
            },
            tooltip: {
                x: {
                    formatter: function (val) {
                        return val + ' ' + new Date().getFullYear()
                    }
                }
            }
        }
        JS);
    }
}

<?php

namespace App\Filament\Reports;

use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\VerticalSpace;
use Filament\Forms\Form;
use App\Models\Order;
use Illuminate\Support\Collection; 

class OrdersReport extends Report
{
    public ?string $heading = "Orders Report";

    public function header(Header $header): Header
    {
        return $header
            ->schema([
                Header\Layout\HeaderRow::make()
                    ->schema([
                        Header\Layout\HeaderColumn::make()
                            ->schema([
                                Text::make('Revenue Report')
                                    ->title(),
                                Text::make('This report shows revenue and order details in the system')
                                    ->subtitle(),
                            ]),
                        Header\Layout\HeaderColumn::make()
                    ])
            ]);
    }

    public function body(Body $body): Body
    {
        return $body
            ->schema([
                Body\Layout\BodyColumn::make()
                    ->schema([
                        Body\Table::make()
                            ->data(
                                fn(?array $filters) => $this->ordersSummary($filters)
                            ),
                        VerticalSpace::make(),
                        Body\Table::make()
                            ->data(
                                fn(?array $filters) => $this->salesSummary($filters)
                            ),
                    ]),
            ]);
    }

    public function footer(Footer $footer): Footer
    {
        return $footer
            ->schema([
                Footer\Layout\FooterRow::make()
                    ->schema([
                        Footer\Layout\FooterColumn::make()
                            ->schema([
                                Text::make("GamerGo")
                                    ->title()
                                    ->primary(),
                                Text::make("All Rights Reserved")
                                    ->subtitle(),
                            ]),
                        Footer\Layout\FooterColumn::make()
                            ->schema([
                                Text::make("Generated on: " . now()->format('F d, Y')),
                            ])
                            ->alignRight(),
                    ]),
            ]);
    }

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('month')
                    ->label('Month')
                    ->options(collect(range(1, 12))->mapWithKeys(fn($month) => [$month => now()->setMonth($month)->format('F')]))
                    ->placeholder('All Months')
                    ->native(false),
                \Filament\Forms\Components\Select::make('status')
                    ->label('Order Status')
                    ->options(array_combine(Order::$statuses, Order::$statuses))
                    ->placeholder('All Statuses')
                    ->native(false),
                \Filament\Forms\Components\Actions::make([
                    \Filament\Forms\Components\Actions\Action::make('reset')
                        ->label('Reset Filters')
                        ->color('danger')
                        ->action(function (Form $form) {
                            $form->fill([
                                'status' => null,
                                'month' => null,
                            ]);
                        })
                ]),
            ]);
    }

    public function ordersSummary(?array $filters): Collection
    {
        $query = Order::query()
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->when($filters['month'] ?? null, fn($query, $month) => $query->whereMonth('created_at', $month))
            ->with(['customer', 'orderItems.product']);

        $filtersApplied = isset($filters['status']) || isset($filters['month']);

        if (!$filtersApplied) {
            return collect();
        } else {
            $orders = $query->latest('created_at')->get();
        }

        $result = collect([
            [
                'column1' => 'Order Number',
                'column2' => 'Customer',
                'column3' => 'Total Amount',
                'column4' => 'Status',
                'column5' => 'Payment Status',
                'column6' => 'Date',
                'column7' => 'Items',
            ]
        ]);

        foreach ($orders as $order) {
            $itemsList = $order->orderItems->map(function ($item) {
                $productName = $item->product->name ?? 'Unknown Product';
                return "• {$productName} - Qty: {$item->quantity} - Price: " . number_format($item->price, 2);
            })->join("\n");

            $result->push([
                'column1' => $order->order_number,
                'column2' => $order->customer?->name ?? 'N/A',
                'column3' => number_format($order->total_amount, 2),
                'column4' => $order->status,
                'column5' => $order->payment_status,
                'column6' => $order->created_at->format('F d, Y'),
                'column7' => $itemsList,
            ]);
        }

        return $result;
    }

    public function salesSummary(?array $filters): Collection
    {
        // Check if any filters are applied
        $filtersApplied = isset($filters['status']) || isset($filters['month']);

        if (!$filtersApplied) {
            return collect();
        }

        // Base query
        $baseQuery = Order::query();

        // Apply filters if present
        if (isset($filters['month'])) {
            $baseQuery->whereMonth('created_at', $filters['month']);
        }

        $result = collect([
            [
                'column1' => 'Status',
                'column2' => 'Total Revenue',
            ]
        ]);

        // If status filter is applied, show only that status
        if (isset($filters['status'])) {
            $total = $baseQuery->where('status', $filters['status'])->sum('total_amount');
            $result->push([
                'column1' => ucfirst($filters['status']),
                'column2' => number_format($total, 2),
            ]);
        } else {
            // If no status filter, show all statuses
            foreach (Order::$statuses as $status) {
                $total = (clone $baseQuery)->where('status', $status)->sum('total_amount');
                $result->push([
                    'column1' => ucfirst($status),
                    'column2' => number_format($total, 2),
                ]);
            }
        }

        return $result;
    }
}

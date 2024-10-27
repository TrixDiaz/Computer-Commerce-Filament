<?php

namespace App\Filament\Reports;

use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\VerticalSpace;
use EightyNine\Reports\Components\Table;
use EightyNine\Reports\Components\Chart;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use App\Models\Order;

class RevenueReport extends Report
{
    public ?string $heading = "Revenue Report";

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
                \Filament\Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->placeholder('Start Date')
                    ->timezone('Asia/Manila')
                    ->displayFormat('Y-m-d')
                    ->native(false)
                    ->maxDate(now()),
                \Filament\Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->placeholder('End Date')
                    ->timezone('Asia/Manila')
                    ->displayFormat('Y-m-d')
                    ->native(false)
                    ->maxDate(now()),
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
                                'start_date' => null,
                                'end_date' => null,
                                'status' => null,
                            ]);
                        })
                ]),
            ]);
    }

    public function ordersSummary(?array $filters): Collection
    {
        $query = Order::query()
            ->when($filters['start_date'] ?? null, fn($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->with(['customer', 'orderItems.product']);

        $filtersApplied = isset($filters['start_date']) || isset($filters['end_date']) || isset($filters['status']);

        if (!$filtersApplied) {
            $orders = $query->latest('created_at')->take(10)->get();
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
                return "• {$item->product->name} - Qty: {$item->quantity} - Price: " . number_format($item->price, 2);
            })->join("\n");

            $result->push([
                'column1' => $order->order_number,
                'column2' => $order->customer?->name ?? 'N/A', // Use null coalescing operator
                'column3' => number_format($order->total_amount, 2),
                'column4' => $order->status,
                'column5' => $order->payment_status,
                'column6' => $order->created_at->format('F d, Y'),
                'column7' => $itemsList,
            ]);
        }

        return $result;
    }

    public function revenueByDay(?array $filters): Collection
    {
        return Order::query()
            ->when($filters['start_date'] ?? null, fn($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($item) => [
                'x' => $item->date,
                'y' => $item->total_revenue,
            ]);
    }
}

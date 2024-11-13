<div class="antialiased max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
    <div class="bg-white p-6">
        <div class="flex justify-between mb-8">
            <div>
            <h2 class="text-2xl font-bold">INVOICE</h2>
            <p>Order #{{ $order->order_number }}</p>
            <p>Date: {{ $order->created_at->format('M d, Y') }}</p>
        </div>
        <div>
            <h3 class="text-xl font-semibold">Your Company Name</h3>
            <p>123 Company Street</p>
            <p>City, State 12345</p>
            <p>Phone: (123) 456-7890</p>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-2">Bill To:</h3>
        <p>{{ $order->customer->name }}</p>
        @if($order->billingAddress)
            <p>{{ $order->billingAddress->address_line_1 }}</p>
            <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
        @else
            <p>No billing address provided</p>
        @endif
    </div>

    <table class="w-full mb-8">
        <thead>
            <tr>
                <th class="text-left">Product</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-semibold">Total:</td>
                <td class="text-right font-semibold">₱{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div>
        <h3 class="text-lg font-semibold mb-2">Payment Information:</h3>
        <p>Payment Status: {{ ucfirst($order->status) }}</p>
    </div>
</div>
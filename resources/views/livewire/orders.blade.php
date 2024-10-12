<div class="antialiased max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
    <h2 class="text-2xl font-semibold mb-4">Your Orders</h2>

    @if($orders->isEmpty())
    <p class="text-gray-600">You don't have any orders yet.</p>
    @else
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Order Number
                </th>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Date
                </th>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Status
                </th>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Total
                </th>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                    {{ $order->order_number }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                    {{ $order->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $order->status === 'completed' ? 'green' : 'yellow' }}-100 text-{{ $order->status === 'completed' ? 'green' : 'yellow' }}-800">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                    ₱{{ number_format($order->total_amount, 2) }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                    <button wire:click="viewOrderDetails({{ $order->id }})" class="mr-2 px-2 py-1 rounded-md bg-indigo-500 text-white">View</button>
                    @if($order->status !== 'completed')
                    <button wire:click="cancelOrder({{ $order->id }})" class="mr-2 px-2 py-1 rounded-md bg-red-500 text-white">Cancel</button>
                    @endif
                    @if($order->status === 'completed')
                    <button wire:click="refundOrder({{ $order->id }})" class="mr-2 px-2 py-1 rounded-md bg-yellow-500 text-white">Refund</button>
                    <button wire:click="generateInvoice({{ $order->id }})" class="px-2 py-1 rounded-md bg-green-500 text-white">Invoice</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($selectedOrder)
    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-4">Order Details: {{ $selectedOrder->order_number }}</h3>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Product
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Quantity
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Price
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Subtotal
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($selectedOrder->orderItems as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                        {{ $item->product->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                        {{ $item->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                        ₱{{ number_format($item->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                        ₱{{ number_format($item->quantity * $item->price, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($showInvoice)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="invoice-modal">
        <div class="relative top-20 mx-auto p-5 border w-11/12 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Invoice for Order #{{ $selectedOrder->order_number }}</h3>
                <button wire:click="$set('showInvoice', false)" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="printable-invoice">
                @livewire('invoice', ['order' => $selectedOrder])
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="printInvoice()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Print Invoice
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function printInvoice() {
        const printContents = document.getElementById('printable-invoice').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
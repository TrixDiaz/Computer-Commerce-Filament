<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Invoice</h1>
        <p>Dear {{ $user->name }},</p>
        <p>Thank you for your order. Your order number is: <strong>{{ $orderDetails['orderNumber'] }}</strong></p>

        <h2>Order Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderDetails['items'] as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₱{{ number_format($item['price'], 2) }}</td>
                        <td>₱{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Order Totals</h2>
        <table>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            <tr>
                <td>Subtotal</td>
                <td>₱{{ number_format($orderDetails['subtotal'], 2) }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td>₱{{ number_format($orderDetails['tax'], 2) }}</td>
            </tr>
            <tr>
                <td>Delivery Fee</td>
                <td>₱{{ number_format($orderDetails['deliveryFee'], 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td>₱{{ number_format($orderDetails['discount'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>₱{{ number_format($orderDetails['total'], 2) }}</strong></td>
            </tr>
        </table>

        <h2>Shipping Information</h2>
        <p>
            {{ $orderDetails['shippingAddress']->address_line_1 }}<br>
            @if($orderDetails['shippingAddress']->address_line_2)
                {{ $orderDetails['shippingAddress']->address_line_2 }}<br>
            @endif
            {{ $orderDetails['shippingAddress']->city }}, {{ $orderDetails['shippingAddress']->state }} {{ $orderDetails['shippingAddress']->postal_code }}<br>
            {{ $orderDetails['shippingAddress']->country }}
        </p>

        <h2>Payment and Shipping</h2>
        <p>Payment Method: {{ ucfirst($orderDetails['paymentMethod']) }}</p>
        <p>Shipping Option: {{ ucfirst($orderDetails['shippingOption']) }}</p>

        <p>If you have any questions about your order, please don't hesitate to contact us.</p>
        <p>Thank you for shopping with us!</p>
    </div>
</body>
</html>

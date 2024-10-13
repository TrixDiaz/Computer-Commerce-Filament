<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderInvoice;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class PaymentController extends Controller
{
    public function handlePaymentSuccess(Request $request)
    {
        // Handle successful payment
        session()->flash('swal:success', [
            'title' => 'Success!',
            'text' => 'Your payment was successful.',
            'icon' => 'success',
        ]);

        // Create order and send invoice email
        $this->createOrderAndSendInvoice();

        // Clear the cart or session data
        session()->forget(['cart', 'selected_address_id', 'discount', 'payment_method', 'shipping_option']);

        return redirect()->route('home')->with('success', 'Payment successful!');
    }

    public function handlePaymentFailed(Request $request)
    {
        // Handle failed payment
        return redirect()->route('cart')->with('error', 'Your payment was not successful. Please try again.');
    }

    private function createOrderAndSendInvoice()
    {
        $user = Auth::user();
        $cartItems = session('cart', []);
        $selectedAddress = $user->addresses()->find(session('selected_address_id'));

        if (!$selectedAddress) {
            session()->flash('swal:error', [
                'title' => 'Error!',
                'text' => 'No shipping address found for order',
                'icon' => 'error',
            ]);
            return;
        }

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $this->calculateTotal($cartItems),
            'status' => Order::STATUS_PROCESSING,
            'billing_address_id' => $selectedAddress->id,
            'shipping_address_id' => $selectedAddress->id,
            'notes' => session('order_notes'),
        ]);

        // Create order items
        foreach ($cartItems as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $orderDetails = [
            'items' => $cartItems,
            'orderNumber' => $order->order_number,
            'subtotal' => $this->calculateSubtotal($cartItems),
            'tax' => $this->calculateTax($cartItems),
            'deliveryFee' => $this->calculateDeliveryFee(),
            'discount' => session('discount', 0),
            'total' => $this->calculateTotal($cartItems),
            'shippingAddress' => $selectedAddress,
            'paymentMethod' => session('payment_method'),
            'shippingOption' => session('shipping_option'),
        ];

        Mail::to($user->email)->send(new OrderInvoice($user, $orderDetails));
    }

    private function calculateSubtotal($cartItems)
    {
        return array_reduce($cartItems, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }

    private function calculateTax($cartItems)
    {
        $subtotal = $this->calculateSubtotal($cartItems);
        return $subtotal * 0.12; // Assuming 12% tax
    }

    private function calculateDeliveryFee()
    {
        return session('shipping_option') === 'rush' ? 100 : 0;
    }

    private function calculateTotal($cartItems)
    {
        $subtotal = $this->calculateSubtotal($cartItems);
        $tax = $this->calculateTax($cartItems);
        $deliveryFee = $this->calculateDeliveryFee();
        $discount = session('discount', 0);

        return $subtotal + $tax + $deliveryFee - $discount;
    }

    public function sendTestEmail()
    {
        // Create a dummy user
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        // Create dummy order details
        $orderDetails = [
            'items' => [
                [
                    'name' => 'Test Product 1',
                    'quantity' => 2,
                    'price' => 100.00
                ],
                [
                    'name' => 'Test Product 2',
                    'quantity' => 1,
                    'price' => 50.00
                ]
            ],
            'subtotal' => 250.00,
            'tax' => 25.00,
            'deliveryFee' => 10.00,
            'discount' => 5.00,
            'total' => 280.00,
            'shippingAddress' => (object) [
                'address_line_1' => '123 Test Street',
                'address_line_2' => 'Apt 4B',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'Test Country'
            ],
            'paymentMethod' => 'credit card',
            'shippingOption' => 'standard'
        ];

        // Send the test email
        Mail::to('recipient@example.com')->send(new OrderInvoice($user, $orderDetails));

        return 'Test order invoice email sent successfully!';
    }
}

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
use Filament\Notifications\Notification;

class PaymentController extends Controller
{
    public function handlePaymentSuccess(Request $request)
    {
        // Create order and send invoice email
        $order = $this->createOrderAndSendInvoice();

        if (!$order) {
            // Handle the case where order creation failed
            session()->flash('swal:error', [
                'title' => 'Error!',
                'text' => 'There was a problem creating your order. Please contact support.',
                'icon' => 'error',
            ]);
            return redirect()->route('cart')->with('error', 'Order creation failed.');
        }

        // Handle successful payment
        session()->flash('swal:success', [
            'title' => 'Success!',
            'text' => 'Your payment was successful.',
            'icon' => 'success',
        ]);

        // Create Filament notification
        Notification::make()
            ->success()
            ->title("{$order->payment_method} Order Placed Successfully")
            ->body("{$order->customer->first_name} {$order->customer->last_name}, Your order #{$order->order_number} has been placed successfully.")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Order')
                    ->url(route('orders'))
            ])
            ->sendToDatabase(Auth::user());

        Notification::make()
            ->success()
            ->title('Gcash Payment Successful')
            ->body("{$order->customer->first_name} {$order->customer->last_name}, order #{$order->order_number} has been placed successfully.")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Order')
                    ->url(route('filament.admin.resources.orders.edit', $order->id))
            ])
            ->sendToDatabase(User::find(1));

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
            return null;
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
            'payment_method' => session('payment_method'),
            'shipping_option' => session('shipping_option'),
        ]);

        // Create order items
        foreach ($cartItems as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Update product stock in the database
            $product = Product::find($productId);
            if ($product) {
                $product->stock_quantity -= $item['quantity'];
                $product->save();
            }
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

        return $order; // Return the created order
    }

    private function calculateSubtotal($cartItems)
    {
        // Convert to array if it's a collection
        $items = $cartItems instanceof \Illuminate\Support\Collection ? $cartItems->toArray() : $cartItems;

        return array_reduce($items, function ($carry, $item) {
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
}

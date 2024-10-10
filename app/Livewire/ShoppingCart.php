<?php

namespace App\Livewire;

use Livewire\Component;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $subtotal = 0;
    public $tax = 0;
    public $deliveryFee = 0;
    public $relatedProducts = [];

    public function mount()
    {
        $this->getUpdatedCart();
        $this->fetchRelatedProducts();
    }

    public function getUpdatedCart()
    {
        $this->cartItems = session('cart', []);
        $this->calculateTotal();
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }

    public function calculateTotal()
    {
        $this->subtotal = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $this->tax = $this->subtotal * 0.12; // Assuming 12% tax
        $this->deliveryFee = 100; // Fixed delivery fee of 100
        $this->total = $this->subtotal + $this->tax + $this->deliveryFee;
    }

    public function fetchRelatedProducts()
    {
        if (empty($this->cartItems)) {
            $this->relatedProducts = [];
            return;
        }

        $categoryIds = array_unique(array_column($this->cartItems, 'category_id'));
        $brandId = array_unique(array_column($this->cartItems, 'brand_id'));
        // Fetch related products from the database
        $this->relatedProducts = \App\Models\Product::whereIn('category_id', $categoryIds)
            ->orWhere('brand_id', $brandId)
            ->whereNotIn('id', array_keys($this->cartItems))
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->toArray();
    }

    public function updateQuantity($productId, $quantity)
    {
        if (isset($this->cartItems[$productId])) {
            $this->cartItems[$productId]['quantity'] = max(1, $quantity);
            session(['cart' => $this->cartItems]);
            $this->calculateTotal();
        }
    }

    public function removeItem($productId)
    {
        unset($this->cartItems[$productId]);
        session(['cart' => $this->cartItems]);
        $this->calculateTotal();
        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Item removed from cart successfully!',
            'icon' => 'success',
            'timer' => 3000,
        ]);
    }

    public function proceedToCheckout()
    {
        $total = $this->total; // Assuming $this->total holds the total price

        $data = [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'currency' => 'PHP',
                            'amount' => (int)($total * 100), // Convert to cents and ensure it's an integer
                            'description' => 'Payment for your order',
                            'name' => 'Order Payment',
                            'quantity' => 1,
                        ],
                    ],
                    'payment_method_types' => ['gcash'],
                    'success_url' => route('payment.success'),
                    'cancel_url' => route('payment.failed'),
                    'description' => 'Payment for your order',
                ],
            ],
        ];
        
        $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions')
            ->withHeader('Content-Type: application/json')
            ->withHeader('accept: application/json')
            ->withHeader('Authorization: Basic c2tfdGVzdF9ZS1lMMnhaZWVRRDZjZ1dYWkJYZ1dHVU46' . base64_encode(config('services.paymongo.secret_key')))
            ->withData($data)
            ->asJson()
            ->post();

        if (isset($response->data->attributes->checkout_url)) {
            Session::put('session_id', $response->data->id);
            Session::put('checkout_url', $response->data->attributes->checkout_url);
            return redirect()->to($response->data->attributes->checkout_url);
        } else {
            // Handle error
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Unable to process payment. Please try again later.',
                'icon' => 'error',
            ]);
            return redirect()->route('payment.failed');
        }
    }

    private function formatLineItems()
    {
        return array_map(function ($item) {
            return [
                'currency' => 'PHP',
                'amount' => (int)($item['price'] * 100),
                'name' => $item['name'],
                'quantity' => $item['quantity'],
            ];
        }, $this->cartItems);
    }

    public function handlePaymentSuccess()
    {
        // Handle successful payment
        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Your payment was successful.',
            'icon' => 'success',
        ]);
        // Clear the cart or perform any other necessary actions
        $this->cartItems = [];
        $this->total = 0;
    }

    public function handlePaymentFailed()
    {
        // Handle failed payment
        $this->dispatch('swal:error', [
            'title' => 'Payment Failed',
            'text' => 'Your payment was not successful. Please try again.',
            'icon' => 'error',
        ]);
    }
}

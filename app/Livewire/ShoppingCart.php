<?php

namespace App\Livewire;

use App\Models\Coupon;
use Carbon\Carbon;
use Livewire\Component;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderInvoice;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $subtotal = 0;
    public $tax = 0;
    public $deliveryFee = 0;
    public $relatedProducts = [];
    public $paymentMethod = 'cod';
    public $shippingOption = 'normal';
    public $couponCode;
    public $discount = 0;
    public $addresses = [];
    public $selectedAddressId = null;
    public $newAddress = [
        'user_id' => '',
        'address_line_1' => '',
        'address_line_2' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country' => '',
    ];

    public function mount()
    {
        $this->getUpdatedCart();
        $this->fetchRelatedProducts();
        $this->loadUserAddresses();
    }

    public function loadUserAddresses()
    {
        if (auth()->check()) {
            $this->addresses = auth()->user()->addresses()->get();
            if ($this->addresses->isNotEmpty()) {
                $this->selectedAddressId = $this->addresses->first()->id;
            }
        } else {
            $this->addresses = collect(); // Initialize as an empty collection if user is not authenticated
        }
    }

    public function applyCoupon($couponCode)
    {
        $coupon = Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            $this->addError('coupon', 'Invalid coupon code.');
            return;
        }

        $now = Carbon::now();
        if ($now->lt($coupon->start_date) || $now->gt($coupon->end_date)) {
            $this->addError('coupon', 'This coupon is not valid at this time.');
            return;
        }

        if ($coupon->usage_limit <= $coupon->used_count) {
            $this->addError('coupon', 'This coupon has reached its usage limit.');
            return;
        }

        // Apply the discount
        if ($coupon->type === 'fixed') {
            $this->discount = $coupon->value;
        } else { // percentage
            $this->discount = $this->subtotal * ($coupon->value / 100);
        }

        // Update the total
        $this->calculateTotal();

        // Increment the used count
        $coupon->increment('used_count');

        session()->flash('coupon_message', 'Coupon applied successfully!');
        session()->flash('coupon_success', true);
    }

    public function getUpdatedCart()
    {
        $this->cartItems = collect(session('cart', []));
        $this->cartItems = $this->cartItems->map(function ($item, $productId) {
            $product = Product::find($productId);
            if ($product) {
                $item['stock'] = $product->stock_quantity;
                $item['quantity'] = min($item['quantity'], $item['stock']);
                // Ensure all item properties are strings or numbers
                $item['name'] = (string) $item['name'];
                $item['price'] = (float) $item['price'];
                $item['image'] = $product->images[0] ?? null; // Assuming the first image
            }
            return $item;
        });
        $this->calculateTotal();
        $this->total = $this->subtotal + $this->tax + $this->deliveryFee - $this->discount;
    }

    public function render()
    {
        return view('livewire.shopping-cart', [
            'cartItems' => collect($this->cartItems),
            'total' => $this->total,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'deliveryFee' => $this->deliveryFee,
            'discount' => $this->discount,
            'relatedProducts' => collect($this->relatedProducts),
            'addresses' => collect($this->addresses), // Ensure addresses is always a collection
        ]);
    }

    public function calculateTotal()
    {
        $this->subtotal = $this->cartItems->reduce(function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $this->tax = $this->subtotal * 0.12; // Assuming 12% tax
        $this->deliveryFee = $this->shippingOption === 'rush' ? 100 : 0;
        $this->total = $this->subtotal + $this->tax + $this->deliveryFee - $this->discount;
    }

    public function fetchRelatedProducts()
    {
        if ($this->cartItems->isEmpty()) {
            $this->relatedProducts = [];
            return;
        }

        $categoryIds = $this->cartItems->pluck('category_id')->unique()->toArray();
        $brandIds = $this->cartItems->pluck('brand_id')->unique()->toArray();

        // Fetch related products from the database
        $this->relatedProducts = \App\Models\Product::whereIn('category_id', $categoryIds)
            ->orWhereIn('brand_id', $brandIds)
            ->whereNotIn('id', $this->cartItems->keys())
            ->inRandomOrder()
            ->limit(9)
            ->get()
            ->toArray();
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($this->cartItems->has($productId)) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $maxQuantity = min($product->stock_quantity, max(1, $quantity));
                $updatedCartItems = $this->cartItems->toArray();
                $updatedCartItems[$productId]['quantity'] = $maxQuantity;
                $updatedCartItems[$productId]['stock'] = $product->stock_quantity;
                $this->cartItems = collect($updatedCartItems);
                session(['cart' => $updatedCartItems]);
                $this->calculateTotal();
            }
        }
    }

    public function removeItem($productId)
    {
        $updatedCartItems = $this->cartItems->toArray();
        unset($updatedCartItems[$productId]);
        $this->cartItems = collect($updatedCartItems);
        session(['cart' => $updatedCartItems]);
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
        if (!$this->selectedAddressId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Please select or add a shipping address.',
                'icon' => 'error',
            ]);
            return;
        }

        // Store necessary information in the session
        session([
            'cart' => $this->cartItems,
            'selected_address_id' => $this->selectedAddressId,
            'payment_method' => $this->paymentMethod,
        ]);

        if ($this->paymentMethod === 'cod') {
            // Handle Cash on Delivery checkout
            $this->handleCashOnDeliveryCheckout();
        } else {
            // Handle GCash checkout
            $this->handleGCashCheckout();
        }
    }

    private function handleCashOnDeliveryCheckout()
    {
        // Generate a unique order number
        $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));

        // Create a new order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => $orderNumber,
            'total_amount' => $this->total,
            'status' => 'pending',
            'payment_method' => 'cod',
            'shipping_address_id' => $this->selectedAddressId,
        ]);

        // Add order items
        $this->cartItems->each(function ($item, $productId) use ($order) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        });

        // Send notification to the user
        $user = Auth::user();
        $admin = \App\Models\User::find(1);

        Notification::make()
            ->title("{$order->payment_method} Order Placed")
            ->body("Your {$order->payment_method} order has been placed successfully. Order #{$order->order_number}")
            ->success()
            ->sendToDatabase($user);

        // Send notification to the admin (assuming user with ID 1 is the admin)
        Notification::make()
            ->title("{$order->payment_method} Order Placed")
            ->body("A new {$order->payment_method} order has been placed. Order #{$order->order_number}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Order')
                    ->url(route('filament.admin.resources.orders.edit', $order->id))
            ])
            ->success()
            ->sendToDatabase($admin);

        // Clear the cart session
        session()->forget('cart');

        $this->dispatch('swal:success', [
            'title' => 'Order Placed!',
            'text' => "Your Cash on Delivery order has been placed successfully. Order #{$order->order_number}",
            'icon' => 'success',
        ]);

        // Clear the component's cart items and total
        $this->cartItems = [];
        $this->total = 0;

        // Redirect to the confirmation page
        return redirect()->route('order.confirmation', ['order' => $order->id]);
    }

    private function handleGCashCheckout()
    {
        $total = $this->total;
        $user = Auth::user();
        $customerName = $user->first_name . ' ' . $user->last_name;

        $data = [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'currency' => 'PHP',
                            'amount' => (int)($total * 100),
                            'description' => 'Payment for your order',
                            'name' => 'Order Payment',
                            'quantity' => 1,
                        ],
                    ],
                    'payment_method_types' => ['gcash'],
                    'success_url' => route('payment.success'),
                    'cancel_url' => route('payment.failed'),
                    'description' => 'Payment for your order',
                    'customer' => [
                        'name' => $customerName,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '',
                    ],
                    'billing' => [
                        'name' => $customerName,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '',
                    ],
                ],
            ],
        ];

        // Generate a unique order number
        $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));

        // Create a pending order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => $orderNumber,
            'total_amount' => $this->total,
            'status' => 'pending',
            'payment_method' => 'gcash',
            'shipping_address_id' => $this->selectedAddressId,
        ]);

        // Add order items
        $this->cartItems->each(function ($item, $productId) use ($order) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        });

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
            Session::put('order_id', $order->id);
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

    public function updatePaymentMethod($method)
    {
        $this->paymentMethod = $method;
    }

    public function updateShippingOption($option)
    {
        $this->shippingOption = $option;
        $this->calculateTotal();
    }

    private function formatLineItems()
    {
        return $this->cartItems->map(function ($item) {
            return [
                'currency' => 'PHP',
                'amount' => (int)($item['price'] * 100),
                'name' => $item['name'],
                'quantity' => $item['quantity'],
            ];
        })->values()->toArray();
    }

    public function selectAddress($addressId)
    {
        $this->selectedAddressId = $addressId;
    }

    public function addNewAddress()
    {
        $this->validate([
            'newAddress.address_line_1' => 'required',
            'newAddress.city' => 'required',
            'newAddress.state' => 'required',
            'newAddress.postal_code' => 'required',
            'newAddress.country' => 'required',
        ]);

        $this->newAddress['user_id'] = auth()->id();

        $address = auth()->user()->addresses()->create($this->newAddress);
        $this->addresses->push($address);
        $this->selectedAddressId = $address->id;
        $this->newAddress = [
            'user_id' => '',
            'address_line_1' => '',
            'address_line_2' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => '',
        ];

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'New address added successfully!',
            'icon' => 'success',
        ]);
    }

    public function addToCart($productId)
    {
        $product = \App\Models\Product::find($productId);
        if ($product && $product->stock_quantity > 0) {
            if (isset($this->cartItems[$productId])) {
                if ($this->cartItems[$productId]['quantity'] < $product->stock_quantity) {
                    $this->cartItems[$productId]['quantity']++;
                }
            } else {
                $this->cartItems[$productId] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                    'stock' => $product->stock_quantity,
                    // Add other necessary product details
                ];
            }
            session(['cart' => $this->cartItems]);
            $this->calculateTotal();
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Item added to cart successfully!',
                'icon' => 'success',
                'timer' => 3000,
            ]);
        } else {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'This product is out of stock.',
                'icon' => 'error',
            ]);
        }
    }
}

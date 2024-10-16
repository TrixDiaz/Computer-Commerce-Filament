<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Session;

class ProductProfile extends Component
{
    public $product;
    public $modelPath;
    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->product = Product::where('slug', $slug)->firstOrFail();
        $this->modelPath = $this->product->model ? asset('storage/' . $this->product->model) : null;
    }

    public function addToCart($productId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);
        
        if(isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                "name" => $product->name,
                "slug" => $product->slug,
                "description" => $product->description,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image_url,
                "sku" => $product->sku,
                "category_id" => $product->category_id,
                "brand_id" => $product->brand_id,
                "stock_quantity" => $product->stock_quantity,
                "is_featured" => $product->is_featured,
                "is_active" => $product->is_active,
                "is_sale" => $product->is_sale,
                "is_new" => $product->is_new,
                "is_best_seller" => $product->is_best_seller,
                "is_top_rated" => $product->is_top_rated,
                "is_on_sale" => $product->is_on_sale,
            ];
        }
        
        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Item added to cart successfully!',
            'icon' => 'success',
            'timer' => 3000,
        ]);
    }

    public function payWithGCash($productId)
    {
        $product = Product::findOrFail($productId);
        $total = $product->price;

        // Set session data for single product purchase
        session()->put('cart', [
            $productId => [
                "id" => $product->id,
                "name" => $product->name,
                "slug" => $product->slug,
                "description" => $product->description,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image_url,
                "sku" => $product->sku,
                "category_id" => $product->category_id,
                "brand_id" => $product->brand_id,
                "stock_quantity" => $product->stock_quantity,
                "is_featured" => $product->is_featured,
                "is_active" => $product->is_active,
                "is_sale" => $product->is_sale,
                "is_new" => $product->is_new,
                "is_best_seller" => $product->is_best_seller,
                "is_top_rated" => $product->is_top_rated,
                "is_on_sale" => $product->is_on_sale,
            ]
        ]);

        // Set other necessary session data
        session()->put('selected_address_id', auth()->user()->addresses->first()->id ?? null);
        session()->put('payment_method', 'gcash');
        session()->put('shipping_option', 'standard');

        $data = [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'currency' => 'PHP',
                            'amount' => (int)($total * 100),
                            'description' => "Payment for {$product->name}",
                            'name' => $product->name,
                            'quantity' => 1,
                        ],
                    ],
                    'payment_method_types' => ['gcash'],
                    'success_url' => route('payment.success'),
                    'cancel_url' => route('payment.failed'),
                    'description' => "Payment for {$product->name}",
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
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Unable to process payment. Please try again later.',
                'icon' => 'error',
            ]);
            return redirect()->route('payment.failed');
        }
    }

    public function render()
    {
        return view('livewire.product-profile');
    }
}

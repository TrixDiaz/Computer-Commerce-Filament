<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductProfile extends Component
{
    public $product;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->firstOrFail();
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
        // Implement GCash payment logic here
        session()->flash('message', 'GCash payment functionality coming soon!');
    }

    public function render()
    {
        return view('livewire.product-profile');
    }
}

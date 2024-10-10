<?php

namespace App\Livewire;

use Livewire\Component;

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
}

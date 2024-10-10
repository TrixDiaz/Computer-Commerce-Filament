<?php

namespace App\Livewire;

use Livewire\Component;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $total = 0;

    public function mount()
    {
        $this->cartItems = session('cart', []);
        $this->calculateTotal();
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }

    public function getUpdatedCart()
    {
        $this->cartItems = session('cart', []);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
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

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

    public function render()
    {
        return view('livewire.product-profile');
    }
}

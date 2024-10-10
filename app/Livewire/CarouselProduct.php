<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class CarouselProduct extends Component
{
    public $products;

    public function mount()
    {
        $this->products = Product::inRandomOrder()->take(10)->get();
    }

    public function render()
    {
        return view('livewire.carousel-product');
    }
}

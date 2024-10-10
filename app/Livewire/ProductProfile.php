<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductProfile extends Component
{
    public $product;
    public $slug;
    public $error;

    public function mount($slug)
    {
        $this->slug = $slug;
        try {
            $this->product = Product::where('slug', $slug)->firstOrFail();
        } catch (\Exception $e) {
            $this->error = "Product not found. Slug: " . $slug;
        }
    }

    public function render()
    {
        return view('livewire.product-profile');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Review;

class ProductReview extends Component
{
    public $slug;
    public $reviews;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadReviews();
    }

    public function loadReviews()
    {
        $product = Product::where('slug', $this->slug)->firstOrFail();
        $this->reviews = $product->reviews()
            ->with('user')
            ->where('is_approved', 1)
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.product-review');
    }
}

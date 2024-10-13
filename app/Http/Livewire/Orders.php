<?php

namespace App\Http\Livewire;

use App\Models\Review;
use Livewire\Component;

class Orders extends Component
{
    public $showReviewModal = false;
    public $productIdToReview;
    public $rating;
    public $comment;

    public function openReviewModal($productId)
    {
        $this->productIdToReview = $productId;
        $this->showReviewModal = true;
    }

    public function submitReview()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        Review::create([
            'product_id' => $this->productIdToReview,
            'customer_id' => auth()->id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_approved' => false, // Set to false by default, admin can approve later
        ]);

        $this->showReviewModal = false;
        $this->reset(['productIdToReview', 'rating', 'comment']);
        session()->flash('message', 'Review submitted successfully!');
    }
}

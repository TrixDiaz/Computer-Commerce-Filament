<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Orders extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 5;
    public $selectedOrder;
    public $showInvoice = false;
    public $showCancelConfirmation = false;
    public $orderIdToCancel;
    public $showRefundConfirmation = false;
    public $orderToRefund;
    public $showReviewModal = false;
    public $productIdToReview;
    public $rating;
    public $comment;
    public $selectedOrderId;

    public function mount()
    {
        // Remove any initialization of $this->orders
    }

    public function openReviewModal($orderId, $productId)
    {
        $this->selectedOrderId = $orderId;
        $this->productIdToReview = $productId;
        $this->showReviewModal = true;
    }

    public function submitReview()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        // Ensure the user is authenticated
        if (!Auth::check()) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'You must be logged in to submit a review.',
            ]);
            return;
        }

        // Ensure the order exists
        $order = Order::find($this->selectedOrderId);
        if (!$order) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'The selected order does not exist.',
            ]);
            return;
        }

        // Check if the user has already reviewed this product for this order
        $existingReview = Review::where('product_id', $this->productIdToReview)
            ->where('user_id', Auth::id())
            ->where('order_id', $this->selectedOrderId)
            ->first();

        if ($existingReview) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'You have already reviewed this product for this order.',
            ]);
        } else {
            Review::create([
                'product_id' => $this->productIdToReview,
                'user_id' => Auth::id(),
                'order_id' => $this->selectedOrderId,
                'rating' => $this->rating,
                'comment' => $this->comment,
            ]);

            $this->showReviewModal = false;
            $this->reset(['productIdToReview', 'rating', 'comment', 'selectedOrderId']);
            
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Review submitted successfully!',
                'icon' => 'success',
            ]);
        }
    }

    public function confirmCancelOrder($orderId)
    {
        $this->orderIdToCancel = $orderId;
        $this->showCancelConfirmation = true;
    }

    public function cancelOrder()
    {
        $order = Order::findOrFail($this->orderIdToCancel);

        if ($order->status !== Order::STATUS_COMPLETED) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
            $this->dispatch('orderCancelled');
        }

        $this->showCancelConfirmation = false;
        $this->orderIdToCancel = null;

        return redirect()->route('orders');
    }

    public function confirmRefundOrder($orderId)
    {
        $this->orderToRefund = $orderId;
        $this->showRefundConfirmation = true;
    }

    public function refundOrder()
    {
        $order = Order::find($this->orderToRefund);
        if ($order) {
            // Perform the refund logic here
            $order->status = 'refunded';
            $order->save();

            // You might want to add more logic here, such as
            // updating inventory, notifying the customer, etc.

            $this->showRefundConfirmation = false;
            $this->orderToRefund = null;

            session()->flash('message', 'Order refunded successfully.');
        }
    }

    public function viewOrderDetails($orderId)
    {
        $this->selectedOrder = Order::with('orderItems.product')->find($orderId);
        $this->showInvoice = false;
    }

    public function generateInvoice($orderId)
    {
        $this->selectedOrder = Order::with(['orderItems.product', 'customer', 'billingAddress', 'shippingAddress'])->find($orderId);
        $this->showInvoice = true;
    }

    public function render()
    {
        $orders = auth()->user()->orders()->latest()->paginate($this->perPage);
        return view('livewire.orders', ['orders' => $orders]);
    }
}

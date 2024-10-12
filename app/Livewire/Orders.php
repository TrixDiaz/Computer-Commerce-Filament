<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class Orders extends Component
{
    public $orders;
    public $selectedOrder;
    public $showInvoice = false;

    public function mount()
    {
        $this->orders = auth()->user()->orders()->latest()->get();
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
        return view('livewire.orders');
    }
}
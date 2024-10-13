<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class Orders extends Component
{
    public $orders;
    public $selectedOrder;
    public $showInvoice = false;
    public $showCancelConfirmation = false;
    public $orderIdToCancel;
    public $showRefundConfirmation = false;
    public $orderToRefund;

    public function mount()
    {
        $this->orders = auth()->user()->orders()->latest()->get();
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
        return view('livewire.orders');
    }
}

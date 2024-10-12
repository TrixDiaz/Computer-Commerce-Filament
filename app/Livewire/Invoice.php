<?php

namespace App\Livewire;

use Livewire\Component;

class Invoice extends Component
{
    public $order;

    public function mount($order)
    {
        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.invoice');
    }
}

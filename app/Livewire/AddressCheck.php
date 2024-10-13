<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AddressCheck extends Component
{
    public $showModal = false;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->addresses->isEmpty()) {
            $this->showModal = true;
        }
    }

    public function render()
    {
        return view('livewire.address-check');
    }

    public function redirectToAddress()
    {
        return redirect()->route('address');
    }
}
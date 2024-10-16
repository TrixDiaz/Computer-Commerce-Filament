<?php

namespace App\Livewire;

use Livewire\Component;

class Motherboard extends Component
{
    public $modelPath;

    public function mount($modelPath = null)
    {
        $this->modelPath = $modelPath ?? null;
    }

    public function render()
    {
        return view('livewire.motherboard');
    }
}

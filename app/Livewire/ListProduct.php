<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ListProduct extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.list-product', [
            'products' => Product::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orderBy(DB::raw('RAND()'))
                ->paginate(12)
        ]);
    }
}

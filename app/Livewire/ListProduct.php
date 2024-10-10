<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ListProduct extends Component
{
    use WithPagination;

    public $sortBy = 'random';
    public $sortDirection = 'asc';
    public $perPage = 12;
    public $search = '';
    public $minPrice = 0;
    public $maxPrice = 10000;
    public $selectedBrands = [];
    public $selectedColors = [];
    public $selectedRating = null;
    public $sortOptions = [
        'random' => 'Random',
        'name' => 'Name',
        'price' => 'Price',
        'created_at' => 'Date Added',
        // Add more sorting options as needed
    ];

    protected $queryString = [
        'sortBy' => ['except' => 'random'],
        'sortDirection' => ['except' => 'asc'],
        'search' => ['except' => ''],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 10000],
        'selectedBrands' => ['except' => []],
        'selectedColors' => ['except' => []],
        'selectedRating' => ['except' => null],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy($value)
    {
        if ($value !== 'random') {
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->minPrice, fn($query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice, fn($query) => $query->where('price', '<=', $this->maxPrice))
            ->when($this->selectedBrands, fn($query) => $query->whereIn('brand', $this->selectedBrands))
            ->when($this->selectedColors, fn($query) => $query->whereIn('color', $this->selectedColors))
            ->when($this->selectedRating, fn($query) => $query->where('rating', '>=', $this->selectedRating));

        if ($this->sortBy === 'random') {
            $products = $products->inRandomOrder();
        } else {
            $products = $products->orderBy($this->sortBy, $this->sortDirection);
        }

        $products = $products->paginate($this->perPage);

        return view('livewire.list-product', [
            'products' => $products,
        ]);
    }
}

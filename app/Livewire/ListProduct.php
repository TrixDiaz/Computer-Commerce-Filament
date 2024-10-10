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

    public function sortBy($field)
    {
        if ($field === 'random') {
            $this->sortBy = 'random';
            // No need for sort direction when using random
            $this->sortDirection = null;
        } else {
            if ($this->sortBy === $field) {
                $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                $this->sortDirection = 'asc';
            }
            $this->sortBy = $field;
        }
    }

    public function applyFilters()
    {
        // This method will be called when the "Apply Filters" button is clicked
        // You can add any additional logic here if needed
    }

    public function render()
    {
        $query = Product::query()
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->minPrice, function ($query) {
                return $query->where('price', '>=', $this->minPrice);
            })
            ->when($this->maxPrice, function ($query) {
                return $query->where('price', '<=', $this->maxPrice);
            })
            ->when($this->selectedBrands, function ($query) {
                return $query->whereIn('brand', $this->selectedBrands);
            })
            ->when($this->selectedColors, function ($query) {
                return $query->whereIn('color', $this->selectedColors);
            })
            ->when($this->selectedRating, function ($query) {
                return $query->where('rating', '>=', $this->selectedRating);
            });

        // Handle random sorting separately
        if ($this->sortBy === 'random') {
            $query->inRandomOrder();
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $products = $query->paginate($this->perPage);

        return view('livewire.list-product', [
            'products' => $products,
        ]);
    }
}
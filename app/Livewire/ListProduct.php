<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ListProduct extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $sort = '';

    public $showFilterModal = false;

    #[Url]
    public $selectedCategories = [];

    #[Url]
    public $selectedBrands = [];

    #[Url]
    public $minPrice = null;

    #[Url]
    public $maxPrice = null;

    #[Url]
    public $onSale = false;

    #[Url]
    public $isNew = false;

    #[Url]
    public $isBestSeller = false;

    #[Url]
    public $isTopRated = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    public function toggleFilterModal()
    {
        $this->showFilterModal = !$this->showFilterModal;
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->showFilterModal = false;
    }

    public function resetFilters()
    {
        $this->selectedCategories = [];
        $this->selectedBrands = [];
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->onSale = false;
        $this->isNew = false;
        $this->isBestSeller = false;
        $this->isTopRated = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->selectedCategories)) {
            $query->whereIn('category_id', $this->selectedCategories);
        }

        if (!empty($this->selectedBrands)) {
            $query->whereIn('brand_id', $this->selectedBrands);
        }

        if ($this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        if ($this->onSale) {
            $query->where('is_on_sale', true);
        }

        if ($this->isNew) {
            $query->where('is_new', true);
        }

        if ($this->isBestSeller) {
            $query->where('is_best_seller', true);
        }

        if ($this->isTopRated) {
            $query->where('is_top_rated', true);
        }

        switch ($this->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy(DB::raw('RAND()'));
        }

        return view('livewire.list-product', [
            'products' => $query->paginate(12),
            'categories' => Category::all(),
            'brands' => Brand::all(),
        ]);
    }

    public function addToCart($productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);
        
        if(isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                "name" => $product->name,
                "slug" => $product->slug,
                "description" => $product->description,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image_url,
                "sku" => $product->sku,
                "category_id" => $product->category_id,
                "brand_id" => $product->brand_id,
                "stock_quantity" => $product->stock_quantity,
                "is_featured" => $product->is_featured,
                "is_active" => $product->is_active,
                "is_sale" => $product->is_sale,
                "is_new" => $product->is_new,
                "is_best_seller" => $product->is_best_seller,
                "is_top_rated" => $product->is_top_rated,
                "is_on_sale" => $product->is_on_sale,
            ];
        }
        
        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Item added to cart successfully!',
            'icon' => 'success',
            'timer' => 3000,
        ]);
    }

    public function redirectToProduct($slug)
    {
        return Redirect::route('product-profile', ['slug' => $slug]);
    }
}

<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.header')]
#[Title('Productos')]
class ProductListing extends Component
{
    use WithPagination;

    public string $search = '';

    public mixed $category = null;

    public mixed $selectedBrand = null;

    public mixed $priceMin = null;

    public mixed $priceMax = null;

    public string $sortBy = '';

    public string $isPhysical = 'all';

    public string $inStock = 'all';

    public int $perPage = 12;

    public bool $filter = false;

    public bool $showDropdown = false;

    #[On('showCategory')]
    public function showCategory(int|string $id): void
    {
        $this->clearFilters();
        $this->filter = true;
        $this->showDropdown = false;
        $this->category = $id;
        $this->resetPage();
    }

    public function updatedSearch(mixed $value): void
    {
        $this->clearFilters();
        $this->filter = true;
        $this->search = (string) $value;
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'category',
            'selectedBrand',
            'priceMin',
            'priceMax',
            'sortBy',
            'isPhysical',
            'inStock',
            'filter',
        ]);
        $this->resetPage();
    }

    public function render(): View
    {
        $productsQuery = Product::where('is_active', true)->with(['brand', 'category', 'latestImage']);

        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        if (! empty($this->search)) {
            $productsQuery->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->category)) {
            $productsQuery->where('category_id', $this->category);
        }

        if (! empty($this->selectedBrand)) {
            $productsQuery->where('brand_id', $this->selectedBrand);
        }

        if (! empty($this->priceMin)) {
            $productsQuery->where('final_price', '>=', (float) $this->priceMin);
        }

        if (! empty($this->priceMax)) {
            $productsQuery->where('final_price', '<=', (float) $this->priceMax);
        }

        if ($this->isPhysical !== 'all') {
            $productsQuery->where('is_physical', (bool) $this->isPhysical);
        }

        if ($this->inStock !== 'all') {
            if ($this->inStock === '1') {
                $productsQuery->where(function ($query) {
                    $query->where('stock', '>', 0)
                        ->orWhere('allow_backorder', true);
                });
            } elseif ($this->inStock === '0') {
                $productsQuery->where(function ($query) {
                    $query->where('stock', '<=', 0)
                        ->where('allow_backorder', false);
                });
            }
        }

        $sortOptions = [
            'price_asc' => ['final_price', 'asc'],
            'price_desc' => ['final_price', 'desc'],
            'name_asc' => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
            'newest' => ['created_at', 'desc'],
        ];

        $sortColumn = $sortOptions[$this->sortBy] ?? ['pts_base', 'desc'];
        $productsQuery->orderBy(...$sortColumn);

        $products = $productsQuery->paginate($this->perPage);

        return view('livewire.product.product-listing', [
            'products' => $products,
            'brands' => $brands,
        ]);
    }
}

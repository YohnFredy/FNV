<?php

namespace App\Livewire\Product;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CategoryListing extends Component
{
    public $categories;

    public function mount(): void
    {
        $this->categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();
    }

    public function category(int|string $category_id): void
    {
        $this->dispatch('showCategory', id: $category_id);
    }

    public function render(): View
    {
        return view('livewire.product.category-listing');
    }
}

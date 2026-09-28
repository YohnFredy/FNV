<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Traits\HasCrudPermissions;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Gestión de Productos')]
class ProductIndex extends Component
{
    use HasCrudPermissions;
    use WithoutUrlPagination, WithPagination;

    public string $search = '';

    public array $searchTerms = [];

    protected function permissionModule(): string
    {
        return 'products';
    }

    public function mount(): void
    {
        $this->authorizeView();
    }

    public function searchEnter(): void
    {
        if (empty(trim($this->search))) {
            $this->clearSearch();
        } else {
            $this->searchTerms = array_filter(explode(' ', trim($this->search)));
            $this->resetPage();
        }
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->searchTerms = [];
        $this->resetPage();
    }

    public function destroy(int $id): void
    {
        $this->authorizeDelete();

        $product = Product::findOrFail($id);
        $product->delete();

        session()->flash('success', 'El producto fue eliminado correctamente.');
    }

    public function render(): View
    {
        $productsQuery = Product::query()->with(['category', 'brand', 'latestImage']);

        if (! empty($this->searchTerms)) {
            foreach ($this->searchTerms as $term) {
                $productsQuery->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%'.$term.'%')
                        ->orWhere('description', 'like', '%'.$term.'%')
                        ->orWhere('slug', 'like', '%'.$term.'%');
                });
            }
        }

        $products = $productsQuery->orderBy('id', 'desc')->paginate(10);

        return view('livewire.admin.products.product-index', [
            'products' => $products,
        ]);
    }
}

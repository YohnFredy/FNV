<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Traits\HasCrudPermissions;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Gestión de Categorías')]
class CategoryIndex extends Component
{
    use HasCrudPermissions;
    use WithoutUrlPagination, WithPagination;

    public string $search = '';

    public array $searchTerms = [];

    protected function permissionModule(): string
    {
        return 'categories';
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

        $category = Category::findOrFail($id);
        $category->delete();

        session()->flash('success', 'La categoría fue eliminada correctamente.');
    }

    public function render(): View
    {
        $categoriesQuery = Category::query()->with('parent');

        if (! empty($this->searchTerms)) {
            foreach ($this->searchTerms as $term) {
                $categoriesQuery->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%'.$term.'%')
                        ->orWhere('slug', 'like', '%'.$term.'%');
                });
            }
        }

        $categories = $categoriesQuery->orderBy('id', 'desc')->paginate(10);

        return view('livewire.admin.categories.category-index', [
            'categories' => $categories,
        ]);
    }
}

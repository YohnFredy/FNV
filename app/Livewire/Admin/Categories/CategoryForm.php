<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Traits\HasCrudPermissions;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Formulario de Categoría')]
class CategoryForm extends Component
{
    use HasCrudPermissions;

    public ?Category $category = null;

    public string $name = '';

    public string $slug = '';

    public bool $is_active = true;

    public bool $is_final = false;

    public ?int $parent_id = null;

    public array $selectedLevels = [];

    public array $categoryLevels = [];

    public bool $isEditMode = false;

    protected function permissionModule(): string
    {
        return 'categories';
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->category?->id),
            ],
            'parent_id' => 'nullable|integer|exists:categories,id',
            'is_final' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];
    }

    public function mount(?Category $category = null): void
    {
        $this->category = $category ?? new Category;

        $this->categoryLevels[0] = Category::whereNull('parent_id')->get();

        if ($this->category->exists) {
            $this->authorizeEdit();
            $this->isEditMode = true;
            $this->loadCategoryData();
        } else {
            $this->authorizeCreate();
        }
    }

    public function loadCategoryData(): void
    {
        $this->name = $this->category->name ?? '';
        $this->slug = $this->category->slug ?? '';
        $this->is_active = (bool) ($this->category->is_active ?? true);
        $this->is_final = (bool) ($this->category->is_final ?? false);
        $this->parent_id = $this->category->parent_id;

        $parentChain = collect();
        $currentCategory = $this->category->parent;

        while ($currentCategory) {
            $parentChain->prepend($currentCategory);
            $currentCategory = $currentCategory->parent;
        }

        $level = 0;
        foreach ($parentChain as $parent) {
            $this->selectedLevels[$level] = $parent->id;
            $this->categoryLevels[$level + 1] = Category::where('parent_id', $parent->id)->get();
            $level++;
        }

        if ($this->category->parent_id) {
            $this->selectedLevels[$level] = $this->category->parent_id;
        }
    }

    public function updatedSelectedLevels(mixed $value, int|string $key): void
    {
        $intKey = (int) $key;

        if (empty($value)) {
            $this->clearLevelsFrom($intKey);
            $this->parent_id = $intKey > 0 ? ($this->selectedLevels[$intKey - 1] ?? null) : null;

            return;
        }

        $this->clearLevelsFrom($intKey + 1);
        $this->parent_id = (int) $value;

        $subcategories = Category::where('parent_id', $value)->get();

        if ($subcategories->isNotEmpty()) {
            $this->categoryLevels[$intKey + 1] = $subcategories;
        } else {
            unset($this->categoryLevels[$intKey + 1]);
        }
    }

    protected function clearLevelsFrom(int $startLevel): void
    {
        foreach ($this->selectedLevels as $level => $selectedId) {
            if ($level >= $startLevel) {
                unset($this->selectedLevels[$level]);
                unset($this->categoryLevels[$level + 1]);
            }
        }
    }

    public function save(): mixed
    {
        $this->authorizeCreate();

        $validatedData = $this->validate();

        Category::create($validatedData);

        session()->flash('success', 'Categoría creada exitosamente.');

        return redirect()->route('admin.categories.index');
    }

    public function update(): mixed
    {
        $this->authorizeEdit();

        $validatedData = $this->validate();

        $this->category->update($validatedData);

        session()->flash('success', 'Categoría actualizada exitosamente.');

        return redirect()->route('admin.categories.index');
    }

    public function render(): View
    {
        return view('livewire.admin.categories.category-form');
    }
}

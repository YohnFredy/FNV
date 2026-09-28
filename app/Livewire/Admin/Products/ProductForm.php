<?php

namespace App\Livewire\Admin\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Traits\HasCrudPermissions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Formulario de Producto')]
class ProductForm extends Component
{
    use HasCrudPermissions;
    use WithFileUploads;

    public ?Product $product = null;

    public string $name = '';

    public ?string $description = '';

    public float $price = 0;

    public float $final_price = 0;

    public float $tax_percent = 19;

    public int $commission_income = 0;

    public float $pts_base = 0;

    public float $pts_bonus = 0;

    public float $pts_dist = 0;

    public int $maximum_discount = 0;

    public ?string $specifications = '';

    public ?string $information = '';

    public bool $is_physical = true;

    public ?int $stock = 0;

    public bool $allow_backorder = true;

    public mixed $category_id = '';

    public ?int $brand_id = null;

    public bool $is_active = true;

    public array $newImages = [];

    public array $images = [];

    public bool $isEditMode = false;

    public ?Category $category = null;

    public array $selectedLevels = [];

    public array $categoryLevels = [];

    public bool $hasChildCategories = false;

    protected function permissionModule(): string
    {
        return 'products';
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0|max:9999999.99',
            'final_price' => 'numeric|min:0|max:9999999.99',
            'tax_percent' => 'numeric|min:0|max:99',
            'commission_income' => 'numeric|min:0|max:999999',
            'pts_base' => 'numeric|min:0|max:999999.99',
            'pts_bonus' => 'numeric|min:0|max:999999.99',
            'pts_dist' => 'numeric|min:0|max:999999.99',
            'maximum_discount' => 'integer|min:0|max:100',
            'specifications' => 'nullable|string',
            'information' => 'nullable|string',
            'is_physical' => 'required|boolean',
            'stock' => 'nullable|required_if:is_physical,true|integer|min:0',
            'allow_backorder' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'required|boolean',
            'newImages.*' => 'nullable|image|max:3072',
            'hasChildCategories' => 'required|boolean|accepted',
        ];
    }

    private function getValidValue(mixed $value): float
    {
        return empty($value) ? 0.0 : floatval($value);
    }

    public function updatedFinalPrice(mixed $valor): void
    {
        $this->final_price = $this->getValidValue($valor);
        $tax = $this->getValidValue($this->tax_percent);

        $this->price = round($this->final_price / (1 + ($tax / 100)), 2);
        $this->final_price = round(($this->price * $tax / 100) + $this->price, 2);
    }

    public function updatedTaxPercent(mixed $valor): void
    {
        $tax = $this->getValidValue($valor);
        $this->final_price = $this->getValidValue($this->final_price);

        $this->price = round($this->final_price / (1 + ($tax / 100)), 2);
        $this->final_price = round(($this->price * $tax / 100) + $this->price, 2);
    }

    public function updatedPrice(mixed $valor): void
    {
        $this->price = $this->getValidValue($valor);
        $tax = $this->getValidValue($this->tax_percent);

        $this->final_price = round(($this->price * $tax / 100) + $this->price, 2);
    }

    public function mount(?Product $product = null): void
    {
        $this->product = $product ?? new Product;

        $this->categoryLevels[0] = Category::whereNull('parent_id')->get();

        if ($this->product->exists) {
            $this->authorizeEdit();
            $this->isEditMode = true;
            $this->loadProductData();
            $this->loadCategoryData();
        } else {
            $this->authorizeCreate();
        }
    }

    public function loadProductData(): void
    {
        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->price = (float) $this->product->price;
        $this->final_price = (float) ($this->product->final_price ?: round($this->price * (1 + ($this->product->tax_percent / 100)), 2));
        $this->tax_percent = (float) $this->product->tax_percent;
        $this->commission_income = (int) $this->product->commission_income;
        $this->pts_base = (float) $this->product->pts_base;
        $this->pts_bonus = (float) $this->product->pts_bonus;
        $this->pts_dist = (float) $this->product->pts_dist;
        $this->maximum_discount = (int) $this->product->maximum_discount;
        $this->specifications = $this->product->specifications;
        $this->information = $this->product->information;
        $this->is_physical = (bool) $this->product->is_physical;
        $this->stock = $this->product->stock;
        $this->allow_backorder = (bool) $this->product->allow_backorder;
        $this->category_id = $this->product->category_id;
        $this->brand_id = $this->product->brand_id;
        $this->is_active = (bool) $this->product->is_active;

        $this->images = $this->product->images->pluck('path', 'id')->toArray();
    }

    public function loadCategoryData(): void
    {
        $this->category = Category::find($this->product->category_id);

        if (! $this->category) {
            return;
        }

        $parentChain = collect();
        $currentCategory = $this->category;

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

        $this->category_id = $this->category->id;
        $this->hasChildCategories = $this->category->children()->count() === 0;
    }

    #[On('calculadora-financiera')]
    public function updateDatos(
        float $precioPublico,
        float $ivaPorcentaje,
        float $pts_base,
        float $bonoInicioPorcentaje,
        float $pts_bono,
        float $descuentoPorcentaje,
        float $pts_dist
    ): void {
        $this->final_price = $precioPublico;
        $this->tax_percent = $ivaPorcentaje;
        $this->commission_income = (int) $bonoInicioPorcentaje;
        $this->pts_base = $pts_base;
        $this->pts_bonus = $pts_bono;
        $this->pts_dist = $pts_dist;
        $this->maximum_discount = (int) $descuentoPorcentaje;

        $this->updatedFinalPrice($this->final_price);
    }

    public function updatedSelectedLevels(mixed $value, int|string $key): void
    {
        $intKey = (int) $key;
        $this->clearLevelsFrom($intKey + 1);

        $this->category_id = $value;

        if (empty($value)) {
            $this->hasChildCategories = false;

            return;
        }

        $subcategories = Category::where('parent_id', $value)->get();

        if ($subcategories->isNotEmpty()) {
            $this->categoryLevels[$intKey + 1] = $subcategories;
            $this->hasChildCategories = false;
        } else {
            unset($this->categoryLevels[$intKey + 1]);
            $this->hasChildCategories = true;
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
        unset($validatedData['hasChildCategories'], $validatedData['newImages'], $validatedData['final_price']);

        $validatedData['slug'] = Product::generateSlug($this->name);

        $product = Product::create($validatedData);
        $this->saveImages($product);

        session()->flash('success', 'Producto creado exitosamente.');

        return redirect()->route('admin.products.index');
    }

    public function update(): mixed
    {
        $this->authorizeEdit();

        $validatedData = $this->validate();
        unset($validatedData['hasChildCategories'], $validatedData['newImages'], $validatedData['final_price']);

        $this->product->update($validatedData);
        $this->saveImages($this->product);

        session()->flash('success', 'Producto actualizado exitosamente.');

        return redirect()->route('admin.products.index');
    }

    public function removeMedia(int $mediaId): void
    {
        $image = Image::find($mediaId);

        if (! $image) {
            return;
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        unset($this->images[$mediaId]);
    }

    protected function saveImages(Product $product): void
    {
        foreach ($this->newImages as $image) {
            $path = $image->storeAs(
                'products',
                Str::slug($this->name).'-'.uniqid().'.'.$image->getClientOriginalExtension(),
                'public'
            );
            $product->images()->create(['path' => $path]);
        }
    }

    public function render(): View
    {
        return view('livewire.admin.products.product-form', [
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}

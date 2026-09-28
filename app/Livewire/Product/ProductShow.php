<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.header')]
class ProductShow extends Component
{
    public Product $product;

    public int $quantity = 1;

    public int $currentImageIndex = 0;

    public bool $modalCart = false;

    public array $cart = [];

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->cart = session()->get('cart', []);
    }

    public function incrementQuantity(): void
    {
        if ($this->product->is_physical && $this->product->stock !== null) {
            if ($this->quantity < $this->product->stock || $this->product->allow_backorder) {
                $this->quantity++;
            }
        } else {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function changeImage(int $index): void
    {
        $this->currentImageIndex = $index;
    }

    public function addToCart(): void
    {
        $this->cart = session()->get('cart', []);

        $index = array_search($this->product->id, array_column($this->cart, 'id'));

        if ($index !== false) {
            $this->cart[$index]['quantity'] += $this->quantity;
        } else {
            $this->cart[] = [
                'id' => $this->product->id,
                'quantity' => $this->quantity,
            ];
        }

        session()->put('cart', $this->cart);

        $this->modalCart = true;

        $this->dispatch('update-cart');
    }

    public function render(): View
    {
        $title = "{$this->product->name} - Catálogo Oficial";

        $cart = session()->get('cart', []);
        $cartTotalUnits = collect($cart)->sum('quantity');

        $productIds = collect($cart)->pluck('id');
        $productsDB = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $cartSubtotal = collect($cart)->sum(function ($item) use ($productsDB) {
            $p = $productsDB[$item['id']] ?? null;

            return $p ? ($p->final_price * $item['quantity']) : 0;
        });

        return view('livewire.product.product-show', [
            'cartItems' => $cart,
            'cartTotalUnits' => $cartTotalUnits,
            'cartSubtotal' => $cartSubtotal,
        ])->title($title);
    }
}

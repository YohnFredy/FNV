<?php

namespace App\Livewire\Product;

use App\Models\ActivationPt;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Cart extends Component
{
    public $cart = [];

    public $products = [];

    public $quantity = 1;

    public $total;

    public $user;

    public $activationPt;

    public $tipo_usuario = 'inactive';

    public $productItems = [];

    // Variables organizadas para cálculos
    public $totals = [
        'subtotal' => 0,
        'descuento' => 0,
        'total_bruto_factura' => 0,
        'iva' => 0,
        'total_factura' => 0,
        'quantity' => 0,
        'total_pts' => 0,
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->activationPt = ActivationPt::first();
        $this->showProducts();
    }

    public function showProducts()
    {
        $this->productItems = [];
        $this->cart = session()->get('cart', []);
        $productIds = collect($this->cart)->pluck('id');
        $productsDB = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Mapeamos los productos del carrito con la info de la BD
        $this->products = collect($this->cart)->map(function ($item, $index) use ($productsDB) {
            $product = $productsDB[$item['id']] ?? null;

            if (! $product) {
                return null;
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'tax_percent' => $product->tax_percent,
                'final_price' => $product->final_price,
                'pts_base' => $product->pts_base,
                'pts_bonus' => $product->pts_bonus,
                'pts_dist' => $product->pts_dist,
                'maximum_discount' => $product->maximum_discount,
                'quantity' => $item['quantity'],
                'path' => $product->latestImage?->path ?? '',
                'index' => $index,
            ];
        })->filter();

        // Determinar tipo de usuario
        $totalPts = collect($this->products)->sum(fn ($product) => $product['pts_base'] * $product['quantity']);

        $minFirst = $this->activationPt?->min_pts_first ?? 1.8;
        $minMonthly = $this->activationPt?->min_pts_monthly ?? 1.8;

        $this->tipo_usuario = match (true) {
            $this->user?->activation?->is_active => 'active',
            $this->user?->activation && ! $this->user->activation->is_active && $totalPts >= $minMonthly => 'renew_activation',
            ! $this->user?->activation && $totalPts >= $minFirst => 'new_affiliate',
            default => 'inactive',
        };

        // Construir items del carrito según tipo de usuario
        match ($this->tipo_usuario) {
            'new_affiliate' => $this->optimize($minFirst),
            'renew_activation' => $this->optimize($minMonthly),
            'active' => $this->buildCartItems($this->products, useDistributorPts: true),
            default => $this->buildCartItems($this->products, useDistributorPts: false, discount: 0),
        };
        $this->calculateTotals();
    }

    /**
     * Optimiza el carrito para cumplir con un mínimo de puntos requeridos.
     */
    public function optimize(float $minPts)
    {
        $productsUnit = [];
        $index = 0;

        foreach ($this->products as $product) {
            for ($i = 0; $i < $product['quantity']; $i++) {
                $productsUnit[] = [
                    'index' => $index++,
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'tax_percent' => $product['tax_percent'],
                    'pts_base' => $product['pts_base'],
                    'pts_bonus' => $product['pts_bonus'],
                    'pts_dist' => $product['pts_dist'],
                    'discount_percent' => $product['maximum_discount'],
                    'quantity' => 1,
                ];
            }
        }

        $bestCombination = $this->findBestPtsCombination($productsUnit, $minPts);
        $selectedIndexes = array_column($bestCombination, 'index');

        foreach ($productsUnit as $unit) {
            $isOptimized = in_array($unit['index'], $selectedIndexes);
            $pts = $isOptimized ? $unit['pts_base'] : $unit['pts_dist'];
            $discount = $isOptimized ? 0 : $unit['discount_percent'];

            $item = [
                'index' => $unit['index'],
                'product_id' => $unit['id'],
                'name' => $unit['name'],
                'price' => $unit['price'],
                'tax_percent' => $unit['tax_percent'],
                'pts' => $pts,
                'discount_percent' => $discount,
                'quantity' => 1,
            ];

            $key = md5(json_encode([
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'tax_percent' => $item['tax_percent'],
                'pts' => $item['pts'],
                'discount_percent' => $item['discount_percent'],
            ]));

            if (isset($this->productItems[$key])) {
                $this->productItems[$key]['quantity'] += 1;
            } else {
                $this->productItems[$key] = $item;
            }
        }
    }

    /**
     * Encuentra la mejor combinación de productos que cumplan con los puntos mínimos.
     */
    protected function findBestPtsCombination(array $units, float $target): array
    {
        $bestCombo = [];
        $bestSum = INF;
        $tolerance = 0.0001;

        for ($i = 1; $i <= count($units); $i++) {
            $combinations = $this->getCombinations($units, $i);

            foreach ($combinations as $combo) {
                $sum = array_sum(array_column($combo, 'pts_base'));

                if ($sum + $tolerance >= $target && $sum < $bestSum) {
                    $bestSum = $sum;
                    $bestCombo = $combo;
                }
            }
        }

        return $bestCombo;
    }

    /**
     * Genera todas las combinaciones posibles de $length elementos.
     */
    private function getCombinations(array $items, int $length): array
    {
        if ($length === 0) {
            return [[]];
        }
        if (empty($items)) {
            return [];
        }

        $head = $items[0];
        $tail = array_slice($items, 1);

        $combosWithHead = array_map(
            fn ($combo) => array_merge([$head], $combo),
            $this->getCombinations($tail, $length - 1)
        );

        $combosWithoutHead = $this->getCombinations($tail, $length);

        return array_merge($combosWithHead, $combosWithoutHead);
    }

    /**
     * Construye los elementos del carrito a partir de los productos.
     */
    protected function buildCartItems($products, bool $useDistributorPts = true, ?int $discount = null)
    {
        $this->productItems = [];

        foreach ($products as $item) {
            $this->productItems[] = [
                'product_id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'tax_percent' => $item['tax_percent'],
                'pts' => $useDistributorPts ? $item['pts_dist'] : $item['pts_base'],
                'discount_percent' => $discount ?? $item['maximum_discount'],
                'quantity' => $item['quantity'],
            ];
        }
    }

    public function calculateTotals()
    {
        $this->totals = array_fill_keys(array_keys($this->totals), 0);

        foreach ($this->productItems as &$item) {
            $subtotal = $item['price'] * $item['quantity'];
            $descuento = ($subtotal * $item['discount_percent']) / 100;
            $total_bruto_factura = $subtotal - $descuento;
            $iva = ($total_bruto_factura * $item['tax_percent']) / 100;
            $total_factura = $total_bruto_factura + $iva;
            $total_pts = $item['pts'] * $item['quantity'];

            $this->totals['subtotal'] += $subtotal;
            $this->totals['quantity'] += $item['quantity'];
            $this->totals['descuento'] += $descuento;
            $this->totals['total_bruto_factura'] += $total_bruto_factura;
            $this->totals['iva'] += $iva;
            $this->totals['total_factura'] += $total_factura;
            $this->totals['total_pts'] += $total_pts;
        }
    }

    public function increment($index)
    {
        if (isset($this->cart[$index]) && $this->cart[$index]['quantity'] <= 1000) {
            $this->cart[$index]['quantity'] += 1;
            session()->put('cart', $this->cart);
            $this->showProducts();
        }
    }

    public function decrement($index)
    {
        if (isset($this->cart[$index]) && $this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity'] -= 1;
            session()->put('cart', $this->cart);
            $this->showProducts();
        }
    }

    public function updatedProducts($value, $name)
    {
        [$index, $property] = explode('.', $name);

        if ($property === 'quantity' && isset($this->cart[$index])) {
            $value = max(1, min(1000, (int) $value));
            $this->cart[$index]['quantity'] = $value;
            session()->put('cart', $this->cart);
            $this->showProducts();
        }
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
            session()->put('cart', $this->cart);
            $this->showProducts();
        }
    }

    #[Layout('layouts.app.header')]
    public function render()
    {
        $this->dispatch('update-cart');

        return view('livewire.product.cart');
    }
}

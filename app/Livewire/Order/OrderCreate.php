<?php

namespace App\Livewire\Order;

use App\Models\ActivationPt;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Order;
use App\Models\OrderBillingData;
use App\Models\OrderItem;
use App\Models\Parish;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class OrderCreate extends Component
{
    public $cart = [];

    public $products = [];

    public $productItems = [];

    public $groupedItems = [];

    public $documentTypes = [];

    public $shipping_documentTypes = [];

    public $user;

    public $tipo_usuario = 'inactive';

    public $activationPt;

    public $shipping_cost = 0;

    public $shipping_type = 2;

    public $terms = false;

    // ===== Facturación =====
    #[Validate]
    public $name;

    public $document_type = 3;

    public $document;

    public $email;

    public $phone;

    public $address = '';

    #[Validate]
    public $countries = [];

    public $departments = [];

    public $cities = [];

    public $parishes = [];

    #[Validate]
    public $selectedCountry;

    public $selectedDepartment;

    public $selectedCity;

    public $selectedParish;

    public $city = '';

    // Dynamic Labels
    public $division1 = 'Departamento';

    public $division2 = 'Ciudad';

    public $division3 = 'Parroquia';

    // Dynamic Labels (Shipping)
    public $shipping_division1 = 'Departamento';

    public $shipping_division2 = 'Ciudad';

    public $shipping_division3 = 'Parroquia';

    // ===== Datos del que recibe =====
    #[Validate]
    public $shippingDifferent = false;

    public $shipping_name;

    public $shipping_document_type = 3;

    public $shipping_document;

    public $shipping_phone;

    #[Validate]
    public $shipping_countries = [];

    public $shipping_departments = [];

    public $shipping_cities = [];

    public $shipping_parishes = [];

    #[Validate]
    public $shipping_selectedCountry;

    public $shipping_selectedDepartment;

    public $shipping_selectedCity;

    public $shipping_selectedParish;

    public $shipping_city = '';

    #[Validate]
    public $shipping_address;

    public $shipping_additional_address;

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

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'document_type' => 'required',
            'document' => 'required|regex:/^[0-9]+$/|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'selectedCountry' => 'required',
            'selectedDepartment' => 'required',
            'selectedCity' => Rule::requiredIf(empty($this->city)),
            'city' => Rule::requiredIf(empty($this->selectedCity)),
            'address' => 'required|max:255',

            'shipping_selectedCountry' => Rule::requiredIf($this->shipping_type == 2),
            'shipping_selectedDepartment' => Rule::requiredIf($this->shipping_type == 2),
            'shipping_selectedCity' => Rule::requiredIf($this->shipping_type == 2 && empty($this->shipping_city)),
            'shipping_city' => Rule::requiredIf($this->shipping_type == 2 && empty($this->shipping_selectedCity)),
            'shipping_address' => Rule::requiredIf($this->shipping_type == 2),
            'shipping_additional_address' => 'nullable|string|max:255',

            'shipping_type' => ['required', Rule::in(['1', '2'])],
            'terms' => 'required|accepted',
        ];

        if ($this->shippingDifferent == true) {
            $rules = array_merge($rules, [
                'shipping_name' => 'required|string|max:255',
                'shipping_document_type' => 'required',
                'shipping_document' => 'required|regex:/^[0-9]+$/|max:50',
                'shipping_phone' => 'required|string|max:30',
            ]);
        }

        return $rules;
    }

    public function mount()
    {
        $this->user = Auth::user();
        $this->cart = session('cart', []);
        $this->activationPt = ActivationPt::first();
        $this->countries = Country::all();
        $this->shipping_countries = $this->countries;

        $this->loadData();

        $productIds = collect($this->cart)->pluck('id');
        $productsDB = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $this->products = collect($this->cart)->map(function ($item, $index) use ($productsDB) {
            $product = $productsDB[$item['id']] ?? null;

            if (! $product) {
                return null;
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'tax_percent' => $product->tax_percent,
                'pts_base' => $product->pts_base,
                'pts_bonus' => $product->pts_bonus,
                'pts_dist' => $product->pts_dist,
                'maximum_discount' => $product->maximum_discount,
                'quantity' => $item['quantity'],
                'index' => $index,
            ];
        })->filter();

        $totalPts = collect($this->products)->sum(fn ($product) => $product['pts_base'] * $product['quantity']);
        $minFirst = $this->activationPt?->min_pts_first ?? 1.8;
        $minMonthly = $this->activationPt?->min_pts_monthly ?? 1.8;

        $this->tipo_usuario = match (true) {
            $this->user?->activation?->is_active => 'active',
            $this->user?->activation && ! $this->user->activation->is_active && $totalPts >= $minMonthly => 'renew_activation',
            ! $this->user?->activation && $totalPts >= $minFirst => 'new_affiliate',
            default => 'inactive',
        };

        match ($this->tipo_usuario) {
            'new_affiliate' => $this->optimize($minFirst),
            'renew_activation' => $this->optimize($minMonthly),
            'active' => $this->buildCartItems($this->products, useDistributorPts: true),
            default => $this->buildCartItems($this->products, useDistributorPts: false, discount: 0),
        };

        $this->calculateTotals();
    }

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

        $this->calculateTotals();
    }

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

    public function loadData()
    {
        if (! $this->user) {
            return;
        }

        // Consultar datos de perfil y fallback a la última orden del usuario
        $userData = $this->user->userData;
        $lastOrder = $this->user->orders()->latest()->with('billingData')->first();
        $lastBilling = $lastOrder?->billingData;

        // 1. Nombre completo
        $fullName = trim(($this->user->name ?? '').' '.($this->user->last_name ?? ''));
        if (empty($fullName) && $lastBilling?->name) {
            $fullName = $lastBilling->name;
        }
        $this->name = ucwords(strtolower($fullName));

        // 2. Documento y Tipo de documento
        $this->document = ! empty($this->user->dni) ? $this->user->dni : ($lastBilling?->document ?? '');
        $this->document_type = $this->user->document_type_id ?? ($lastBilling?->document_type_id ?? 1);

        // 3. Email y Teléfono
        $this->email = $this->user->email ?? ($lastBilling?->email ?? '');
        $this->phone = $userData?->phone ?? ($lastBilling?->phone ?? '');

        // 4. Dirección
        $this->address = $userData?->address ?? ($lastBilling?->address ?? '');

        // 5. País (del usuario, última orden, o Colombia por defecto)
        $userCountryId = $userData?->country_id ?? ($lastBilling?->country_id ?? null);
        $colombia = Country::where('name', 'like', '%Colombia%')->first();
        $this->selectedCountry = $userCountryId ?? ($colombia?->id ?? Country::first()?->id);

        // Departamentos del país
        $this->departments = Department::where('country_id', $this->selectedCountry)->orderBy('name', 'asc')->get();

        // Seleccionar departamento
        $userDeptId = $userData?->department_id ?? ($lastBilling?->department_id ?? null);
        if ($userDeptId && $this->departments->contains('id', $userDeptId)) {
            $this->selectedDepartment = $userDeptId;
        } elseif ($this->departments->count() === 1) {
            $this->selectedDepartment = $this->departments->first()->id;
        }

        // Ciudades del departamento seleccionado
        if ($this->selectedDepartment) {
            $this->cities = City::where('department_id', $this->selectedDepartment)->orderBy('name', 'asc')->get();
            $userCityId = $userData?->city_id ?? ($lastBilling?->city_id ?? null);
            if ($userCityId && $this->cities->contains('id', $userCityId)) {
                $this->selectedCity = $userCityId;
            } elseif ($this->cities->count() === 1) {
                $this->selectedCity = $this->cities->first()->id;
            }
        } else {
            $this->cities = collect();
        }

        if ($this->selectedCity) {
            $this->parishes = Parish::where('city_id', $this->selectedCity)->orderBy('name', 'asc')->get();
            $this->selectedParish = null;
        }

        $this->city = $userData?->city ?? ($lastBilling?->addCity ?? '');

        // 6. Datos de Envío (por defecto reflejar la facturación o la última dirección de envío)
        $this->shipping_selectedCountry = $lastOrder?->shipping_country_id ?? $this->selectedCountry;
        $this->shipping_departments = Department::where('country_id', $this->shipping_selectedCountry)->orderBy('name', 'asc')->get();

        $shippingDeptId = $lastOrder?->shipping_department_id ?? $this->selectedDepartment;
        if ($shippingDeptId && $this->shipping_departments->contains('id', $shippingDeptId)) {
            $this->shipping_selectedDepartment = $shippingDeptId;
        } elseif ($this->shipping_departments->count() === 1) {
            $this->shipping_selectedDepartment = $this->shipping_departments->first()->id;
        }

        if ($this->shipping_selectedDepartment) {
            $this->shipping_cities = City::where('department_id', $this->shipping_selectedDepartment)->orderBy('name', 'asc')->get();
            $shippingCityId = $lastOrder?->shipping_city_id ?? $this->selectedCity;
            if ($shippingCityId && $this->shipping_cities->contains('id', $shippingCityId)) {
                $this->shipping_selectedCity = $shippingCityId;
            } elseif ($this->shipping_cities->count() === 1) {
                $this->shipping_selectedCity = $this->shipping_cities->first()->id;
            }
        } else {
            $this->shipping_cities = collect();
        }

        $this->shipping_city = $lastOrder?->shipping_addCity ?? $this->city;
        $this->shipping_address = $lastOrder?->shipping_address ?? $this->address;
        $this->shipping_additional_address = $lastOrder?->shipping_additional_address ?? '';

        if ($this->shipping_selectedCity) {
            $this->shipping_parishes = Parish::where('city_id', $this->shipping_selectedCity)->orderBy('name', 'asc')->get();
            $cityModel = City::find($this->shipping_selectedCity);
            $this->shipping_cost = $cityModel->cost ?? 0;
        }

        // 7. Tipos de Documento y Divisiones
        if ($this->selectedCountry) {
            $this->documentTypes = DocumentType::where('country_id', $this->selectedCountry)->where('is_active', true)->get();
            $this->shipping_documentTypes = $this->documentTypes;

            if (! $this->document_type || ! $this->documentTypes->contains('id', $this->document_type)) {
                $defaultDoc = $this->documentTypes->firstWhere('is_default', true) ?? $this->documentTypes->first();
                if ($defaultDoc) {
                    $this->document_type = $defaultDoc->id;
                    $this->shipping_document_type = $defaultDoc->id;
                }
            }

            $country = Country::find($this->selectedCountry);
            if ($country) {
                $this->division1 = $country->division_term_1 ?? 'Departamento';
                $this->division2 = $country->division_term_2 ?? 'Ciudad';
                $this->division3 = $country->division_term_3 ?? 'Parroquia';
            }
        }

        if ($this->shipping_selectedCountry) {
            $country = Country::find($this->shipping_selectedCountry);
            if ($country) {
                $this->shipping_division1 = $country->division_term_1 ?? 'Departamento';
                $this->shipping_division2 = $country->division_term_2 ?? 'Ciudad';
                $this->shipping_division3 = $country->division_term_3 ?? 'Parroquia';
            }
        }
    }

    public function updatedSelectedCountry($countryId)
    {
        $this->reset(['departments', 'selectedDepartment', 'cities', 'selectedCity', 'city', 'parishes', 'selectedParish', 'documentTypes', 'document_type']);
        $this->departments = Department::where('country_id', $countryId)->orderBy('name', 'asc')->get();

        $country = Country::find($countryId);
        $this->division1 = $country->division_term_1 ?? 'Departamento';
        $this->division2 = $country->division_term_2 ?? 'Ciudad';
        $this->division3 = $country->division_term_3 ?? 'Parroquia';

        $this->documentTypes = DocumentType::where('country_id', $countryId)->where('is_active', true)->get();

        $defaultDoc = collect($this->documentTypes)->firstWhere('is_default', true);
        if ($defaultDoc) {
            $this->document_type = $defaultDoc['id'];
        } elseif (count($this->documentTypes) > 0) {
            $this->document_type = collect($this->documentTypes)->first()['id'];
        }
    }

    public function updatedSelectedDepartment($departmentId)
    {
        $this->reset(['cities', 'selectedCity', 'city', 'parishes', 'selectedParish']);
        $this->cities = City::where('department_id', $departmentId)->orderBy('name', 'asc')->get();
    }

    public function updatedSelectedCity($cityId)
    {
        $this->reset(['city', 'parishes', 'selectedParish']);
        $this->parishes = Parish::where('city_id', $cityId)->get();
    }

    public function updatedCity()
    {
        $this->reset('selectedParish');
    }

    public function updatedShippingSelectedCountry($shippingCountryId)
    {
        $this->reset(['shipping_departments', 'shipping_selectedDepartment', 'shipping_cities', 'shipping_selectedCity', 'shipping_city', 'shipping_parishes', 'shipping_selectedParish', 'shipping_documentTypes', 'shipping_document_type']);
        $this->shipping_departments = Department::where('country_id', $shippingCountryId)->orderBy('name', 'asc')->get();

        $country = Country::find($shippingCountryId);
        $this->shipping_division1 = $country->division_term_1 ?? 'Departamento';
        $this->shipping_division2 = $country->division_term_2 ?? 'Ciudad';
        $this->shipping_division3 = $country->division_term_3 ?? 'Parroquia';

        $this->shipping_documentTypes = DocumentType::where('country_id', $shippingCountryId)->where('is_active', true)->get();

        $defaultDoc = collect($this->shipping_documentTypes)->firstWhere('is_default', true);
        if ($defaultDoc) {
            $this->shipping_document_type = $defaultDoc['id'];
        } elseif (count($this->shipping_documentTypes) > 0) {
            $this->shipping_document_type = collect($this->shipping_documentTypes)->first()['id'];
        }
    }

    public function updatedShippingSelectedDepartment($shippingDepartmentId)
    {
        $this->reset(['shipping_cities', 'shipping_selectedCity', 'shipping_city', 'shipping_parishes', 'shipping_selectedParish']);
        $this->shipping_cities = City::where('department_id', $shippingDepartmentId)->orderBy('name', 'asc')->get();
    }

    public function updatedShippingSelectedCity($shippingCityId)
    {
        $this->reset(['shipping_city', 'shipping_parishes', 'shipping_selectedParish']);
        $this->shipping_parishes = Parish::where('city_id', $shippingCityId)->orderBy('name', 'asc')->get();

        $city = City::find($shippingCityId);
        $this->shipping_cost = $city->cost ?? 0;
    }

    public function updatedShippingCity()
    {
        $this->reset('shipping_selectedParish');
    }

    public function create_order()
    {
        $this->validate();

        try {
            return DB::transaction(function () {
                $publicOrderNumber = strtoupper(dechex(time()).bin2hex(random_bytes(4)));

                $orderData = [
                    'public_order_number' => $publicOrderNumber,
                    'user_id' => $this->user->id,
                    'status' => Order::STATUS_SALE_PENDING,
                    'shipping_type' => $this->shipping_type,
                    // === Totales ===
                    'subtotal' => $this->totals['subtotal'],
                    'discount' => $this->totals['descuento'],
                    'taxable_amount' => $this->totals['total_bruto_factura'],
                    'tax_amount' => $this->totals['iva'],
                    'shipping_cost' => $this->shipping_cost,
                    'total' => $this->totals['total_factura'] + $this->shipping_cost,
                    'total_pts' => $this->totals['total_pts'],
                ];

                if ($this->shipping_type == 2) {
                    $orderData = array_merge($orderData, [
                        'shipping_country_id' => $this->shipping_selectedCountry,
                        'shipping_department_id' => $this->shipping_selectedDepartment,
                        'shipping_city_id' => $this->shipping_selectedCity,
                        'shipping_addCity' => $this->shipping_city,
                        'shipping_address' => $this->shipping_address,
                        'shipping_additional_address' => $this->shipping_additional_address,
                    ]);
                }

                if ($this->shippingDifferent == true) {
                    $orderData = array_merge($orderData, [
                        'shipping_name' => $this->shipping_name,
                        'document_type_id' => $this->shipping_document_type,
                        'shipping_document' => $this->shipping_document,
                        'shipping_phone' => $this->shipping_phone,
                    ]);
                }

                $order = Order::create($orderData);

                $orderBillingData = [
                    'order_id' => $order->id,
                    'name' => $this->name,
                    'document_type_id' => $this->document_type,
                    'document' => $this->document,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'country_id' => $this->selectedCountry,
                    'department_id' => $this->selectedDepartment,
                    'city_id' => $this->selectedCity,
                    'addCity' => $this->city,
                ];

                OrderBillingData::create($orderBillingData);

                foreach ($this->productItems as $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $descuento = ($subtotal * $item['discount_percent']) / 100;
                    $total_bruto_factura = $subtotal - $descuento;
                    $iva = ($total_bruto_factura * $item['tax_percent']) / 100;
                    $total_pts = $item['pts'] * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'name' => $item['name'],
                        'unit_price' => $item['price'],
                        'pts' => $item['pts'],
                        'quantity' => $item['quantity'],
                        'discount' => $descuento,
                        'tax_percent' => $item['tax_percent'],
                        'tax_amount' => $iva,
                        'unit_sales_price' => $total_bruto_factura,
                        'total_pts' => $total_pts,
                    ]);
                }

                session()->forget('cart');

                return redirect()->route('bold.checkout', $order);
            });
        } catch (\Exception $e) {
            Log::error('Error al crear la orden: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al procesar tu orden: '.$e->getMessage());
        }
    }

    #[Layout('layouts.app.header')]
    public function render()
    {
        return view('livewire.order.order-create');
    }
}

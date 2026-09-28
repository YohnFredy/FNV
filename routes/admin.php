<?php

use App\Http\Controllers\Admin\BrandController;
use App\Livewire\Admin\Categories\CategoryForm;
use App\Livewire\Admin\Categories\CategoryIndex;
use App\Livewire\Admin\Mlm\MlmSettings;
use App\Livewire\Admin\Mlm\MlmSettlements;
use App\Livewire\Admin\Orders\OrderIndex;
use App\Livewire\Admin\Orders\OrderShow;
use App\Livewire\Admin\Products\ProductForm;
use App\Livewire\Admin\Products\ProductIndex;
use App\Livewire\Admin\Roles\RoleManager;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Plataforma Administrativa (Panel de Control Central)
|--------------------------------------------------------------------------
|
| Rutas exclusivas para el equipo administrativo del sistema.
| Protegidas por autenticación y el middleware 'admin'.
|
*/

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // Portada del Panel Administrativo
    Route::get('/', ProductIndex::class)->name('index');

    // Módulo de Gestión de Pedidos y Puntos MLM
    Route::get('orders', OrderIndex::class)->name('orders.index');
    Route::get('orders/{order}', OrderShow::class)->name('orders.show');

    // Módulo de Catálogo: Marcas
    Route::resource('brands', BrandController::class);

    // Módulo de Catálogo: Categorías
    Route::get('categories', CategoryIndex::class)->name('categories.index');
    Route::get('categories/create', CategoryForm::class)->name('categories.create');
    Route::get('categories/{category}/edit', CategoryForm::class)->name('categories.edit');

    // Módulo de Catálogo: Productos
    Route::get('products', ProductIndex::class)->name('products.index');
    Route::get('products/create', ProductForm::class)->name('products.create');
    Route::get('products/{product}/edit', ProductForm::class)->name('products.edit');

    // Módulo de Seguridad: Roles y Permisos (RBAC Spatie)
    Route::get('/roles', RoleManager::class)->name('roles');

    // Módulo de Compensación, Calificación y Liquidación MLM
    Route::get('mlm/settings', MlmSettings::class)->name('mlm.settings');
    Route::get('mlm/settlements', MlmSettlements::class)->name('mlm.settlements');
});

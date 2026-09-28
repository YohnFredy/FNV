<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookBoldController;
use App\Livewire\AlliedCompanies;
use App\Livewire\CompanyShow;
use App\Livewire\Order\OrderCreate;
use App\Livewire\Product\Cart;
use App\Livewire\Product\ProductListing;
use App\Livewire\Product\ProductShow;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Plataforma Web Pública (Página Inicial / Landing)
|--------------------------------------------------------------------------
|
| Rutas públicas accesibles para todos los visitantes.
| Se muestra con el layout superior (header.blade.php).
|
*/

Route::view('/', 'welcome')->name('home');

Route::view('/terminos-y-condiciones', 'pages.legal.terms')->name('legal.terms');
Route::view('/contrato-afiliacion', 'pages.legal.contract')->name('legal.contract');

Route::get('/register/{sponsor}/{leg?}', function (string $sponsor, ?string $leg = null) {
    return view('pages::auth.register', [
        'routeSponsor' => $sponsor,
        'routeLeg' => $leg,
    ]);
})->where(['leg' => 'left|right|L|R|l|r'])->middleware(['web'])->name('register.sponsor');

// Catálogo de Productos y Detalle Público
Route::get('/productos', ProductListing::class)->name('products.index');
Route::get('/producto/{product}', ProductShow::class)->name('products.show');

Route::get('empresas-aliadas', AlliedCompanies::class)->name('companies.index');
Route::get('empresa-aliada/{businessData}', CompanyShow::class)->name('companies.show');

// Rutas de Comercio y Pedidos Protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', Cart::class)->name('products.cart');
    Route::get('/order/create', OrderCreate::class)->name('orders.create');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/show', [OrderController::class, 'show'])->name('orders.show');

    // Pasarela de Pagos Bold
    Route::get('/checkout/bold/{order}', [OrderController::class, 'boldCheckout'])->name('bold.checkout');
    Route::get('/pagos/respuesta/bold', [PaymentController::class, 'boldResponse'])->name('bold.response');
});

// Webhook Pasarela Bold (Exento de CSRF en bootstrap/app.php)
Route::post('/webhook/bold', [WebhookBoldController::class, 'handleBoldWebhook'])->name('webhook.bold');

// Cambio de idioma de la plataforma (Internacionalización)
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en'], true)) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('locale.switch');

require __DIR__.'/settings.php';

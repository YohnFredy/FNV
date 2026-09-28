<?php

use App\Livewire\Office\Dashboard;
use App\Livewire\Office\Network\BinaryTree;
use App\Livewire\Office\Network\UnilevelTree;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Plataforma de Oficina Virtual (Distribuidor / Afiliado)
|--------------------------------------------------------------------------
|
| Rutas protegidas para la gestión de la red y oficina del distribuidor.
| Utilizan el layout con menú lateral (sidebar.blade.php).
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard principal de la Oficina (accesible tanto vía /office como /dashboard)
    Route::get('/office', Dashboard::class)->name('office.dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Visualizadores de Genealogía y Redes MLM (accesibles vía /office/* y /network/*)
    Route::get('/office/binary', BinaryTree::class)->name('office.network.binary');
    Route::get('/network/binary', BinaryTree::class)->name('network.binary');

    Route::get('/office/unilevel', UnilevelTree::class)->name('office.network.unilevel');
    Route::get('/network/unilevel', UnilevelTree::class)->name('network.unilevel');
});

<?php

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WompiResponseController;

// Ruta principal que carga la vista en el navegador
Route::get('/', function () {
    return view('index');
});

// Ruta a la que Wompi redirigirá al cliente
Route::get('/pago/resultado', [WompiResponseController::class, 'show'])
    ->name('wompi.resultado');

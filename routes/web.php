<?php

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

// Esta ruta carga la vista visual en el navegador
Route::get('/', function () {
    return view('index');
});

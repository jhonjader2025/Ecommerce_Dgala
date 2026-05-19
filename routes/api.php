<?php

use App\Http\Controllers\LonaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - D'gala Ecommerce
|--------------------------------------------------------------------------
| Aquí es donde registramos todas las rutas para los datos de nuestra app.
| Laravel les pone automáticamente el prefijo "/api", así que tus rutas
| reales quedarán como "/api/lonas".
*/

// 1. Ruta para listar todas las lonas (Método GET)
Route::get('/lonas', [LonaController::class, 'index']);

// 2. Ruta para registrar una nueva lona con su stock (Método POST)
Route::post('/lonas', [LonaController::class, 'store']);

// 3. Ruta para ver el detalle y auditoría de una lona específica (Método GET con ID)
Route::get('/lonas/{id}', [LonaController::class, 'show']);

// Ruta para activar/desactivar la lona (Borrado lógico)
Route::put('/lonas/{id}/toggle', [LonaController::class, 'toggleStatus']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\WompiWebhookController; // <-- ¡No olvide este señor!

/*
|--------------------------------------------------------------------------
| API Routes - D'gala Ecommerce
|--------------------------------------------------------------------------
| Aquí es donde registramos todas las rutas para los datos de nuestra app.
| Laravel les pone automáticamente el prefijo "/api", así que tus rutas
| reales quedarán como "/api/lonas".
*/

// ==========================================
// 🛡️ MÓDULO DE AUTENTICACIÓN Y WEBHOOKS (PÚBLICOS)
// ==========================================

// Ruta receptora de pagos de Wompi (Libre de candados para que la pasarela pueda avisarnos)
Route::post('/webhooks/wompi', [WompiWebhookController::class, 'handle']);


// ==========================================
// 🧵 MÓDULO DE LONAS / TEXTILES
// ==========================================

// Lista todas las lonas (GET)
Route::get('/lonas', [LonaController::class, 'index']);

// Registra una nueva lona con su stock (POST)
Route::post('/lonas', [LonaController::class, 'store']);

// Ve el detalle y auditoría de una lona específica (GET con ID)
Route::get('/lonas/{id}', [LonaController::class, 'show']);

// Activar/desactivar la lona (Borrado lógico)
Route::put('/lonas/{id}/toggle', [LonaController::class, 'toggleStatus']);


// ==========================================
// 📦 MÓDULO DE PEDIDOS / CARRITO
// ==========================================

// Crear un nuevo pedido generando referencia Wompi (POST)
Route::post('/pedidos', [PedidoController::class, 'store']);

// Listar todos los pedidos para el administrador (GET)
Route::get('/pedidos', [PedidoController::class, 'index']);

// Ver la radiografía completa de un pedido específico (GET con ID)
Route::get('/pedidos/{id}', [PedidoController::class, 'show']);


// ==========================================
// 👥 MÓDULO DE CONTROL DE USUARIOS
// ==========================================

// Listar todos los usuarios del sistema (GET)
Route::get('/usuarios', [UserController::class, 'index']);

// Crear o registrar un usuario internamente (POST)
Route::post('/usuarios', [UserController::class, 'store']);

// Activar/desactivar un usuario (Borrado lógico - ¡Aquí corregimos el typo de usuries!)
Route::put('/usuarios/{id}/toggle', [UserController::class, 'toggleStatus']);

// Ruta para activar la cuenta desde el enlace de correo
Route::get('/usuarios/activar', [UserController::class, 'activar']);
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Muestra el listado de usuarios activos en el sistema.
     * Ruta: GET /api/usuarios
     */
    public function index()
    {
        // Traemos solo los usuarios que estén activos (igualito a las lonas)
        $usuarios = User::where('activo', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => $usuarios
        ], 200);
    }
    /**
     * Crea un nuevo usuario en el sistema.
     * Ruta: POST /api/usuarios
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos que nos mandan desde Postman o el Frontend
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'rol' => 'nullable|in:cliente,admin,super_admin',
            'telefono' => 'nullable|string|max:20',
        ]);

        // 2. Creamos el usuario en la base de datos
        $usuario = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            // Encriptamos la clave para que no quede visible en la base de datos
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'] ?? 'cliente', // Si no mandan rol, es cliente por defecto
            'telefono' => $validated['telefono'] ?? null,
            'activo' => true, // Siempre nacen activos
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario registrado de forma exitosa en D\'gala.',
            'data' => $usuario
        ], 201);
    }

    /**
     * Activa o desactiva un usuario (Borrado lógico).
     * Ruta: PUT /api/usuarios/{id}/toggle
     */
    public function toggleStatus($id)
    {
        try {
            // 1. Buscamos el usuario por su ID, si no existe tira 404
            $usuario = User::findOrFail($id);

            // 2. Switcheamos el estado booleano (si está en true pasa a false, y viceversa)
            $usuario->activo = !$usuario->activo;
            $usuario->save();

            $estadoTexto = $usuario->activo ? 'activado' : 'desactivado';

            return response()->json([
                'status' => 'success',
                'message' => "El usuario {$usuario->name} ha sido {$estadoTexto} correctamente.",
                'data' => [
                    'id' => $usuario->id,
                    'name' => $usuario->name,
                    'activo' => $usuario->activo
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error en toggleStatus de usuario: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo cambiar el estado del usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

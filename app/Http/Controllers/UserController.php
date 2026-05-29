<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Mail\ActivarCuentaMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Muestra el listado de usuarios activos en el sistema.
     * Ruta: GET /api/usuarios
     */
    public function index()
    {
        $usuarios = User::where('activo', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => $usuarios
        ], 200);
    }

    /**
     * Crea un nuevo usuario inactivo y le dispara el correo de activación.
     * Ruta: POST /api/usuarios
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos que nos mandan
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'rol'      => 'nullable|in:cliente,admin,super_admin',
            'telefono' => 'nullable|string|max:20',
        ]);

        try {
            // 2. Creamos el usuario en la base de datos (Nace INACTIVO hasta que verifique correo)
            $usuario = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']), // Encriptamos la clave
                'rol'      => $validated['rol'] ?? 'cliente',
                'telefono' => $validated['telefono'] ?? null,
                'activo'   => false, // 👈 IMPORTANTE: Nace en falso para obligar la activación
            ]);

            // 🚀 3. Disparamos el correo de auditoría mediante el driver (log o mailgun)
            Mail::to($usuario->email)->send(new ActivarCuentaMail($usuario));

            // 4. Retornamos la respuesta al final, después de hacer todo el proceso
            return response()->json([
                'status'  => 'success',
                'message' => 'Usuario registrado de forma exitosa. Se ha enviado un correo de activación.',
                'data'    => $usuario
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error registrando usuario en D'gala: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo registrar el usuario correctamente.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesa el clic del enlace del correo para activar la cuenta.
     * Ruta: GET /api/usuarios/activar
     */
    public function activar(Request $request)
    {
        $email = $request->query('email');
        $tokenRecibido = $request->query('token');

        // Buscamos el usuario por el correo electrónico
        $usuario = User::where('email', $email)->first();

        if (!$usuario) {
            return response()->json(['status' => 'error', 'message' => 'El usuario no existe.'], 404);
        }

        // Recreamos el mismo token para comprobar que no haya sido alterado
        $tokenReal = sha1($usuario->email . $usuario->created_at);

        if ($tokenRecibido !== $tokenReal) {
            return response()->json(['status' => 'error', 'message' => 'El token de activación es inválido.'], 400);
        }

        if ($usuario->activo) {
            return response()->json(['status' => 'success', 'message' => 'Esta cuenta ya se encontraba activa.'], 200);
        }

        // Activamos al usuario definitivamente
        $usuario->activo = true;
        $usuario->save();

        return response()->json([
            'status'  => 'success',
            'message' => '¡Felicidades! Su cuenta de D\'gala ha sido activada con éxito. Ya puede iniciar sesión.'
        ], 200);
    }

    /**
     * Activa o desactiva un usuario administrativamente (Borrado lógico).
     * Ruta: PUT /api/usuarios/{id}/toggle
     */
    public function toggleStatus($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->activo = !$usuario->activo;
            $usuario->save();

            $estadoTexto = $usuario->activo ? 'activado' : 'desactivado';

            return response()->json([
                'status'  => 'success',
                'message' => "El usuario {$usuario->name} ha sido {$estadoTexto} correctamente.",
                'data'    => [
                    'id'     => $usuario->id,
                    'name'   => $usuario->name,
                    'activo' => $usuario->activo
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error en toggleStatus de usuario: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo cambiar el estado del usuario.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}

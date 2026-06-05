<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\CodigoActivacionMail; // El correo que creamos antes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validar los datos que llegan del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // 2. Generar el código de 6 dígitos para la activación
        $codigoActivacion = rand(100000, 999999);

        // 3. Crear el usuario (manual, por ende is_active inicia en false)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activation_code' => $codigoActivacion,
            'is_active' => false,
        ]);

        // 4. ¡La magia! Laravel lee el .env y usa Mailgun automáticamente
        Mail::to($user->email)->send(new CodigoActivacionMail($codigoActivacion));

        return response()->json([
            'status' => 'success',
            'message' => '¡Usuario registrado! Por favor revisa tu correo para activar la cuenta.'
        ], 201);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // GET /perfil  -> muestra el formulario
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // PUT /perfil  -> procesa la actualización
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validación básica (email único ignorando el propio usuario)
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            // Cambio de contraseña opcional: si pones new_password, exige current_password correcto
            'current_password'      => ['nullable', 'string'],
            'new_password'          => ['nullable', 'string', 'min:8', 'confirmed'], // requiere new_password_confirmation
        ]);

        // Actualiza nombre y email
        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        // Si se quiere cambiar la contraseña
        if (!empty($validated['new_password'])) {
            // Verificamos la actual
            if (!Hash::check($validated['current_password'] ?? '', $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.'])->withInput();
            }
            // Con Laravel 10 el cast "hashed" en User hace el hash automáticamente,
            // pero usar Hash::make aquí lo deja explícito y seguro.
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('status', 'Perfil actualizado correctamente.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

use App\Models\Writeup;
use App\Models\WriteupTemporal;
use App\Models\EnvioMaquina;
use App\Models\Maquina;

class ProfileController extends Controller
{
    // GET /perfil  -> muestra el formulario + resumen/estadísticas
    public function edit()
    {
        $user = Auth::user();

        // Writeups pendientes enviados por el usuario
        $writeupsPendUser = WriteupTemporal::query()
            ->where('autor', $user->name)
            ->orWhere('autor_email', $user->email)
            ->count();

        // Writeups aprobados del usuario
        $writeupsAprobUser = Writeup::query()
            ->where('autor', $user->name)
            ->orWhere('autor_email', $user->email)
            ->count();

        // Máquinas enviadas por el usuario (en tabla de envíos)
        $maquinasEnvUser = EnvioMaquina::query()
            ->where('autor', $user->name)
            ->orWhere('autor_email', $user->email)
            ->count();

        // Máquinas aprobadas DEL USUARIO (ya en tabla maquinas)
        // Se prioriza user_id si existe; si no, se hace match por nombre/email del autor.
        $maquinasAprobUser = Maquina::query()
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('autor', $user->name)
                  ->orWhere('autor_email', $user->email);
            })
            ->count();

        // Globales útiles (moderación/resumen)
        $writeupsAprobGlobal = Writeup::query()->count();
        $writeupsPendGlobal  = WriteupTemporal::query()->count();
        // (Ya no usamos global de máquinas aprobadas para el widget principal)

        $stats = [
            'writeups_pendientes_user'      => $writeupsPendUser,
            'writeups_aprobados_user'       => $writeupsAprobUser,
            'maquinas_enviadas_user'        => $maquinasEnvUser,

            // Ahora sí: aprobadas del usuario, no globales
            'maquinas_aprobadas_user'       => $maquinasAprobUser,
            'maquinas_aprobadas_user_known' => true, // la vista ya mostrará "Tus máquinas aprobadas"

            'writeups_aprobados_global'     => $writeupsAprobGlobal,
            'writeups_pendientes_global'    => $writeupsPendGlobal,
        ];

        return view('profile.edit', compact('user', 'stats'));
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
            // Hash explícito
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('status', 'Perfil actualizado correctamente.');
    }
}

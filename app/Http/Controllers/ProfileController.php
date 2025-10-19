<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

// Importa los modelos que ya usas en el proyecto
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

        // ---- Estadísticas sincronizadas con BD ----
        // Nota: Usamos coincidencia por nombre/email (columnas presentes en tu esquema)
        // para atribuir envíos al usuario actual sin requerir user_id.
        $writeupsPendUser = WriteupTemporal::query()
            ->where('autor', $user->name)
            ->orWhere('autor_email', $user->email)
            ->count();

        $writeupsAprobUser = Writeup::query()
            ->where('autor', $user->name)
            ->orWhere('autor_email', $user->email)
            ->count();

        $maquinasEnvUser = EnvioMaquina::query()
            ->where('autor', $user->name)
            ->orWhere('autor_email', $user->email)
            ->count();

        // Si no hay trazabilidad directa de "autor" en Maquina, mostramos globales.
        $maquinasAprobGlobal = Maquina::query()->count();

        // Globales útiles para moderación/resumen
        $writeupsAprobGlobal = Writeup::query()->count();
        $writeupsPendGlobal  = WriteupTemporal::query()->count();

        $stats = [
            'writeups_pendientes_user'      => $writeupsPendUser,
            'writeups_aprobados_user'       => $writeupsAprobUser,
            'maquinas_enviadas_user'        => $maquinasEnvUser,
            'maquinas_aprobadas_global'     => $maquinasAprobGlobal,
            'writeups_aprobados_global'     => $writeupsAprobGlobal,
            'writeups_pendientes_global'    => $writeupsPendGlobal,
            // bandera para la vista (indicamos que no hay conteo “tuyo” de máquinas aprobadas)
            'maquinas_aprobadas_user_known' => false,
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
            // Con Laravel 10 el cast "hashed" en User hace el hash automáticamente,
            // pero usar Hash::make aquí lo deja explícito y seguro.
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('status', 'Perfil actualizado correctamente.');
    }
}

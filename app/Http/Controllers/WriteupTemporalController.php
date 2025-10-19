<?php

namespace App\Http\Controllers;

use App\Models\WriteupTemporal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WriteupTemporalController extends Controller
{
    /**
     * Almacena un nuevo writeup temporal enviado por un usuario autenticado.
     */
    public function store(Request $request)
    {
        // Verificar que el usuario está autenticado
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para enviar un writeup.');
        }

        // Validación de los campos del formulario
        $validated = $request->validate([
            'maquina_id' => ['required', 'exists:maquinas,id'],
            'enlace'     => ['required', 'url', 'max:2048'],
        ], [
            'enlace.url' => 'El enlace debe ser una URL válida (incluye http/https).'
        ]);

        // Asegurar que el enlace tiene http o https
        if (! Str::startsWith($validated['enlace'], ['http://', 'https://'])) {
            $validated['enlace'] = 'https://' . ltrim($validated['enlace'], '/');
        }

        // Asignar autor automáticamente desde la sesión
        $validated['autor'] = $user->name;
        $validated['autor_email'] = $user->email;

        // Si la tabla writeup_temporals tiene columna user_id, se guarda
        if (\Schema::hasColumn('writeup_temporals', 'user_id')) {
            $validated['user_id'] = $user->id;
        }

        // Crear el registro
        WriteupTemporal::create($validated);

        return back()->with('success', '¡Writeup enviado correctamente! Queda pendiente de revisión por un moderador.');
    }
}

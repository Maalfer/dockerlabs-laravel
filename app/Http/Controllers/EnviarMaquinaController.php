<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EnvioMaquina;

class EnviarMaquinaController extends Controller
{
    public function create()
    {
        return view('enviar-maquina');
    }

    public function store(Request $request)
    {
        // Si el usuario está autenticado, usar su nombre como autor por defecto
        if (Auth::check()) {
            $request->merge(['autor_nombre' => Auth::user()->name]);
        }

        // Validación de los campos del formulario, incluyendo el destino
        $data = $request->validate([
            'nombre_maquina'   => ['required', 'string', 'max:150'],
            'dificultad'       => ['required', 'in:facil,medio,dificil'],
            'autor_nombre'     => ['required', 'string', 'max:120'],
            'autor_enlace'     => ['nullable', 'url', 'max:255'],
            'fecha_creacion'   => ['nullable', 'date'],
            'writeup'          => ['nullable', 'url', 'max:255'],
            'enlace_descarga'  => ['nullable', 'url', 'max:255'],
            'imagen'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'destino'          => ['required', 'in:dockerlabs,bunkerlabs'],
        ], [
            'autor_enlace.url'    => 'El enlace del autor debe ser una URL válida.',
            'writeup.url'         => 'El writeup debe ser una URL válida.',
            'enlace_descarga.url' => 'El enlace de descarga debe ser una URL válida.',
            'destino.required'    => 'Debes seleccionar un destino de publicación.',
            'destino.in'          => 'El destino elegido no es válido.',
        ]);

        // Subida opcional de imagen y guardado de la ruta
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('maquinas_envios', 'public');
        }
        $data['imagen_path'] = $imagenPath;

        // Defensa por si la vista no envía el campo (mantener funcionamiento estable)
        $data['destino'] = $data['destino'] ?? 'dockerlabs';

        // Crear el registro del envío
        EnvioMaquina::create($data);

        // Redirección a la misma pantalla con mensaje de éxito
        return redirect()
            ->route('dockerlabs.enviar-maquina.form')
            ->with('success', '¡Gracias! Hemos recibido tu envío.');
    }
}

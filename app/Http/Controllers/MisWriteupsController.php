<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Writeup;
use App\Models\WriteupTemporal;

class MisWriteupsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Aprobados del usuario (por 'autor' = users.name)
        $aprobados = Writeup::with('maquina')
            ->where('autor', $user->name)
            ->latest()
            ->get();

        // Temporales del usuario (pendientes y/o no)
        $enviados = WriteupTemporal::with(['maquina', 'writeup'])
            ->where('autor', $user->name)
            ->latest()
            ->get();

        // Mapa rápido de "edición pendiente" por writeup_id para pintar estado en la vista
        $pendientesEdicion = $enviados
            ->where('tipo', 'edicion')
            ->where('estado', 'pendiente')
            ->keyBy('writeup_id');

        return view('mis-writeups', [
            'user'              => $user,
            'aprobados'         => $aprobados,
            'enviados'          => $enviados,
            'pendientesEdicion' => $pendientesEdicion,
        ]);
    }

    public function solicitarCambio(Request $request, Writeup $writeup)
    {
        $user = $request->user();

        // Seguridad: solo puede pedir cambios del writeup cuyo autor coincide con su nombre
        abort_unless($writeup->autor === $user->name, 403);

        // Validación base
        $data = $request->validate([
            'enlace'     => ['required', 'url', 'max:2048'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ], [
            'enlace.url' => 'El enlace debe ser una URL válida (incluye http/https).'
        ]);

        // Normaliza (trim + asegura esquema http/https)
        $nuevoEnlace = trim($data['enlace']);
        if (! Str::startsWith($nuevoEnlace, ['http://', 'https://'])) {
            $nuevoEnlace = 'https://' . ltrim($nuevoEnlace, '/');
        }

        // Evita solicitudes sin cambios (mismo enlace que el actual)
        if (rtrim($writeup->enlace, '/') === rtrim($nuevoEnlace, '/')) {
            return back()->with('success', 'El enlace es idéntico al publicado; no hay cambios que solicitar.');
        }

        // Mejora opcional aplicada:
        // Si ya hay una edición pendiente para este writeup, la actualizamos (evita duplicados)
        $pendiente = WriteupTemporal::where('writeup_id', $writeup->id)
            ->where('tipo', 'edicion')
            ->where('estado', 'pendiente')
            ->first();

        if ($pendiente) {
            $pendiente->update([
                'enlace'     => $nuevoEnlace,
                'comentario' => $data['comentario'] ?? null,
                'tipo'       => 'edicion', // refuerza tipo
            ]);
        } else {
            // Creamos una nueva solicitud de edición
            WriteupTemporal::create([
                'maquina_id' => $writeup->maquina_id,
                'autor'      => $user->name,
                'enlace'     => $nuevoEnlace,
                'estado'     => 'pendiente',
                'tipo'       => 'edicion',
                'writeup_id' => $writeup->id,
                'comentario' => $data['comentario'] ?? null,
            ]);
        }

        return back()->with('success', 'Tu solicitud de cambio se ha enviado para revisión.');
    }
}

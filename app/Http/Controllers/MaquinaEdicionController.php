<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Maquina;
use App\Models\MaquinaEdicion;

class MaquinaEdicionController extends Controller
{
    public function store($id, Request $request)
    {
        $user = $request->user();
        $maquina = Maquina::findOrFail($id);

        // Autorizar: que sea su máquina (por user_id o por autor/autor_email)
        $esPropia = ($maquina->user_id && $maquina->user_id === $user->id)
            || ($maquina->autor && $maquina->autor === $user->name)
            || ($maquina->autor_email && $maquina->autor_email === $user->email);

        abort_unless($esPropia, 403);

        $data = $request->validate([
            'nombre'          => ['required','string','max:150'],
            'descripcion'     => ['required','string','max:5000'],
            'dificultad'      => ['required','string','in:muy-facil,facil,medio,dificil'],
            'enlace_descarga' => ['nullable','url','max:2048'],
            'comentario'      => ['nullable','string','max:500'],
        ], [
            'enlace_descarga.url' => 'El enlace de descarga debe ser una URL válida.',
        ]);

        // Evitar solicitudes sin cambios
        $sinCambios = (
            trim((string)$maquina->nombre)           === trim((string)$data['nombre']) &&
            trim((string)$maquina->descripcion)      === trim((string)$data['descripcion']) &&
            trim((string)$maquina->dificultad)       === trim((string)$data['dificultad']) &&
            trim((string)($maquina->enlace_descarga ?? '')) === trim((string)($data['enlace_descarga'] ?? ''))
        );

        if ($sinCambios) {
            return back()->with('success', 'No hay cambios respecto a la máquina publicada.');
        }

        // Si ya hay una edición pendiente para esta máquina y este usuario, actualízala
        $pendiente = MaquinaEdicion::where('maquina_id', $maquina->id)
            ->where('estado', 'pendiente')
            ->where('user_id', $user->id)
            ->first();

        $payload = [
            'nombre'          => $data['nombre'],
            'descripcion'     => $data['descripcion'],
            'dificultad'      => $data['dificultad'],
            'enlace_descarga' => $data['enlace_descarga'] ?? null,
        ];

        if ($pendiente) {
            $pendiente->update([
                'cambios'    => $payload,
                'comentario' => $data['comentario'] ?? null,
            ]);
        } else {
            MaquinaEdicion::create([
                'maquina_id' => $maquina->id,
                'user_id'    => $user->id,
                'estado'     => 'pendiente',
                'cambios'    => $payload,
                'comentario' => $data['comentario'] ?? null,
            ]);
        }

        return back()->with('success', 'Tu solicitud de edición se ha enviado para revisión.');
    }
}

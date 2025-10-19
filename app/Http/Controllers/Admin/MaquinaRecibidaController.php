<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnvioMaquina;
use App\Models\Maquina;

class MaquinaRecibidaController extends Controller
{
    public function index()
    {
        $maquinas = EnvioMaquina::latest()->paginate(10);
        return view('admin.maquinas-recibidas', compact('maquinas'));
    }

    public function approve($id)
    {
        $envio = EnvioMaquina::findOrFail($id);

        Maquina::create([
            'nombre'          => $envio->nombre_maquina,
            'descripcion'     => $envio->writeup ?: 'Sin descripción.',
            'dificultad'      => $envio->dificultad ?: 'medio',
            'enlace_descarga' => $envio->autor_enlace ?: null,

            // Trazabilidad del autor de la máquina aprobada
            'user_id'         => $envio->user_id ?? null,
            'autor'           => $envio->autor ?? null,
            'autor_email'     => $envio->autor_email ?? null,
        ]);

        $envio->delete();

        return back()->with('success', 'Máquina aprobada y publicada.');
    }
}

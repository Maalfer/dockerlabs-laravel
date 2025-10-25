<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\EnvioMaquina;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    public function index()
    {
        $maquinas = Maquina::latest()->get();
        return view('admin', compact('maquinas'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nombre'           => 'required|string|max:255',
        'descripcion'      => 'required|string|max:500',
        'dificultad'       => 'required|in:facil,medio,dificil,muy-facil',
        'enlace_descarga'  => 'nullable|url',
        'envio_id'         => 'nullable|integer|exists:envios_maquinas,id',
        'autor'            => 'required|string|max:255',
        'autor_email'      => 'nullable|string|max:255',
        'autor_url'        => 'nullable|string|max:255',
        'destino'          => 'required|in:principal,bunker',
    ]);

    $common = [
        'nombre'          => $validated['nombre'],
        'descripcion'     => $validated['descripcion'],
        'dificultad'      => $validated['dificultad'],
        'enlace_descarga' => $validated['enlace_descarga'] ?? null,
        'autor'           => $validated['autor'],
        'autor_email'     => $validated['autor_email'] ?? null,
        // Si quieres guardar autor_url, a�ade la columna en ambas tablas y aqu� el fillable
        // 'autor_url'    => $validated['autor_url'] ?? null,
    ];

    if (auth()->check()) {
        $common['user_id'] = auth()->id();
    }

    if ($validated['destino'] === 'bunker') {
        // Guardar en maquinas_bunkerlabs
        \App\Models\MaquinaBunker::create($common);
    } else {
        // Guardar en maquinas (comportamiento actual)
        \App\Models\Maquina::create($common);
    }

    // Si vino desde un EnvioMaquina, lo eliminamos
    if (!empty($validated['envio_id'])) {
        \App\Models\EnvioMaquina::whereKey($validated['envio_id'])->delete();
    }

    return redirect()
        ->route('dockerlabs.admin.dashboard')
        ->with('success', 'Máquina agregada exitosamente');
}

}

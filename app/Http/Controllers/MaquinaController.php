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
        ]);

        $maquina = new Maquina();
        $maquina->nombre          = $validated['nombre'];
        $maquina->descripcion     = $validated['descripcion'];
        $maquina->dificultad      = $validated['dificultad'];
        $maquina->enlace_descarga = $validated['enlace_descarga'] ?? null;

        if (auth()->check()) {
            $maquina->user_id = auth()->id();
        }

        $maquina->autor = $validated['autor'];
        $maquina->autor_email = $validated['autor_email'] ?? ($validated['autor_url'] ?? null);

        $maquina->save();

        if (!empty($validated['envio_id'])) {
            EnvioMaquina::whereKey($validated['envio_id'])->delete();
        }

        return redirect()->route('dockerlabs.admin.dashboard')->with('success', 'Máquina agregada exitosamente');
    }

    public function destroy(Maquina $maquina)
    {
        $maquina->delete();
        return redirect()->route('dockerlabs.admin.dashboard')->with('success', 'Máquina eliminada correctamente');
    }
}

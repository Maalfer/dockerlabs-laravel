<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    // Mostrar la página de administración con el listado de máquinas
    public function index()
    {
        // Ordena por más reciente primero (puedes cambiar a ->paginate(10) si quieres)
        $maquinas = Maquina::latest()->get();

        return view('admin', compact('maquinas'));
    }

    // Almacenar una nueva máquina en la base de datos
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'required|string|max:500',
            'dificultad'    => 'required|in:facil,medio,dificil,muy-facil',
            'enlace_descarga' => 'nullable|url', // Validación para el enlace de descarga (opcional y debe ser una URL)
        ]);

        // Crear una nueva máquina
        $maquina = new Maquina();
        $maquina->nombre         = $validated['nombre'];
        $maquina->descripcion    = $validated['descripcion'];
        $maquina->dificultad     = $validated['dificultad'];
        $maquina->enlace_descarga = $validated['enlace_descarga'] ?? null; // Asignar el enlace de descarga si existe
        $maquina->save();

        // Redirigir a la página de administración con un mensaje de éxito
        return redirect()->route('admin')->with('success', 'Máquina agregada exitosamente');
    }

    // Eliminar una máquina
    public function destroy(Maquina $maquina)
    {
        $maquina->delete();

        return redirect()
            ->route('admin')
            ->with('success', 'Máquina eliminada correctamente');
    }
}

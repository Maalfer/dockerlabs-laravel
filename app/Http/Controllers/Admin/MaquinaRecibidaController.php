<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnvioMaquina;

class MaquinaRecibidaController extends Controller
{
    public function index()
    {
        $maquinas = EnvioMaquina::latest()->paginate(10);
        return view('admin.maquinas-recibidas', compact('maquinas'));
    }

    /**
     * Genera datos de pre-relleno para el formulario de /admin a partir del envío
     * y redirige allí. NO crea la máquina aún.
     */
    public function prefill(int $id)
    {
        $envio = EnvioMaquina::findOrFail($id);

        // Montamos una descripción sugerida con la info disponible en el envío
        $lineas = [];
        $lineas[] = "Autor: {$envio->autor_nombre}";
        if ($envio->autor_enlace)   $lineas[] = "Autor URL: {$envio->autor_enlace}";
        if ($envio->fecha_creacion) $lineas[] = "Creación: {$envio->fecha_creacion}";
        if ($envio->writeup)        $lineas[] = "Writeup: {$envio->writeup}";
        $descripcionSugerida = implode("\n", $lineas);

        // En envíos públicos no tenemos enlace de descarga real; dejamos vacío.
        // (Si en tu flujo el "autor_enlace" es realmente el enlace de descarga, cámbialo aquí)
        $prefill = [
            'envio_id'        => $envio->id,
            'nombre'          => $envio->nombre_maquina,
            'descripcion'     => $descripcionSugerida,
            'dificultad'      => $envio->dificultad ?: 'medio',
            'enlace_descarga' => null,

            // Trazabilidad del autor al publicar
            'autor'        => $envio->autor_nombre,
            'autor_email'  => null, // el modelo de envío no lo recoge hoy
        ];

        // Flash a sesión para que el formulario de /admin se auto-llene
        session()->flash('prefill_maquina', $prefill);

        return redirect()->route('admin')
            ->with('success', 'Datos pre-cargados desde el envío #' . $envio->id . '. Revisa y publica.');
    }
}

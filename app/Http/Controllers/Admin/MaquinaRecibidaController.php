<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnvioMaquina;
use Carbon\Carbon;

class MaquinaRecibidaController extends Controller
{
    public function index()
    {
        $maquinas = EnvioMaquina::latest()->paginate(10);
        return view('admin.maquinas-recibidas', compact('maquinas'));
    }

    public function prefill(int $id)
    {
        $envio = EnvioMaquina::findOrFail($id);

        $lineas = [];
        if ($envio->autor_nombre)   $lineas[] = "Autor: {$envio->autor_nombre}";
        if ($envio->autor_enlace)   $lineas[] = "Autor URL: {$envio->autor_enlace}";
        if ($envio->fecha_creacion) $lineas[] = "Creación: {$envio->fecha_creacion}";
        if ($envio->writeup)        $lineas[] = "Writeup: {$envio->writeup}";
        $descripcionSugerida = implode("\n", $lineas);

        $fechaIso = null;
        if (!empty($envio->fecha_creacion)) {
            try {
                $fechaIso = Carbon::parse($envio->fecha_creacion)->format('Y-m-d');
            } catch (\Throwable $e) {
                $fechaIso = null;
            }
        }

        $prefill = [
            'envio_id'        => $envio->id,
            'nombre'          => $envio->nombre_maquina,
            'descripcion'     => $descripcionSugerida,
            'dificultad'      => $envio->dificultad ?: 'medio',
            'enlace_descarga' => null,
            'autor'           => $envio->autor_nombre,
            'autor_url'       => $envio->autor_enlace,
            'fecha_creacion'  => $fechaIso,
            'writeup'         => $envio->writeup,
            'autor_email'     => null,
        ];

        session()->flash('prefill_maquina', $prefill);

        return redirect()
            ->route('dockerlabs.admin.dashboard')
            ->with('success', 'Datos pre-cargados desde el envío #' . $envio->id . '.');
    }
}

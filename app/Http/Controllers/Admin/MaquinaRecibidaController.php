<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnvioMaquina;
use App\Models\Maquina;
use App\Models\MaquinaBunker;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class MaquinaRecibidaController extends Controller
{
    public function index()
    {
        $maquinas = EnvioMaquina::latest()->paginate(10);
        return view('admin.maquinas-recibidas', compact('maquinas'));
    }

    public function prefill(int $id): RedirectResponse
    {
        $envio = EnvioMaquina::findOrFail($id);

        // Construcción de descripción sugerida (se mantiene el comportamiento existente)
        $lineas = [];
        if ($envio->autor_nombre)   $lineas[] = "Autor: {$envio->autor_nombre}";
        if ($envio->autor_enlace)   $lineas[] = "Autor URL: {$envio->autor_enlace}";
        if ($envio->fecha_creacion) $lineas[] = "Creación: {$envio->fecha_creacion}";
        if ($envio->writeup)        $lineas[] = "Writeup: {$envio->writeup}";
        $descripcionSugerida = implode("\n", $lineas);

        // Parseo conservado (aunque no se use) para no alterar el funcionamiento previo
        $fechaIso = null;
        if (!empty($envio->fecha_creacion)) {
            try {
                $fechaIso = Carbon::parse($envio->fecha_creacion)->format('Y-m-d');
            } catch (\Throwable $e) {
                $fechaIso = null;
            }
        }

        DB::transaction(function () use ($envio, $descripcionSugerida, $fechaIso) {
            // Mantener user_id si viene del envío; fallback a usuario autenticado si existe
            $userId = $envio->user_id ?: (auth()->check() ? auth()->id() : null);

            $payload = [
                'user_id'         => $userId,
                'nombre'          => $envio->nombre_maquina,
                'descripcion'     => $descripcionSugerida,
                'dificultad'      => $envio->dificultad ?: 'medio',
                'enlace_descarga' => $envio->enlace_descarga,
                'autor'           => $envio->autor_nombre,
                'autor_email'     => $envio->autor_email,
                'imagen_path'     => $envio->imagen_path,
            ];

            // Crear en la tabla destino según el valor guardado en el envío
            $destino = $envio->destino ?? 'dockerlabs';
            if ($destino === 'bunkerlabs') {
                MaquinaBunker::create($payload);
            } else {
                Maquina::create($payload);
            }

            // Eliminar el envío procesado
            $envio->delete();
        });

        return redirect()
            ->route('dockerlabs.admin.maquinas.recibidas')
            ->with('success', 'Máquina aprobada y publicada con la autoría original.');
    }
}

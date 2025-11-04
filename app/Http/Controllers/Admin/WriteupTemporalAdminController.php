<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WriteupTemporal;
use App\Models\Writeup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WriteupTemporalAdminController extends Controller
{
    /**
     * Lista de solicitudes temporales (nuevas y ediciones)
     */
    public function index()
    {
        // Cargamos también 'writeup' para poder mostrar el origen en ediciones
        $items = WriteupTemporal::with(['maquina', 'writeup'])
            ->latest()
            ->paginate(20);

        return view('admin.writeups-temporal-index', compact('items'));
    }

    /**
     * Aprueba una solicitud temporal:
     * - Si es EDICIÓN (tipo='edicion' o tiene writeup_id), ACTUALIZA el writeup original.
     * - Si es ALTA nueva, crea el writeup (evitando duplicados exactos).
     * En ambos casos, elimina la temporal.
     */
    public function approve($id)
    {
        $mensaje = 'Solicitud aplicada correctamente.';

        DB::transaction(function () use ($id, &$mensaje) {
            // Bloqueamos la fila temporal para consistencia
            $temp = WriteupTemporal::with('writeup')->lockForUpdate()->findOrFail($id);

            // Detectar edición por cualquiera de las dos vías (robusto ante datos antiguos)
            $esEdicion = ($temp->tipo === 'edicion') || !is_null($temp->writeup_id);

            if ($esEdicion && $temp->writeup_id) {
                // ===== EDICIÓN: actualizar el writeup ya existente =====
                $w = Writeup::lockForUpdate()->findOrFail($temp->writeup_id);

                // Actualiza solo el enlace (comportamiento recomendado para conservar ID/timestamps)
                $w->update([
                    'enlace' => $temp->enlace,
                ]);

                // Elimina la solicitud temporal
                $temp->delete();

                $mensaje = 'Edición aplicada: enlace actualizado y solicitud eliminada.';
            } else {
                // ===== ALTA NUEVA: crear writeup aprobado (evitando duplicados exactos) =====
                $existe = Writeup::where('maquina_id', $temp->maquina_id)
                    ->where('autor', $temp->autor)
                    ->where('enlace', $temp->enlace)
                    ->exists();

                if (!$existe) {
                    Writeup::create([
                        'maquina_id' => $temp->maquina_id,
                        'autor'      => $temp->autor,
                        'enlace'     => $temp->enlace,
                    ]);
                    $mensaje = 'Writeup aprobado y publicado.';
                } else {
                    $mensaje = 'El writeup ya existía; se descartó el duplicado y se eliminó la solicitud.';
                }

                // Elimina la solicitud temporal
                $temp->delete();
            }
        });

        return back()->with('success', $mensaje);
    }

    /**
     * Elimina una solicitud temporal (rechazo)
     */
    public function destroy($id)
    {
        WriteupTemporal::findOrFail($id)->delete();
        return back()->with('success', 'Writeup temporal eliminado.');
    }
}

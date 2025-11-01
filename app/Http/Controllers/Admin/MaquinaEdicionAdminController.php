<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maquina;
use App\Models\MaquinaEdicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaquinaEdicionAdminController extends Controller
{
    public function index(Request $request)
    {
        // Solo ediciones pendientes
        $ediciones = MaquinaEdicion::with(['maquina','user'])
            ->where('estado','pendiente')
            ->latest()
            ->paginate(10);

        return view('admin.maquinas-editadas', compact('ediciones'));
    }

    public function approve(MaquinaEdicion $edicion)
    {
        DB::transaction(function () use ($edicion) {
            $edicion->refresh();

            if ($edicion->estado !== 'pendiente') {
                return; // idempotente
            }

            $m = Maquina::lockForUpdate()->findOrFail($edicion->maquina_id);

            $c = $edicion->cambios ?? [];
            // Aplica SOLO los campos esperados
            $update = [];
            foreach (['nombre','descripcion','dificultad','enlace_descarga'] as $k) {
                if (array_key_exists($k, $c)) {
                    $update[$k] = $c[$k];
                }
            }

            if (!empty($update)) {
                $m->update($update);
            }

            $edicion->update(['estado' => 'aprobada']);
            $edicion->delete(); // limpiar después de aplicar
        });

        return back()->with('success', 'Edición aplicada a la máquina.');
    }

    public function destroy(MaquinaEdicion $edicion)
    {
        $edicion->update(['estado' => 'rechazada']);
        $edicion->delete();

        return back()->with('success', 'Edición rechazada y eliminada.');
    }
}

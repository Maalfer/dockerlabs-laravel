<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WriteupTemporal;
use App\Models\Writeup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WriteupTemporalAdminController extends Controller
{
    public function index()
    {
        $items = WriteupTemporal::with('maquina')->latest()->paginate(20);
        return view('admin.writeups-temporal-index', compact('items'));
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            $temp = WriteupTemporal::lockForUpdate()->findOrFail($id);

            // Crea el writeup aprobado
            Writeup::create([
                'maquina_id' => $temp->maquina_id,
                'autor'      => $temp->autor,
                'enlace'     => $temp->enlace,
            ]);

            // Puedes eliminar el temporal o marcarlo como aprobado
            $temp->delete();
            // o: $temp->update(['estado' => 'aprobado']);
        });

        return back()->with('success', 'Writeup aprobado y publicado.');
    }

    public function destroy($id)
    {
        WriteupTemporal::findOrFail($id)->delete();
        return back()->with('success', 'Writeup temporal eliminado.');
    }
}

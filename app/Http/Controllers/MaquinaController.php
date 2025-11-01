<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\EnvioMaquina;
use App\Models\MaquinaBunker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'nombre'              => 'required|string|max:255',
            'descripcion'         => 'required|string|max:500',
            'dificultad'          => 'required|in:facil,medio,dificil,muy-facil',
            'enlace_descarga'     => 'nullable|url',
            'envio_id'            => 'nullable|integer|exists:envios_maquinas,id',
            'autor'               => 'required|string|max:255',
            'autor_email'         => 'nullable|string|max:255',
            'autor_url'           => 'nullable|string|max:255',
            'destino'             => 'required|in:principal,bunker',
            'imagen'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'imagen_path_prefill' => 'nullable|string',
        ]);

        $finalImagenPath = null;

        if ($request->hasFile('imagen')) {
            $finalImagenPath = $request->file('imagen')->store('maquinas', 'public');
        } elseif (!empty($validated['imagen_path_prefill'])) {
            $srcRel = $validated['imagen_path_prefill'];
            $srcAbs = storage_path('app/public/' . $srcRel);
            if (is_file($srcAbs)) {
                $ext   = pathinfo($srcAbs, PATHINFO_EXTENSION);
                $name  = uniqid('maq_') . '.' . $ext;
                $dstRel= 'maquinas/' . $name;
                $dstAbs= storage_path('app/public/' . $dstRel);
                if (!is_dir(dirname($dstAbs))) {
                    @mkdir(dirname($dstAbs), 0775, true);
                }
                copy($srcAbs, $dstAbs);
                $finalImagenPath = $dstRel;
            }
        }

        $common = [
            'nombre'          => $validated['nombre'],
            'descripcion'     => $validated['descripcion'],
            'dificultad'      => $validated['dificultad'],
            'enlace_descarga' => $validated['enlace_descarga'] ?? null,
            'autor'           => $validated['autor'],
            'autor_email'     => $validated['autor_email'] ?? null,
            'imagen_path'     => $finalImagenPath,
        ];

        if (auth()->check()) {
            $common['user_id'] = auth()->id();
        }

        if ($validated['destino'] === 'bunker') {
            MaquinaBunker::create($common);
        } else {
            Maquina::create($common);
        }

        if (!empty($validated['envio_id'])) {
            EnvioMaquina::whereKey($validated['envio_id'])->delete();
        }

        return redirect()
            ->route('dockerlabs.admin.dashboard')
            ->with('success', 'Máquina agregada exitosamente');
    }

    public function destroy(Maquina $maquina)
    {
        $path = $maquina->imagen_path;

        if ($path && str_starts_with($path, 'maquinas/')) {
            Storage::disk('public')->delete($path);
        }

        $maquina->delete();

        return redirect()
            ->route('dockerlabs.admin.dashboard')
            ->with('success', 'Máquina eliminada correctamente');
    }
}

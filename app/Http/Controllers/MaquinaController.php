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
            $file = $request->file('imagen');
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
            $name = uniqid('maq_') . '.' . $ext;
            $destDir = public_path('images');
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $name);
            $finalImagenPath = 'images/' . $name;
        } elseif (!empty($validated['imagen_path_prefill'])) {
            $srcRel = ltrim($validated['imagen_path_prefill'], '/');
            $srcRel = preg_replace('#^(storage/|app/public/)#', '', $srcRel);
            $srcAbs = storage_path('app/public/' . $srcRel);
            if (is_file($srcAbs)) {
                $ext = strtolower(pathinfo($srcAbs, PATHINFO_EXTENSION) ?: 'jpg');
                $name = uniqid('maq_') . '.' . $ext;
                $destDir = public_path('images');
                if (!is_dir($destDir)) {
                    @mkdir($destDir, 0755, true);
                }
                @copy($srcAbs, $destDir . DIRECTORY_SEPARATOR . $name);
                $finalImagenPath = 'images/' . $name;
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

        if ($path && str_starts_with($path, 'images/')) {
            $abs = public_path($path);
            if (is_file($abs)) {
                @unlink($abs);
            }
        }

        $maquina->delete();

        return redirect()
            ->route('dockerlabs.admin.dashboard')
            ->with('success', 'Máquina eliminada correctamente');
    }
}

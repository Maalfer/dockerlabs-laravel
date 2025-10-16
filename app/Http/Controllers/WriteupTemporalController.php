<?php

namespace App\Http\Controllers;

use App\Models\WriteupTemporal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class WriteupTemporalController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'maquina_id' => ['required', 'exists:maquinas,id'],
            'autor'      => ['required', 'string', 'max:120'],
            'enlace'     => ['required', 'url', 'max:2048'],
        ], [
            'enlace.url' => 'El enlace debe ser una URL válida (incluye http/https).'
        ]);

        if (! Str::startsWith($data['enlace'], ['http://', 'https://'])) {
            $data['enlace'] = 'https://' . ltrim($data['enlace'], '/');
        }

        WriteupTemporal::create($data);

        return back()->with('success', '¡Writeup enviado! Queda registrado para revisión.');
    }
}

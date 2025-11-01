<?php

namespace App\Http\Controllers;

use App\Models\WriteupTemporal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WriteupTemporalController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'maquina_id' => ['required', 'exists:maquinas,id'],
            'enlace'     => ['required', 'url', 'max:2048'],
            'website'    => ['nullable', 'max:0'],
        ];

        if (! $user) {
            $rules['autor'] = ['required', 'string', 'max:120'];
            $rules['autor_email'] = ['required', 'email', 'max:255'];
        }

        $validated = $request->validate($rules, [
            'enlace.url' => 'El enlace debe ser una URL válida (incluye http/https).',
            'website.max' => 'Bot detectado.',
        ]);

        if (! Str::startsWith($validated['enlace'], ['http://', 'https://'])) {
            $validated['enlace'] = 'https://' . ltrim($validated['enlace'], '/');
        }

        if ($user) {
            $validated['autor'] = $user->name;
            $validated['autor_email'] = $user->email;

            if (Schema::hasColumn('writeup_temporals', 'user_id')) {
                $validated['user_id'] = $user->id;
            }
        } else {
            if (Schema::hasColumn('writeup_temporals', 'user_id')) {
                $validated['user_id'] = null;
            }
        }

        WriteupTemporal::create($validated);

        return back()->with('success', '¡Writeup enviado correctamente! Te recomendamos registrarte para poder gestionarlo más adelante.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EnvioMaquina;

class EnviarMaquinaController extends Controller
{
    public function create()
    {
        return view('enviar-maquina');
    }

    public function store(Request $request)
    {
        if (Auth::check()) {
            $request->merge(['autor_nombre' => Auth::user()->name]);
        }

        $data = $request->validate([
            'nombre_maquina'   => ['required','string','max:150'],
            'dificultad'       => ['required','in:facil,medio,dificil'],
            'autor_nombre'     => ['required','string','max:120'],
            'autor_enlace'     => ['nullable','url','max:255'],
            'fecha_creacion'   => ['nullable','date'],
            'writeup'          => ['nullable','url','max:255'],
            'enlace_descarga'  => ['nullable','url','max:255'],
        ], [
            'autor_enlace.url'   => 'El enlace del autor debe ser una URL válida.',
            'writeup.url'        => 'El writeup debe ser una URL válida.',
            'enlace_descarga.url'=> 'El enlace de descarga debe ser una URL válida.',
        ]);
        
        EnvioMaquina::create($data);

        return redirect()
            ->route('enviar-maquina.form')
            ->with('success', '¡Gracias! Hemos recibido tu envío.');
    }
}

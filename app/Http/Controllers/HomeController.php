<?php

namespace App\Http\Controllers;

use App\Models\Maquina;

class HomeController extends Controller
{
    public function index()
    {
        // Antes: Maquina::all();
        // Ahora: carga writeups + el user del autor para evitar N+1
        $maquinas = Maquina::with(['writeups.user'])->get();

        return view('home', compact('maquinas'));
    }
}

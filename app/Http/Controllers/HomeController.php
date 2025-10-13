<?php

namespace App\Http\Controllers;

use App\Models\Maquina;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener todas las máquinas
        $maquinas = Maquina::all();

        // Pasar las máquinas a la vista
        return view('home', compact('maquinas'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnvioMaquina;

class MaquinaRecibidaController extends Controller
{
    public function index()
    {
        $maquinas = EnvioMaquina::latest()->paginate(10);
        return view('admin.maquinas-recibidas', compact('maquinas'));
    }
}

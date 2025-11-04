<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maquina;
use App\Models\MaquinaBunker;

class AdminController extends Controller
{
    public function index()
    {
        $maquinas        = Maquina::latest()->get();
        $maquinasBunker  = MaquinaBunker::latest()->get();

        return view('admin', compact('maquinas', 'maquinasBunker'));
    }
}

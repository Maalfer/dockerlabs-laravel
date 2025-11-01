<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maquina;

class AdminController extends Controller
{
    public function index()
    {
        $maquinas = Maquina::latest()->get();
        return view('admin', compact('maquinas'));
    }
}

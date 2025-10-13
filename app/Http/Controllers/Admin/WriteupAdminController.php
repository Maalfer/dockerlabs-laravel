<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Writeup;

class WriteupAdminController extends Controller
{
    public function index()
    {
        // Eager load de la máquina; ordena por fecha desc; pagina
        $items = Writeup::with('maquina')
            ->latest()
            ->paginate(20);

        return view('admin.writeups-index', compact('items'));
    }
}

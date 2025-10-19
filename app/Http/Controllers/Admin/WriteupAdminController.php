<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Writeup;

class WriteupAdminController extends Controller
{
    public function index()
    {
        $items = Writeup::with('maquina')->latest()->paginate(20);
        return view('admin.writeups-index', compact('items'));
    }

    public function destroy(Writeup $writeup)
    {
        $writeup->delete();
        return back()->with('success','Writeup eliminado correctamente.');
    }
}

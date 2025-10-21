<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maquina;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // mismos niveles que usaste en el scope/Blade
        $niveles = ['muy-facil', 'facil', 'medio', 'dificil'];

        // lee ?dificultad=...
        $filtro = $request->query('dificultad');

        // usa el scope difficulty() que añadimos al modelo
        $maquinas = Maquina::query()
            ->difficulty($filtro)
            ->latest()
            ->paginate(12)
            ->appends($request->query());

        return view('home', [
            'maquinas' => $maquinas,
            'filtroDificultad' => in_array($filtro, $niveles) ? $filtro : null,
        ]);
    }
}

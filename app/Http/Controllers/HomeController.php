<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maquina;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $niveles = ['muy-facil', 'facil', 'medio', 'dificil'];
        $filtro  = $request->query('dificultad');

        $maquinas = Maquina::query()
            ->difficulty($filtro)
            ->latest()
            ->paginate(12)
            ->appends($request->query());

        $wj = DB::table('writeups as w')
            ->join('maquinas as m', 'm.id', '=', 'w.maquina_id');

        $writeupsHasUserId = Schema::hasColumn('writeups', 'user_id');
        $writeupsHasAutor  = Schema::hasColumn('writeups', 'autor');

        $hasUsersTable = Schema::hasTable('users');
        $usersHasId    = $hasUsersTable && Schema::hasColumn('users', 'id');
        $usersHasName  = $hasUsersTable && Schema::hasColumn('users', 'name');

        $usersJoined = false;
        if ($writeupsHasUserId && $usersHasId && $usersHasName) {
            $wj->leftJoin('users as u', 'u.id', '=', 'w.user_id');
            $usersJoined = true;
        }

        if ($usersJoined && $writeupsHasAutor) {
            $wj->selectRaw("
                COALESCE(u.name, w.autor) as nombre,
                SUM(CASE LOWER(REPLACE(m.dificultad, ' ', '-'))
                    WHEN 'dificil' THEN 4
                    WHEN 'medio'   THEN 3
                    WHEN 'facil'   THEN 2
                    WHEN 'muy-facil' THEN 1
                    ELSE 0 END
                ) as puntos,
                COUNT(*) as total_writeups
            ");
        } elseif ($usersJoined) {
            $wj->selectRaw("
                u.name as nombre,
                SUM(CASE LOWER(REPLACE(m.dificultad, ' ', '-'))
                    WHEN 'dificil' THEN 4
                    WHEN 'medio'   THEN 3
                    WHEN 'facil'   THEN 2
                    WHEN 'muy-facil' THEN 1
                    ELSE 0 END
                ) as puntos,
                COUNT(*) as total_writeups
            ");
        } elseif ($writeupsHasAutor) {
            $wj->selectRaw("
                w.autor as nombre,
                SUM(CASE LOWER(REPLACE(m.dificultad, ' ', '-'))
                    WHEN 'dificil' THEN 4
                    WHEN 'medio'   THEN 3
                    WHEN 'facil'   THEN 2
                    WHEN 'muy-facil' THEN 1
                    ELSE 0 END
                ) as puntos,
                COUNT(*) as total_writeups
            ");
        } else {
            $wj->selectRaw("
                'Desconocido' as nombre,
                SUM(CASE LOWER(REPLACE(m.dificultad, ' ', '-'))
                    WHEN 'dificil' THEN 4
                    WHEN 'medio'   THEN 3
                    WHEN 'facil'   THEN 2
                    WHEN 'muy-facil' THEN 1
                    ELSE 0 END
                ) as puntos,
                COUNT(*) as total_writeups
            ");
        }

        $rankingJugadores = $wj
            ->groupBy('nombre')
            ->orderByDesc('puntos')
            ->orderBy('nombre')
            ->limit(100)
            ->get();

        $mc = DB::table('maquinas as m');

        $maquinasHasUserId = Schema::hasColumn('maquinas', 'user_id');
        $maquinasHasAutor  = Schema::hasColumn('maquinas', 'autor');

        $hasUsersTable2    = Schema::hasTable('users');
        $usersHasId2       = $hasUsersTable2 && Schema::hasColumn('users', 'id');
        $usersHasName2     = $hasUsersTable2 && Schema::hasColumn('users', 'name');
        $usersHasUsername2 = $hasUsersTable2 && Schema::hasColumn('users', 'username');
        $usersHasEmail2    = $hasUsersTable2 && Schema::hasColumn('users', 'email');

        if ($maquinasHasUserId && $usersHasId2 && ($usersHasName2 || $usersHasUsername2 || $usersHasEmail2)) {
            $mc->leftJoin('users as u', 'u.id', '=', 'm.user_id');
        }

        $displayParts = [];
        if ($maquinasHasAutor)       $displayParts[] = "NULLIF(TRIM(m.autor),'')";
        if ($usersHasName2)          $displayParts[] = "NULLIF(TRIM(u.name),'')";
        if ($usersHasUsername2)      $displayParts[] = "NULLIF(TRIM(u.username),'')";
        if ($usersHasEmail2)         $displayParts[] = "NULLIF(TRIM(u.email),'')";
        $displayExpr = $displayParts ? 'COALESCE(' . implode(', ', $displayParts) . ", 'Desconocido')" : "'Desconocido'";

        $keyExpr = "CASE WHEN " . ($maquinasHasAutor ? "NULLIF(TRIM(m.autor),'') IS NOT NULL" : "1=0") . " THEN TRIM(m.autor) ";
        $fallbackUser = [];
        if ($usersHasUsername2) $fallbackUser[] = "NULLIF(TRIM(u.username),'')";
        if ($usersHasName2)     $fallbackUser[] = "NULLIF(TRIM(u.name),'')";
        if ($usersHasEmail2)    $fallbackUser[] = "NULLIF(TRIM(u.email),'')";
        if ($fallbackUser) {
            $keyExpr .= "ELSE COALESCE(" . implode(', ', $fallbackUser) . ", 'Desconocido') END";
        } else {
            $keyExpr .= "ELSE 'Desconocido' END";
        }

        $mc->selectRaw("$displayExpr as nombre, COUNT(*) as total_maquinas, $keyExpr as creador_key")
           ->groupBy('creador_key', 'nombre');

        $rankingCreadores = $mc
            ->orderByDesc('total_maquinas')
            ->orderBy('nombre')
            ->limit(100)
            ->get();

        return view('home', [
            'maquinas'          => $maquinas,
            'filtroDificultad'  => in_array($filtro, $niveles) ? $filtro : null,
            'rankingJugadores'  => $rankingJugadores,
            'rankingCreadores'  => $rankingCreadores,
        ]);
    }
}

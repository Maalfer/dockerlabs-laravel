<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaquinaBunker;
use Illuminate\Support\Facades\Storage;

class MaquinaBunkerAdminController extends Controller
{
    public function destroy(MaquinaBunker $maquina)
    {
        $path = $maquina->imagen_path;
        if ($path && str_starts_with($path, 'maquinas/')) {
            Storage::disk('public')->delete($path);
        }
        $maquina->delete();

        return back()->with('success', 'Máquina BunkerLabs eliminada correctamente');
    }
}

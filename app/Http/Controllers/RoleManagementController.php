<?php

// app/Http/Controllers/RoleManagementController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('profile.roles-index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required','in:admin,moderator,user'],
        ]);

        // Evita que un admin se quite a sí mismo el último rol admin (opcional)
        if (auth()->id() === $user->id && $data['role'] !== 'admin') {
            // Si hay más admins, se permite; si no, lo bloqueas.
            $otherAdmins = User::where('role','admin')->where('id','<>',$user->id)->count();
            if ($otherAdmins === 0) {
                return back()->withErrors(['role' => 'No puedes quitarte el rol de admin si eres el único administrador.']);
            }
        }

        $user->update(['role' => $data['role']]);

        return back()->with('status', 'Rol actualizado correctamente.');
    }

    public function destroyUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['delete' => 'No puedes eliminar tu propio usuario.']);
        }
        $user->delete();
        return back()->with('status', 'Usuario eliminado correctamente.');
    }
}

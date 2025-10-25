<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Writeup;
use App\Models\WriteupTemporal;
use App\Models\EnvioMaquina;
use App\Models\Maquina;

class ProfileController extends Controller
{
    // GET /perfil
    public function edit()
    {
        $user = Auth::user();

        // Métricas rápidas para el panel de perfil (tolerantes a fallos)
        try {
            $writeupsPendUser = WriteupTemporal::query()
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('autor', $user->name)
                      ->orWhere('autor_email', $user->email);
                })
                ->count();
        } catch (\Throwable $e) { $writeupsPendUser = 0; }

        try {
            $writeupsAprobUser = Writeup::query()
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('autor', $user->name)
                      ->orWhere('autor_email', $user->email);
                })
                ->count();
        } catch (\Throwable $e) { $writeupsAprobUser = 0; }

        try {
            $maquinasEnvUser = EnvioMaquina::query()
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('autor_nombre', $user->name)
                      ->orWhere('autor_enlace', $user->email);
                })
                ->count();
        } catch (\Throwable $e) { $maquinasEnvUser = 0; }

        try {
            $maquinasAprobUser = Maquina::query()
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('autor', $user->name)
                      ->orWhere('autor_email', $user->email);
                })
                ->count();
        } catch (\Throwable $e) { $maquinasAprobUser = 0; }

        try {
            $writeupsAprobGlobal = Writeup::query()->count();
            $writeupsPendGlobal  = WriteupTemporal::query()->count();
        } catch (\Throwable $e) {
            $writeupsAprobGlobal = 0;
            $writeupsPendGlobal  = 0;
        }

        $stats = [
            'writeups_pendientes_user'      => $writeupsPendUser,
            'writeups_aprobados_user'       => $writeupsAprobUser,
            'maquinas_enviadas_user'        => $maquinasEnvUser,
            'maquinas_aprobadas_user'       => $maquinasAprobUser,
            'maquinas_aprobadas_user_known' => true,
            'writeups_aprobados_global'     => $writeupsAprobGlobal,
            'writeups_pendientes_global'    => $writeupsPendGlobal,
        ];

        return view('profile.edit', compact('user','stats'));
    }

    // POST/PUT /perfil -> actualizar perfil
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'             => ['required','string','max:120'],
            'email'            => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'current_password' => ['nullable','string','min:8'],
            'new_password'     => ['nullable','string','min:8','confirmed'],
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'] ?? '', $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.'])->withInput();
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('status', 'Perfil actualizado correctamente.');
    }

    // GET /perfil/roles (solo admin) -> listado + filtro
    public function rolesIndex(Request $request)
    {
        $this->authorizeForRoles(['admin']);

        $rolesPermitidos = ['admin','moderator','user'];

        $query = User::query();

        if ($search = trim($request->get('q',''))) {
            $query->where(function($q) use ($search) {
                $q->where('name','LIKE',"%{$search}%")
                  ->orWhere('email','LIKE',"%{$search}%");
            });
        }

        $users = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('profile.roles-index', [
            'users' => $users,
            'roles' => $rolesPermitidos,
        ]);
    }

    // PATCH /perfil/roles/{user} -> cambio de rol (route model binding)
    public function rolesUpdate(Request $request, User $user)
    {
        $this->authorizeForRoles(['admin']);

        $data = $request->validate([
            'role' => ['required', Rule::in(['admin','moderator','user'])],
        ]);

        $target = $user;
        $old    = $target->role;
        $new    = $data['role'];

        // No dejar el sistema sin administradores
        if ($old === 'admin' && $new !== 'admin') {
            $admins = User::where('role','admin')->count();
            if ($admins <= 1) {
                return back()->withErrors(['role' => 'No puedes dejar el sistema sin ningún administrador.']);
            }
        }

        // Evitar auto-degradarte mientras estás conectado
        if ($target->id === Auth::id() && $old === 'admin' && $new !== 'admin') {
            return back()->withErrors(['role' => 'No puedes degradar tu propio usuario mientras estás conectado.']);
        }

        $target->role = $new;
        $target->save();

        return back()->with('status', 'Rol actualizado correctamente.');
    }

    // DELETE /perfil/roles/{user} -> eliminar usuario
    public function destroyUser(User $user)
    {
        $this->authorizeForRoles(['admin']);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['delete' => 'No puedes eliminar tu propio usuario.']);
        }

        if ($user->role === 'admin') {
            $admins = User::where('role','admin')->count();
            if ($admins <= 1) {
                return back()->withErrors(['delete' => 'No puedes eliminar al último administrador.']);
            }
        }

        $user->delete();

        return back()->with('status', 'Usuario eliminado correctamente.');
    }

    /**
     * Autoriza que el usuario actual tenga alguno de los roles requeridos.
     */
    protected function authorizeForRoles(array $roles): void
    {
        $current = Auth::user();
        if (!$current || !in_array($current->role, $roles, true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }
}

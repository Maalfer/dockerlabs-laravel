@extends('layouts.app')

@section('title','Gestión de roles')

@section('content')
    <h2>Gestión de roles</h2>

    @if (session('status'))
        <div style="margin:1rem 0; padding:0.75rem; background:#dcfce7; border:1px solid #16a34a;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="margin:1rem 0; padding:0.75rem; background:#fee2e2; border:1px solid #ef4444;">
            <ul style="margin:0; padding-left:1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p style="margin: 0.5rem 0 1rem;">
        Solo los <strong>administradores</strong> pueden cambiar roles y eliminar usuarios.
    </p>

    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="background:#1e293b; color:#fff;">
                <th style="text-align:left; padding:0.5rem;">ID</th>
                <th style="text-align:left; padding:0.5rem;">Nombre</th>
                <th style="text-align:left; padding:0.5rem;">Email</th>
                <th style="text-align:left; padding:0.5rem;">Rol</th>
                <th style="text-align:left; padding:0.5rem;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $u)
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="padding:0.5rem;">{{ $u->id }}</td>
                    <td style="padding:0.5rem;">{{ $u->name }}</td>
                    <td style="padding:0.5rem;">{{ $u->email }}</td>
                    <td style="padding:0.5rem;">
                        <form method="POST" action="{{ route('dockerlabs.profile.roles.update', $u) }}">
                            @csrf
                            @method('PATCH')
                            <select name="role" onchange="this.form.submit()"
                                    style="padding:0.25rem;">
                                <option value="admin"     {{ $u->role==='admin' ? 'selected' : '' }}>Admin</option>
                                <option value="moderator" {{ $u->role==='moderator' ? 'selected' : '' }}>Moderador</option>
                                <option value="user"      {{ $u->role==='user' ? 'selected' : '' }}>Usuario</option>
                            </select>
                        </form>
                    </td>
                    <td style="padding:0.5rem;">
                        <form method="POST" action="{{ route('dockerlabs.profile.roles.destroyUser', $u) }}"
                              onsubmit="return confirm('¿Eliminar usuario {{ $u->email }}? Esta acción es irreversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding:0.5rem 0.75rem; background:#ef4444; color:#fff; border:none; cursor:pointer;">
                                Eliminar usuario
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:1rem;">
        {{ $users->links() }}
    </div>
@endsection

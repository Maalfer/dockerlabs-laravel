@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <h2>Mi Perfil</h2>

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

    <form method="POST" action="{{ route('profile.update') }}" style="max-width:500px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom:1rem;">
            <label for="name">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                   required style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>

        <div style="margin-bottom:1rem;">
            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                   required style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>

        <hr style="margin:1.5rem 0; opacity:0.3;">

        <p style="margin:0 0 0.5rem 0; font-weight:600;">Cambio de contraseña (opcional)</p>

        <div style="margin-bottom:1rem;">
            <label for="current_password">Contraseña actual</label>
            <input id="current_password" name="current_password" type="password"
                   style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>

        <div style="margin-bottom:1rem;">
            <label for="new_password">Nueva contraseña</label>
            <input id="new_password" name="new_password" type="password"
                   style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>

        <div style="margin-bottom:1rem;">
            <label for="new_password_confirmation">Confirmar nueva contraseña</label>
            <input id="new_password_confirmation" name="new_password_confirmation" type="password"
                   style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>

        <button type="submit"
                style="padding:0.75rem 1.25rem; background:#1e293b; color:#fff; border:none; cursor:pointer;">
            Guardar cambios
        </button>
    </form>
@endsection

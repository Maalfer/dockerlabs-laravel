@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="auth-page container" style="max-width:520px;">
    <h2 style="margin-bottom: .75rem;">Iniciar sesión — DockerLabs</h2>
    <p style="margin:0 0 1rem; opacity:.85;">Accede con tu cuenta para gestionar writeups y tu perfil.</p>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert" style="margin-bottom:1rem;">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-info" role="status" style="margin-bottom:1rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('dockerlabs.login.post') }}" style="display:grid; gap:12px;">
        @csrf

        <label style="display:grid; gap:6px;">
            Email
            <input type="email" name="email" class="form-control" required autofocus
                   value="{{ old('email') }}">
        </label>

        <label style="display:grid; gap:6px;">
            Contraseña
            <input type="password" name="password" class="form-control" required>
        </label>

        <div style="display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap;">
            <label style="display:inline-flex; align-items:center; gap:.5rem; margin:.25rem 0;">
                <input type="checkbox" name="remember"> Recuérdame
            </label>

            <div style="display:flex; gap:.5rem; align-items:center;">
                <a href="{{ route('dockerlabs.register') }}" class="link">¿No tienes cuenta? Regístrate</a>
            </div>
        </div>

        <div style="display:flex; gap:.6rem; align-items:center;">
            <button type="submit" class="btn btn-primary">Entrar</button>
            <a href="{{ route('dockerlabs.home') }}" class="btn">Cancelar</a>
        </div>
    </form>

    {{-- Botón adicional para acceder al login de BunkerLabs --}}
    <div style="text-align:center; margin-top:1.5rem;">
        <a href="{{ route('bunkerlabs.login') }}"
           style="display:inline-block; padding:0.65rem 1.25rem; background:#9f1239; color:white; text-decoration:none; border-radius:6px;">
            Bunkerlabs
        </a>
    </div>
</div>
@endsection

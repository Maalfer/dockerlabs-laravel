@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
    <h2>Iniciar Sesión</h2>
    <form method="POST" action="/login" style="max-width:400px; margin:auto;">
        @csrf
        <div style="margin-bottom:1rem;">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required
                   style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>
        <div style="margin-bottom:1rem;">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required
                   style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>
        <button type="submit"
                style="padding:0.75rem 1.5rem; background:#1e293b; color:white; border:none; cursor:pointer;">
            Entrar
        </button>
    </form>
@endsection

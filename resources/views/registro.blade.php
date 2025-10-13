@extends('layouts.app')

@section('title', 'Registro')

@section('content')
    <h2>Crear una cuenta</h2>
    <form method="POST" action="/registro" style="max-width:400px; margin:auto;">
        @csrf
        <div style="margin-bottom:1rem;">
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required
                   style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>
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
        <div style="margin-bottom:1rem;">
            <label for="password_confirmation">Confirmar contraseña:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   style="width:100%; padding:0.5rem; margin-top:0.25rem;">
        </div>
        <button type="submit"
                style="padding:0.75rem 1.5rem; background:#1e293b; color:white; border:none; cursor:pointer;">
            Registrarse
        </button>
    </form>
@endsection

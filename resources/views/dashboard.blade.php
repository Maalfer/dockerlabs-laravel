@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2>Panel de DockerLabs</h2>
    <p>�Bienvenido, {{ Auth::user()->name }}! \U0001f680</p>

    <form action="{{ route('logout') }}" method="POST" style="margin-top:1rem;">
        @csrf
        <button type="submit" style="padding:0.5rem 1rem; background:#1e293b; color:white; border:none; cursor:pointer;">
            Cerrar sesi�n
        </button>
    </form>
@endsection

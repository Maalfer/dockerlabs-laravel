@extends('layouts.app')

@section('title', 'Enviar máquina')

@section('content')
<div class="admin-page container" style="max-width:800px;">
    <h2>Enviar máquina</h2>
    <p>Completa el formulario para proponer una nueva máquina.</p>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('enviar-maquina.store') }}" novalidate style="display:grid; gap:14px;">
        @csrf

        <div class="form-row">
            <label for="nombre_maquina">Nombre de la máquina</label>
            <input id="nombre_maquina" name="nombre_maquina" type="text" class="form-control"
                   value="{{ old('nombre_maquina') }}" required>
        </div>

        <div class="form-row">
            <label for="dificultad">Dificultad</label>
            <select id="dificultad" name="dificultad" class="form-control" required>
                <option value="">Selecciona...</option>
                <option value="facil"  {{ old('dificultad')==='facil' ? 'selected' : '' }}>Fácil</option>
                <option value="medio"  {{ old('dificultad')==='medio' ? 'selected' : '' }}>Medio</option>
                <option value="dificil"{{ old('dificultad')==='dificil' ? 'selected' : '' }}>Difícil</option>
            </select>
        </div>

        <div class="form-row">
            <label for="autor_nombre">Nombre del autor</label>
            <input id="autor_nombre" name="autor_nombre" type="text" class="form-control"
                   value="{{ old('autor_nombre') }}" required>
        </div>

        <div class="form-row">
            <label for="autor_enlace">Enlace del autor (URL)</label>
            <input id="autor_enlace" name="autor_enlace" type="url" class="form-control"
                   value="{{ old('autor_enlace') }}" placeholder="https://...">
        </div>

        <div class="form-row">
            <label for="fecha_creacion">Fecha de creación</label>
            <input id="fecha_creacion" name="fecha_creacion" type="date" class="form-control"
                   value="{{ old('fecha_creacion') }}">
        </div>

        <div class="form-row">
            <label for="writeup">Writeup (URL)</label>
            <input id="writeup" name="writeup" type="url" class="form-control"
                   value="{{ old('writeup') }}" placeholder="https://...">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <a href="{{ url('/') }}" class="btn">Cancelar</a>
        </div>
    </form>
</div>
@endsection

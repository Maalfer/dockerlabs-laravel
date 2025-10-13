{{-- resources/views/admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Administración')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    <div class="admin-page">
        <div class="container">

            {{-- Mensajes flash --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul style="margin:0; padding-left:1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h2>Agregar Nueva Máquina</h2>

            <form action="{{ route('admin.maquinas.store') }}" method="POST" class="form">
                @csrf

                <div class="form-row">
                    <label for="nombre">Nombre de la Máquina</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required value="{{ old('nombre') }}">
                </div>

                <div class="form-row">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required>{{ old('descripcion') }}</textarea>
                </div>

                <div class="form-row">
                    <label for="dificultad">Dificultad</label>
                    <select id="dificultad" name="dificultad" class="form-control" required>
                        <option value="facil" {{ old('dificultad')==='facil' ? 'selected' : '' }}>Fácil</option>
                        <option value="medio" {{ old('dificultad')==='medio' ? 'selected' : '' }}>Medio</option>
                        <option value="dificil" {{ old('dificultad')==='dificil' ? 'selected' : '' }}>Difícil</option>
                        <option value="muy-facil" {{ old('dificultad')==='muy-facil' ? 'selected' : '' }}>Muy Fácil</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="enlace_descarga">Enlace de Descarga (Opcional)</label>
                    <input type="url" id="enlace_descarga" name="enlace_descarga" class="form-control" value="{{ old('enlace_descarga') }}">
                </div>

                <button type="submit" class="btn btn-primary">Agregar Máquina</button>
            </form>

            <hr class="divider">

            <h2>Máquinas existentes</h2>

            @if(isset($maquinas) && $maquinas->count())
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Nombre</th>
                                <th style="width:160px;">Dificultad</th>
                                <th>Descripción</th>
                                <th style="width:140px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($maquinas as $maquina)
                                <tr>
                                    <td>#{{ $maquina->id }}</td>
                                    <td>{{ $maquina->nombre }}</td>
                                    <td>
                                        <span class="badge {{ $maquina->dificultad_clase }}">
                                            {{ $maquina->dificultad_etiqueta }}
                                        </span>
                                    </td>
                                    <td>{{ $maquina->descripcion }}</td>
                                    <td>
                                        {{-- Mostrar enlace de descarga si está disponible --}}
                                        @if($maquina->enlace_descarga)
                                            {{-- Aseguramos que el enlace de descarga es accesible --}}
                                            <a href="{{ filter_var($maquina->enlace_descarga, FILTER_VALIDATE_URL) ? $maquina->enlace_descarga : '#' }}" class="btn btn-success" target="_blank">
                                                Descargar
                                            </a>
                                        @endif

                                        <form action="{{ route('admin.maquinas.destroy', $maquina) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Seguro que quieres eliminar la máquina «{{ $maquina->nombre }}»?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="muted">No hay máquinas registradas todavía.</p>
            @endif
        </div>
    </div>
@endsection

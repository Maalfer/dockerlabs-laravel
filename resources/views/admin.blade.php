{{-- resources/views/admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Administración')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    @php($prefill = session('prefill_maquina'))
    <div class="admin-page">
        <div class="container">

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

            @if($prefill)
                <div class="alert alert-info" style="margin-bottom:1rem;">
                    Estás creando una máquina desde el envío #{{ $prefill['envio_id'] }} de <strong>{{ $prefill['autor'] }}</strong>. Revisa los datos pre-cargados y pulsa “Agregar Máquina”.
                </div>
            @endif

            <h2>Agregar Nueva Máquina</h2>

            <form action="{{ route('admin.maquinas.store') }}" method="POST" class="form">
                @csrf

                @if($prefill)
                    <input type="hidden" name="envio_id" value="{{ $prefill['envio_id'] }}">
                @endif

                <div class="form-row">
                    <label for="nombre">Nombre de la Máquina</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required
                           value="{{ old('nombre', $prefill['nombre'] ?? '') }}">
                </div>

                <div class="form-row">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required>{{ old('descripcion', $prefill['descripcion'] ?? '') }}</textarea>
                </div>

                <div class="form-row">
                    <label for="dificultad">Dificultad</label>
                    @php($difOld = old('dificultad', $prefill['dificultad'] ?? 'medio'))
                    <select id="dificultad" name="dificultad" class="form-control" required>
                        <option value="facil" {{ $difOld==='facil' ? 'selected' : '' }}>Fácil</option>
                        <option value="medio" {{ $difOld==='medio' ? 'selected' : '' }}>Medio</option>
                        <option value="dificil" {{ $difOld==='dificil' ? 'selected' : '' }}>Difícil</option>
                        <option value="muy-facil" {{ $difOld==='muy-facil' ? 'selected' : '' }}>Muy Fácil</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="enlace_descarga">Enlace de Descarga (Opcional)</label>
                    <input type="url" id="enlace_descarga" name="enlace_descarga" class="form-control"
                           value="{{ old('enlace_descarga', $prefill['enlace_descarga'] ?? '') }}">
                </div>

                <div class="form-row">
                    <label for="autor">Autor</label>
                    <input type="text" id="autor" name="autor" class="form-control"
                           value="{{ old('autor', $prefill['autor'] ?? '') }}">
                </div>

                <div class="form-row">
                    <label for="autor_url">Autor URL</label>
                    <input type="url" id="autor_url" name="autor_url" class="form-control"
                           value="{{ old('autor_url', $prefill['autor_url'] ?? '') }}">
                </div>

                <div class="form-row">
                    <label for="fecha_creacion">Fecha de Creación</label>
                    <input type="date" id="fecha_creacion" name="fecha_creacion" class="form-control"
                           value="{{ old('fecha_creacion', $prefill['fecha_creacion'] ?? '') }}">
                </div>

                <div class="form-row">
                    <label for="writeup">Writeup</label>
                    <input type="url" id="writeup" name="writeup" class="form-control"
                           value="{{ old('writeup', $prefill['writeup'] ?? '') }}">
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
                                        @if($maquina->enlace_descarga)
                                            <a href="{{ filter_var($maquina->enlace_descarga, FILTER_VALIDATE_URL) ? $maquina->enlace_descarga : '#' }}"
                                               class="btn btn-success" target="_blank" rel="noopener">
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

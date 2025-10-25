@extends('layouts.app')

@section('title', 'Bunkerlabs')

@section('content')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">

<div class="top-bar" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.5rem 1px 1rem; flex-wrap:wrap;">
  <h1 style="margin:0;">Bunker: Máquinas</h1>
  <div class="filters-bar" style="display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; justify-content:flex-end; padding:.5rem 0 1rem; min-width:320px;">
    @php($f = $f ?? null)
    <a href="{{ route('bunkerlabs.home') }}" class="chip {{ empty($f) ? 'active' : '' }}">Todas</a>
    <a href="{{ route('bunkerlabs.home', ['dificultad' => 'muy-facil']) }}" class="chip chip-cyan {{ $f === 'muy-facil' ? 'active' : '' }}">Muy Fácil</a>
    <a href="{{ route('bunkerlabs.home', ['dificultad' => 'facil']) }}" class="chip chip-green {{ $f === 'facil' ? 'active' : '' }}">Fácil</a>
    <a href="{{ route('bunkerlabs.home', ['dificultad' => 'medio']) }}" class="chip chip-amber {{ $f === 'medio' ? 'active' : '' }}">Medio</a>
    <a href="{{ route('bunkerlabs.home', ['dificultad' => 'dificil']) }}" class="chip chip-red {{ $f === 'dificil' ? 'active' : '' }}">Difícil</a>
  </div>
</div>

@if($maquinas->isEmpty())
  <p class="no-results">No hay máquinas en el bunker para el filtro seleccionado.</p>
@endif

<div class="machines-grid">
  @foreach ($maquinas as $maquina)
    <article class="machine-row">
      <div class="machine-main">
        <span class="machine-name">{{ $maquina->nombre }}</span>
        <span class="badge {{ $maquina->dificultad_clase }}">{{ $maquina->dificultad_etiqueta }}</span>
        <p class="machine-desc">{{ $maquina->descripcion }}</p>
      </div>

      <div class="machine-actions">
        @if (!empty($maquina->enlace_descarga))
          <a href="{{ $maquina->enlace_descarga }}" target="_blank" rel="noopener" class="btn btn-xs">Descargar</a>
        @endif
      </div>
    </article>
  @endforeach
</div>
@endsection

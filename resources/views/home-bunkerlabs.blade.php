@extends('layouts.app-bunkerlabs')

@section('title', 'Bunkerlabs')

@section('content')
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
    @php($uid = $maquina->id ?? $loop->index)
    <article class="machine-row">
      <div class="machine-main">
        <span class="machine-name open-presentacion"
              data-target="pres-{{ $uid }}"
              role="button"
              aria-haspopup="dialog"
              aria-controls="pres-{{ $uid }}">
          {{ $maquina->nombre }}
        </span>
        <span class="badge {{ $maquina->dificultad_clase }}">{{ $maquina->dificultad_etiqueta }}</span>
        <button class="btn btn-xs btn-outline open-desc"
                type="button"
                data-target="desc-{{ $uid }}"
                data-ignore-card="1"
                aria-haspopup="dialog"
                aria-controls="desc-{{ $uid }}">
          Descripción
        </button>
        <p class="machine-desc">{{ $maquina->descripcion }}</p>
      </div>

      <div class="machine-actions">
        @if (!empty($maquina->enlace_descarga))
          <a href="{{ $maquina->enlace_descarga }}" target="_blank" rel="noopener" class="btn btn-xs">Descargar</a>
        @endif
      </div>
    </article>

    <div id="pres-{{ $uid }}" class="modal" role="dialog" aria-modal="true" aria-labelledby="pres-title-{{ $uid }}" aria-hidden="true">
      <div class="modal-card" role="document">
        <header class="modal-header">
          <h3 id="pres-title-{{ $uid }}" class="modal-title">
            {{ $maquina->nombre }} — Presentación
          </h3>
          <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
        </header>
        <div class="modal-body">
          <div style="display:grid; grid-template-columns: 240px 1fr; gap:16px; align-items:start;">
            <div>
              <img src="{{ asset('images/logo.png') }}" alt="Presentación máquina" style="width:100%; height:auto; display:block; border-radius:12px;">
            </div>
            <div style="display:grid; gap:12px;">
              <dl style="display:grid; grid-template-columns: 140px 1fr; gap:8px 12px; align-items:center;">
                <dt style="opacity:.75;">Autor</dt>
                <dd>{{ $maquina->autor ?? 'N/D' }}</dd>

                <dt style="opacity:.75;">Autor URL</dt>
                <dd>
                  @php($au = $maquina->autor_url ?? $maquina->autor_enlace ?? null)
                  @if($au)
                    <a href="{{ $au }}" target="_blank" rel="noopener noreferrer">{{ $au }}</a>
                  @else
                    N/D
                  @endif
                </dd>

                <dt style="opacity:.75;">Creación</dt>
                <dd>{{ $maquina->fecha_creacion ?? ($maquina->created_at ? $maquina->created_at->format('Y-m-d') : 'N/D') }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <footer class="modal-footer">
          <button class="btn btn-xs modal-close" type="button">Cerrar</button>
        </footer>
      </div>
    </div>

    <div id="desc-{{ $uid }}" class="modal" role="dialog" aria-modal="true" aria-labelledby="desc-title-{{ $uid }}" aria-hidden="true">
      <div class="modal-card" role="document">
        <header class="modal-header">
          <h3 id="desc-title-{{ $uid }}" class="modal-title">
            {{ $maquina->nombre }} Descripción
          </h3>
          <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
        </header>
        <div class="modal-body">
          <p>{{ $maquina->descripcion }}</p>
        </div>
        <footer class="modal-footer">
          <button class="btn btn-xs modal-close" type="button">Cerrar</button>
        </footer>
      </div>
    </div>
  @endforeach
</div>

<script>
(function () {
  const open = (el) => { if (!el) return; el.classList.add('open'); el.setAttribute('aria-hidden','false'); };
  const close = (el) => { if (!el) return; el.classList.remove('open'); el.setAttribute('aria-hidden','true'); };

  document.addEventListener('click', (e) => {
    const pres = e.target.closest('.open-presentacion');
    if (pres) { open(document.getElementById(pres.getAttribute('data-target'))); return; }
    const desc = e.target.closest('.open-desc');
    if (desc) { open(document.getElementById(desc.getAttribute('data-target'))); return; }
    if (e.target.matches('.modal-close')) { close(e.target.closest('.modal')); return; }
    if (e.target.classList && e.target.classList.contains('modal')) { close(e.target); return; }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal.open').forEach(m => m && m.classList.remove('open'));
      document.querySelectorAll('.modal').forEach(m => m && m.setAttribute('aria-hidden','true'));
    }
  });
})();
</script>
@endsection

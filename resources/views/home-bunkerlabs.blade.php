@extends('layouts.app-bunkerlabs')

@section('title', 'Bunkerlabs')

@section('content')

@php($f = $f ?? null)

{{-- ===== TOP BAR ===== --}}
<div class="top-bar">
  <h1 class="page-title">Bunker: Máquinas</h1>

  <div class="filters-bar">
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

{{-- ===== GRID DE CARDS ===== --}}
<div class="machines-grid">
  @foreach ($maquinas as $maquina)
    @php($uid = $maquina->id ?? $loop->index)
    <article class="machine-card">
      <div class="machine-media">
        @php($thumb = $maquina->imagen_path ?? null)
        @if($thumb)
          <img class="machine-thumb" src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/storage/']) ? $thumb : \Illuminate\Support\Facades\Storage::url($thumb) }}" alt="Miniatura de {{ $maquina->nombre }}">
        @else
          <div class="machine-thumb placeholder" aria-hidden="true">
            <i class="fa-solid fa-server"></i>
          </div>
        @endif
      </div>

      <div class="machine-content">
        <div class="machine-header">
          <button class="machine-name open-presentacion" type="button"
                  data-target="pres-{{ $uid }}" aria-haspopup="dialog"
                  aria-controls="pres-{{ $uid }}">
            {{ $maquina->nombre }}
          </button>

          <span class="badge {{ $maquina->dificultad_clase }}">
            {{ $maquina->dificultad_etiqueta }}
          </span>
        </div>

        <p class="machine-desc" title="{{ $maquina->descripcion }}">{{ $maquina->descripcion }}</p>

        <div class="machine-actions">
          <button class="btn btn-xs btn-outline open-desc" type="button"
                  data-target="desc-{{ $uid }}"
                  data-ignore-card="1" aria-haspopup="dialog"
                  aria-controls="desc-{{ $uid }}">
            Descripción
          </button>

          @if (!empty($maquina->enlace_descarga))
            <a href="{{ $maquina->enlace_descarga }}" target="_blank" rel="noopener" class="btn btn-xs btn-primary">
              Descargar
            </a>
          @endif
        </div>
      </div>
    </article>

    {{-- ===== MODAL: Presentación ===== --}}
    <div id="pres-{{ $uid }}" class="modal" role="dialog" aria-modal="true" aria-labelledby="pres-title-{{ $uid }}" aria-hidden="true">
      <div class="modal-card" role="document" tabindex="-1">
        <header class="modal-header">
          <h3 id="pres-title-{{ $uid }}" class="modal-title">
            {{ $maquina->nombre }} — Presentación
          </h3>
          <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
        </header>
        <div class="modal-body">
          <div class="pres-grid">
            <div>
              @if($thumb)
                <img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/storage/']) ? $thumb : \Illuminate\Support\Facades\Storage::url($thumb) }}"
                     alt="Imagen de {{ $maquina->nombre }}" class="pres-thumb">
              @else
                <img src="{{ asset('images/logo.png') }}" alt="Presentación máquina" class="pres-thumb">
              @endif
            </div>
            <div class="pres-info">
              <dl class="pres-dl">
                <dt>Autor</dt>
                <dd>{{ $maquina->autor ?? 'N/D' }}</dd>

                <dt>Autor URL</dt>
                <dd>
                  @php($au = $maquina->autor_url ?? $maquina->autor_enlace ?? null)
                  @if($au)
                    <a href="{{ $au }}" target="_blank" rel="noopener noreferrer">{{ $au }}</a>
                  @else
                    N/D
                  @endif
                </dd>

                <dt>Creación</dt>
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

    {{-- ===== MODAL: Descripción ===== --}}
    <div id="desc-{{ $uid }}" class="modal" role="dialog" aria-modal="true" aria-labelledby="desc-title-{{ $uid }}" aria-hidden="true">
      <div class="modal-card" role="document" tabindex="-1">
        <header class="modal-header">
          <h3 id="desc-title-{{ $uid }}" class="modal-title">
            {{ $maquina->nombre }} — Descripción
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

{{-- ===== JS: Modales con accesibilidad y focus ===== --}}
<script>
(function () {
  const open = (el) => {
    if (!el) return;
    el.classList.add('open');
    el.setAttribute('aria-hidden','false');
    const card = el.querySelector('.modal-card');
    // almacenar último foco
    el._lastFocus = document.activeElement;
    // foco inicial seguro
    setTimeout(() => {
      (card.querySelector('.modal-close') || card).focus();
    }, 0);
  };

  const close = (el) => {
    if (!el) return;
    el.classList.remove('open');
    el.setAttribute('aria-hidden','true');
    // devolver foco al origen
    const back = el._lastFocus;
    if (back && typeof back.focus === 'function') back.focus();
  };

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
      document.querySelectorAll('.modal.open').forEach(m => close(m));
    }
  });
})();
</script>
@endsection

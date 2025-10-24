@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<style>

  .top-bar{
    display:flex; align-items:center; justify-content:space-between;
    gap:1rem; padding:.5rem 1px 1rem;
    flex-wrap:wrap;
  }
  .rank-actions{
    display:flex; gap:.6rem; align-items:center; flex-wrap:wrap;
  }
  .btn-rank{
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.65rem 1rem; border-radius:10px;
    background: var(--surface, #12161c);
    color: var(--text, #e6e8ef);
    border:1px solid var(--border, #222834);
    font-size:.95rem; line-height:1.2; text-decoration:none;
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    cursor:pointer;
  }
  .btn-rank:hover{ transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.25); }

  /* ===== Filtros por dificultad ===== */
  .filters-bar {
    display: flex; flex-wrap: wrap; gap: .6rem;
    align-items: center; justify-content: flex-end;
    padding: .5rem 0 1rem;
    min-width: 320px;
  }
  .chip {
    --ring: transparent;
    display: inline-flex; align-items: center; gap: .6rem;
    padding: .65rem 1rem; border-radius: 999px;
    background: var(--surface, #12161c);
    color: var(--text, #e6e8ef);
    border: 1px solid var(--border, #222834);
    font-size: 1rem; line-height: 1.2;
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
    text-decoration: none; will-change: transform;
  }
  .chip:hover{
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0,0,0,.25);
    border-color: rgba(255,255,255,.12);
  }
  .chip.active{
    outline: 2px solid var(--ring);
    outline-offset:2px;
    box-shadow: 0 0 0 4px color-mix(in oklab, var(--ring) 20%, transparent);
  }
  .chip-cyan{  --ring:#0891b2; color:#0aaecf; background: color-mix(in oklab, #0891b2 12%, transparent); }
  .chip-green{ --ring:#16a34a; color:#22c55e; background: color-mix(in oklab, #16a34a 12%, transparent); }
  .chip-amber{ --ring:#d97706; color:#f59e0b; background: color-mix(in oklab, #d97706 12%, transparent); }
  .chip-red{   --ring:#dc2626; color:#f87171; background: color-mix(in oklab, #dc2626 12%, transparent); }
  .no-results{ margin:.5rem 0 1rem; opacity:.8; }

  /* ===== Tabla modales ranking ===== */
  .table{ width:100%; border-collapse:separate; border-spacing:0 8px; }
  .table thead th{ text-align:left; font-weight:600; opacity:.9; padding:.6rem .8rem; }
  .table tbody tr{
    background: var(--surface, #12161c); border:1px solid var(--border, #222834);
    box-shadow: 0 2px 12px rgba(0,0,0,.15);
  }
  .table tbody td{ padding:.6rem .8rem; vertical-align:middle; }
  .rank-medal{ font-weight:700; }

  /* ===== UX ===== */
  .machine-name{
    cursor:pointer; text-decoration:underline; text-underline-offset:2px;
  }
</style>

@php $f = $filtroDificultad ?? null; @endphp

{{-- ===== TOP BAR: Izquierda rankings / Derecha filtros ===== --}}
<div class="top-bar">
  <div class="rank-actions">
    <button class="btn-rank open-ranking-jugadores" type="button" data-target="ranking-jugadores">
      🏆 Ranking Jugadores
    </button>
    <button class="btn-rank open-ranking-creadores" type="button" data-target="ranking-creadores">
      🧩 Ranking Creadores
    </button>
  </div>

  <div class="filters-bar">
    <a href="{{ route('dockerlabs.home') }}" class="chip {{ empty($f) ? 'active' : '' }}">Todas</a>
    <a href="{{ route('dockerlabs.home', ['dificultad' => 'muy-facil']) }}" class="chip chip-cyan {{ $f === 'muy-facil' ? 'active' : '' }}">Muy Fácil</a>
    <a href="{{ route('dockerlabs.home', ['dificultad' => 'facil']) }}" class="chip chip-green {{ $f === 'facil' ? 'active' : '' }}">Fácil</a>
    <a href="{{ route('dockerlabs.home', ['dificultad' => 'medio']) }}" class="chip chip-amber {{ $f === 'medio' ? 'active' : '' }}">Medio</a>
    <a href="{{ route('dockerlabs.home', ['dificultad' => 'dificil']) }}" class="chip chip-red {{ $f === 'dificil' ? 'active' : '' }}">Difícil</a>
  </div>
</div>

@if($maquinas->isEmpty())
  <p class="no-results">No hay máquinas para el filtro seleccionado.</p>
@endif

{{-- ===== MODAL: Ranking Jugadores ===== --}}
<div id="ranking-jugadores" class="modal" role="dialog" aria-modal="true" aria-labelledby="ranking-jugadores-title" aria-hidden="true">
  <div class="modal-card" role="document">
    <header class="modal-header">
      <h3 id="ranking-jugadores-title" class="modal-title">🏆 Ranking de Jugadores</h3>
      <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
    </header>
    <div class="modal-body">
      @if(($rankingJugadores ?? collect())->isNotEmpty())
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Jugador</th>
              <th>Puntos</th>
              <th>Writeups aprobados</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rankingJugadores as $i => $row)
              <tr>
                <td class="rank-medal">{{ $i+1 }}</td>
                <td>{{ $row->nombre }}</td>
                <td>{{ $row->puntos }}</td>
                <td>{{ $row->total_writeups }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p>No hay datos suficientes para generar el ranking.</p>
      @endif
    </div>
    <footer class="modal-footer">
      <button class="btn btn-xs modal-close" type="button">Cerrar</button>
    </footer>
  </div>
</div>

{{-- ===== MODAL: Ranking Creadores ===== --}}
<div id="ranking-creadores" class="modal" role="dialog" aria-modal="true" aria-labelledby="ranking-creadores-title" aria-hidden="true">
  <div class="modal-card" role="document">
    <header class="modal-header">
      <h3 id="ranking-creadores-title" class="modal-title">🧩 Ranking de Creadores</h3>
      <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
    </header>
    <div class="modal-body">
      @if(($rankingCreadores ?? collect())->isNotEmpty())
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Creador</th>
              <th>Máquinas aprobadas</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rankingCreadores as $i => $row)
              <tr>
                <td class="rank-medal">{{ $i+1 }}</td>
                <td>{{ $row->nombre }}</td>
                <td>{{ $row->total_maquinas }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p>No hay datos suficientes para generar el ranking.</p>
      @endif
    </div>
    <footer class="modal-footer">
      <button class="btn btn-xs modal-close" type="button">Cerrar</button>
    </footer>
  </div>
</div>

<div class="machines-grid">
    @foreach ($maquinas as $maquina)
        <article class="machine-row">
            <div class="machine-main">
                {{-- CLICK EN EL NOMBRE => abre Presentación --}}
                <span class="machine-name open-presentacion"
                      data-target="pres-{{ $maquina->id ?? $loop->index }}"
                      role="button"
                      aria-haspopup="dialog"
                      aria-controls="pres-{{ $maquina->id ?? $loop->index }}">
                    {{ $maquina->nombre }}
                </span>

                <span class="badge {{ $maquina->dificultad_clase }}">
                    {{ $maquina->dificultad_etiqueta }}
                </span>

                {{-- CLICK EN BOTÓN DESCRIPCIÓN => abre Descripción --}}
                <button class="btn btn-xs btn-outline open-desc" type="button"
                        data-target="desc-{{ $maquina->id ?? $loop->index }}"
                        data-ignore-card="1"
                        aria-haspopup="dialog" aria-controls="desc-{{ $maquina->id ?? $loop->index }}">
                    Descripción
                </button>

                <button class="btn btn-xs btn-icon open-upload" type="button"
                        title="Subir writeup"
                        data-target="upload-{{ $maquina->id ?? $loop->index }}"
                        data-ignore-card="1"
                        aria-haspopup="dialog" aria-controls="upload-{{ $maquina->id ?? $loop->index }}">
                    <i class="fas fa-upload" aria-hidden="true"></i>
                </button>

                @if($maquina->enlace_descarga)
                    <a href="{{ $maquina->enlace_descarga }}" class="btn btn-xs btn-icon" target="_blank" title="Descargar" data-ignore-card="1" rel="noopener noreferrer">
                        <i class="fas fa-download" aria-hidden="true"></i>
                    </a>
                @else
                    <button class="btn btn-xs btn-icon" type="button" title="Descargar" data-ignore-card="1" disabled>
                        <i class="fas fa-download" aria-hidden="true"></i>
                    </button>
                @endif

                <button class="btn btn-xs btn-icon open-book" type="button"
                        title="Ver writeups aprobados"
                        data-target="book-{{ $maquina->id ?? $loop->index }}"
                        data-ignore-card="1"
                        aria-haspopup="dialog" aria-controls="book-{{ $maquina->id ?? $loop->index }}">
                    <i class="fas fa-book" aria-hidden="true"></i>
                </button>
            </div>
        </article>

        {{-- ===== MODAL: PRESENTACIÓN (nuevo) ===== --}}
        <div id="pres-{{ $maquina->id ?? $loop->index }}" class="modal" role="dialog"
             aria-modal="true" aria-labelledby="pres-title-{{ $maquina->id ?? $loop->index }}" aria-hidden="true">
            <div class="modal-card" role="document">
                <header class="modal-header">
                    <h3 id="pres-title-{{ $maquina->id ?? $loop->index }}" class="modal-title">
                        {{ $maquina->nombre }} — Presentación
                    </h3>
                    <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
                </header>
                <div class="modal-body">
                  <div style="display:grid; grid-template-columns: 240px 1fr; gap:16px; align-items:start;">
                    <div>
                      <img src="{{ asset('images/logo.png') }}" alt="Presentación máquina"
                           style="width:100%; height:auto; display:block; border-radius:12px;">
                    </div>
                    <div style="display:grid; gap:12px;">
                      <dl style="display:grid; grid-template-columns: 140px 1fr; gap:8px 12px; align-items:center;">
                        <dt style="color:var(--muted,#a8b8d6);">Autor</dt>
                        <dd>pingu</dd>

                        <dt style="color:var(--muted,#a8b8d6);">Autor URL</dt>
                        <dd><a href="https://dockerlabs.es" target="_blank" rel="noopener noreferrer">https://dockerlabs.es</a></dd>

                        <dt style="color:var(--muted,#a8b8d6);">Creación</dt>
                        <dd>2025-10-16</dd>
                      </dl>
                    </div>
                  </div>
                </div>
                <footer class="modal-footer">
                    <button class="btn btn-xs modal-close" type="button">Cerrar</button>
                </footer>
            </div>
        </div>

        {{-- ===== MODAL: DESCRIPCIÓN (existente) ===== --}}
        <div id="desc-{{ $maquina->id ?? $loop->index }}" class="modal" role="dialog"
             aria-modal="true" aria-labelledby="desc-title-{{ $maquina->id ?? $loop->index }}" aria-hidden="true">
            <div class="modal-card" role="document">
                <header class="modal-header">
                    <h3 id="desc-title-{{ $maquina->id ?? $loop->index }}" class="modal-title">
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

        {{-- ===== MODAL: SUBIR WRITEUP ===== --}}
        <div id="upload-{{ $maquina->id ?? $loop->index }}" class="modal" role="dialog"
             aria-modal="true" aria-labelledby="upload-title-{{ $maquina->id ?? $loop->index }}" aria-hidden="true">
            <div class="modal-card" role="document">
                <header class="modal-header">
                    <h3 id="upload-title-{{ $maquina->id ?? $loop->index }}" class="modal-title">
                        {{ $maquina->nombre }} — Enviar writeup
                    </h3>
                    <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
                </header>
                <form method="POST" action="{{ route('dockerlabs.writeups-temporal.store') }}">
                    @csrf
                    <input type="hidden" name="maquina_id" value="{{ $maquina->id }}">
                    <div style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>
                    <div class="modal-body">
                        <div class="alert" style="margin-bottom:10px; padding:.75rem; border:1px solid #ffd6a0; background:#fff3cd; color:#7a4d00;">
                            <strong>Recomendación:</strong> crea una cuenta e inicia sesión para poder gestionar tus writeups.
                            <a href="{{ route('dockerlabs.register') }}" class="link">Registrarme</a> ·
                            <a href="{{ route('dockerlabs.login') }}" class="link">Iniciar sesión</a>
                        </div>

                        <div class="form-row" style="display:grid; gap:10px;">
                            @auth
                                <label style="display:grid; gap:6px;">
                                    Autor
                                    <input type="text" name="autor" class="form-control" required maxlength="120"
                                           value="{{ auth()->user()->name }}" readonly>
                                </label>
                            @else
                                <label style="display:grid; gap:6px;">
                                    Autor
                                    <input type="text" name="autor" class="form-control" required maxlength="120" placeholder="Tu nombre o alias">
                                </label>
                                <label style="display:grid; gap:6px;">
                                    Email
                                    <input type="email" name="autor_email" class="form-control" required maxlength="255" placeholder="tu@email.com">
                                </label>
                            @endauth

                            <label style="display:grid; gap:6px;">
                                Enlace
                                <input type="url" name="enlace" class="form-control" required placeholder="https://...">
                            </label>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-error" style="margin-top:10px;">
                                {{ $errors->first() }}
                            </div>
                        @endif
                    </div>
                    <footer class="modal-footer">
                        <button class="btn btn-xs modal-close" type="button">Cancelar</button>
                        <button class="btn btn-xs btn-primary" type="submit">Enviar</button>
                    </footer>
                </form>
            </div>
        </div>

        {{-- ===== MODAL: WRITEUPS APROBADOS ===== --}}
        <div id="book-{{ $maquina->id ?? $loop->index }}" class="modal" role="dialog"
             aria-modal="true" aria-labelledby="book-title-{{ $maquina->id ?? $loop->index }}" aria-hidden="true">
            <div class="modal-card" role="document">
                <header class="modal-header">
                    <h3 id="book-title-{{ $maquina->id ?? $loop->index }}" class="modal-title">
                        {{ $maquina->nombre }} Writeups aprobados
                    </h3>
                    <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
                </header>
                <div class="modal-body">
                    @if(isset($maquina->writeups) && $maquina->writeups->isNotEmpty())
                        <ul style="margin:0; padding-left:18px; display:grid; gap:6px;">
                            @foreach ($maquina->writeups as $w)
                                <li>
                                    <strong @if($w->user) style="color:#DAA520; font-weight:700" @endif>
                                        {{ $w->autor }}:
                                    </strong>
                                    <a href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                                        {{ \Illuminate\Support\Str::limit($w->enlace, 80) }}
                                    </a>
                                    <small style="opacity:.7;"> — {{ $w->created_at->format('Y-m-d') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No hay writeups aprobados todavía.</p>
                    @endif
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
    const open = (el) => { el?.classList.add('open'); el?.setAttribute('aria-hidden', 'false'); };
    const close = (el) => { el?.classList.remove('open'); el?.setAttribute('aria-hidden', 'true'); };

    document.addEventListener('click', (e) => {
        /* ===== Nuevo: click en nombre => Presentación ===== */
        if (e.target.closest('.open-presentacion')) {
            const id = e.target.closest('.open-presentacion').getAttribute('data-target');
            open(document.getElementById(id));
            return;
        }

        /* ==== Botones/acciones existentes ==== */
        if (e.target.closest('.open-desc')) {
            const id = e.target.closest('.open-desc').getAttribute('data-target');
            open(document.getElementById(id));
            return;
        }
        if (e.target.closest('.open-upload')) {
            const id = e.target.closest('.open-upload').getAttribute('data-target');
            open(document.getElementById(id));
            return;
        }
        if (e.target.closest('.open-book')) {
            const id = e.target.closest('.open-book').getAttribute('data-target');
            open(document.getElementById(id));
            return;
        }
        if (e.target.closest('.open-ranking-jugadores')) {
            const id = e.target.closest('.open-ranking-jugadores').getAttribute('data-target');
            open(document.getElementById(id));
            return;
        }
        if (e.target.closest('.open-ranking-creadores')) {
            const id = e.target.closest('.open-ranking-creadores').getAttribute('data-target');
            open(document.getElementById(id));
            return;
        }

        if (e.target.matches('.modal-close')) {
            close(e.target.closest('.modal'));
        } else if (e.target.classList && e.target.classList.contains('modal')) {
            close(e.target);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.open').forEach(close);
        }
    });
})();
</script>
@endsection

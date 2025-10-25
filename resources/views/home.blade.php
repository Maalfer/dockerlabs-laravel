@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<style>
  /* ===== ENHANCED TOP BAR ===== */
  .top-bar{
    display:flex; align-items:center; justify-content:space-between;
    gap:1.5rem; padding:1.5rem 0 2rem;
    flex-wrap:wrap;
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
  }

  .top-bar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--brand-500), var(--brand-300), var(--brand-500));
  }

  .rank-actions{
    display:flex; gap:1rem; align-items:center; flex-wrap:wrap;
  }

  .btn-rank{
    display:inline-flex; align-items:center; gap:.75rem;
    padding:1rem 1.5rem; border-radius:12px;
    background: linear-gradient(135deg, var(--bg-elev), var(--surface));
    color: var(--text);
    border: 2px solid var(--border);
    font-size:1rem; font-weight:600; line-height:1.2; text-decoration:none;
    transition: all var(--dur-med) var(--ease);
    cursor:pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .btn-rank:hover{ 
    transform: translateY(-3px); 
    box-shadow: 0 8px 25px rgba(31,94,215,0.2);
    border-color: var(--brand-300);
    background: linear-gradient(135deg, var(--surface), var(--bg-elev));
  }

  /* ===== ENHANCED FILTERS BAR ===== */
  .filters-bar {
    display: flex; flex-wrap: wrap; gap: 1rem;
    align-items: center; justify-content: flex-end;
    min-width: 320px;
  }

  .chip {
    --ring: transparent;
    display: inline-flex; align-items: center; gap: .75rem;
    padding: 1rem 1.5rem; border-radius: 50px;
    background: linear-gradient(135deg, var(--bg-elev), var(--surface));
    color: var(--text);
    border: 2px solid var(--border);
    font-size: 1rem; font-weight: 600; line-height: 1.2;
    transition: all var(--dur-med) var(--ease);
    text-decoration: none; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .chip:hover{
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: rgba(255,255,255,.2);
  }

  .chip.active{
    transform: translateY(-2px);
    box-shadow: 0 6px 20px var(--ring-glow, rgba(31,94,215,0.3));
    border-color: var(--ring);
    background: linear-gradient(135deg, var(--active-bg), var(--surface));
  }

  .chip-cyan{  
    --ring:#06b6d4; 
    --ring-glow: rgba(6,182,212,0.3);
    --active-bg: rgba(6,182,212,0.15);
    color:#22d3ee; 
  }

  .chip-green{ 
    --ring:#10b981; 
    --ring-glow: rgba(16,185,129,0.3);
    --active-bg: rgba(16,185,129,0.15);
    color:#34d399; 
  }

  .chip-amber{ 
    --ring:#f59e0b; 
    --ring-glow: rgba(245,158,11,0.3);
    --active-bg: rgba(245,158,11,0.15);
    color:#fbbf24; 
  }

  .chip-red{   
    --ring:#ef4444; 
    --ring-glow: rgba(239,68,68,0.3);
    --active-bg: rgba(239,68,68,0.15);
    color:#f87171; 
  }

  .no-results{ 
    margin:2rem 0; 
    opacity:.8; 
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 16px;
    font-size: 1.1rem;
  }


  /* ===== ENHANCED MACHINES GRID ===== */
.machines-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 columnas fijas */
    gap: 1.5rem;
    padding: 1rem 0;
}

.machine-card {
    display: flex;
    flex-direction: column;
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 2px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    min-height: 120px;
    position: relative;
    overflow: hidden;
    transition: all var(--dur-med) var(--ease);
}

.machine-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(31,94,215,0.15);
    border-color: rgba(138,180,248,.3);
}

.machine-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 0.75rem;
}

.machine-name {
    font-weight: 700;
    font-size: 1.1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    background: linear-gradient(135deg, var(--text), var(--brand-200));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    transition: all var(--dur-fast) var(--ease);
    flex: 1;
    min-width: 0;
}

.machine-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

/* Ajustar badges para que sean más compactos */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 50px;
    border: 2px solid transparent;
    user-select: none;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all var(--dur-fast) var(--ease);
    white-space: nowrap;
}

/* Botones más compactos */
.btn-xs {
    padding: 8px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 2px solid transparent;
    transition: all var(--dur-fast) var(--ease);
    white-space: nowrap;
}

.btn-icon {
    background: transparent;
    border: 2px solid var(--border);
    border-radius: 10px;
    padding: 8px 10px;
    color: var(--text);
    cursor: pointer;
    transition: all var(--dur-fast) var(--ease);
    min-width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-icon i {
    font-size: 0.9rem;
}

/* Responsive para tablets */
@media (max-width: 1024px) {
    .machines-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

/* Responsive para móviles */
@media (max-width: 768px) {
    .machine-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .machine-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .machine-name {
        text-align: left;
        width: 100%;
    }
}




  /* ===== ENHANCED BADGES ===== */
  .badge{
    display:inline-flex; align-items:center; gap:8px;
    font-size:.9rem; font-weight:800; 
    padding:8px 16px; border-radius:50px;
    border:2px solid transparent; user-select:none; 
    letter-spacing:.3px; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all var(--dur-fast) var(--ease);
  }

  .badge--easy{
    background: linear-gradient(135deg, #10b981, #059669);
    border-color:#047857; color:#fff;
    box-shadow: 0 4px 15px rgba(16,185,129,0.3);
  }

  .badge--medium{
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-color:#b45309; color:#fff;
    box-shadow: 0 4px 15px rgba(245,158,11,0.3);
  }

  .badge--hard{
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border-color:#b91c1c; color:#fff;
    box-shadow: 0 4px 15px rgba(239,68,68,0.3);
  }

  .badge--unknown{
    background: linear-gradient(135deg, #6b7280, #4b5563);
    border-color:#374151; color:#fff;
  }

  .badge::after{
    content:""; width:8px; height:8px; border-radius:50%;
    background:rgba(255,255,255,.8); opacity:.6;
  }

  /* ===== ENHANCED BUTTONS ===== */
  .btn-xs{
    padding:10px 16px; font-size:.9rem; font-weight:600;
    border: 2px solid transparent;
    transition: all var(--dur-fast) var(--ease);
  }

  .btn-outline{
    background: transparent;
    border: 2px solid rgba(138,180,248,.4);
    color: var(--brand-300);
  }

  .btn-outline:hover{
    background: rgba(77,130,234,.15);
    border-color: var(--brand-300);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(77,130,234,0.2);
  }

  .btn-icon{
    background: transparent;
    border: 2px solid var(--border);
    border-radius:12px; 
    padding:10px 14px; 
    color:var(--text); 
    cursor:pointer; 
    transition: all var(--dur-fast) var(--ease);
  }

  .btn-icon:hover{
    background:rgba(77,130,234,.15);
    border-color: var(--brand-300);
    transform:translateY(-2px);
    box-shadow: 0 6px 20px rgba(77,130,234,0.15);
  }

  .btn-icon i{ font-size:1rem; }

  .btn-primary{
    background: var(--grad-2);
    border: 2px solid rgba(138,180,248,.5);
    box-shadow: 0 6px 20px rgba(31,94,215,0.25);
  }

  .btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 30px rgba(31,94,215,0.35);
    filter:brightness(1.1);
  }

  /* ===== ENHANCED TABLES ===== */
  .table{ 
    width:100%; 
    border-collapse:separate; 
    border-spacing:0 12px; 
  }

  .table thead th{ 
    text-align:left; 
    font-weight:700; 
    opacity:.9; 
    padding:1rem 1.5rem; 
    background: linear-gradient(135deg, var(--bg-deep), var(--bg-elev));
    color: var(--brand-200);
    border-bottom: 3px solid var(--border);
  }

  .table tbody tr{
    background: linear-gradient(135deg, var(--surface), var(--surface-2)); 
    border: 2px solid var(--border);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all var(--dur-fast) var(--ease);
  }

  .table tbody tr:hover{
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(31,94,215,0.15);
    border-color: rgba(138,180,248,.3);
  }

  .table tbody td{ 
    padding:1.25rem 1.5rem; 
    vertical-align:middle; 
    color: var(--text);
  }

  .rank-medal{ 
    font-weight:800; 
    font-size:1.1rem;
    color: var(--brand-300);
  }

  /* ===== ENHANCED MODALS ===== */
  .modal-card{
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 2px solid var(--border);
    border-radius: 24px;
    box-shadow: 0 30px 60px rgba(2,6,23,.8);
  }

  .modal-header{
    background: linear-gradient(135deg, var(--bg-deep), var(--bg-elev));
    border-bottom: 2px solid var(--border);
    padding: 1.5rem 2rem;
  }

  .modal-title{
    font-size:1.3rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text), var(--brand-200));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }

  .modal-body{
    padding: 2rem;
    line-height: 1.7;
  }

  .modal-footer{
    background: linear-gradient(135deg, var(--bg-deep), var(--bg-elev));
    border-top: 2px solid var(--border);
    padding: 1.5rem 2rem;
  }

  .modal-close{
    border: 2px solid var(--border);
    border-radius: 12px;
    transition: all var(--dur-fast) var(--ease);
  }

  .modal-close:hover{
    background:rgba(239,68,68,0.15);
    border-color: #ef4444;
    transform:translateY(-1px);
  }

  /* ===== ENHANCED FORM ELEMENTS ===== */
  .form-control{
    background: var(--bg-deep);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    transition: all var(--dur-fast) var(--ease);
  }

  .form-control:focus{
    border-color:var(--brand-300);
    box-shadow:0 0 0 4px rgba(138,180,248,.2);
    background:var(--bg-elev);
  }

  .alert{
    padding:1.25rem 1.5rem;
    border-radius:16px;
    border: 2px solid transparent;
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    color:var(--text);
    box-shadow: var(--shadow);
  }

  /* ===== RESPONSIVE DESIGN ===== */
  @media (max-width: 1024px) {
    .top-bar {
      flex-direction: column;
      align-items: stretch;
      gap: 1.5rem;
    }

    .filters-bar {
      justify-content: center;
    }

    .rank-actions {
      justify-content: center;
    }
  }

  @media (max-width: 768px) {
    .top-bar {
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .machine-row {
      grid-template-columns: 1fr;
      gap: 1rem;
      padding: 1.25rem;
    }

    .machine-main {
      justify-content: center;
      text-align: center;
    }

    .btn-rank, .chip {
      padding: 0.875rem 1.25rem;
      font-size: 0.9rem;
    }

    .machines-grid {
      gap: 1rem;
    }
  }

  @media (max-width: 640px) {
    .filters-bar {
      flex-direction: column;
      align-items: center;
    }

    .chip {
      width: 200px;
      justify-content: center;
    }

    .machine-name {
      font-size: 1.1rem;
    }
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

{{-- Los modals y el resto del contenido se mantiene EXACTAMENTE igual --}}
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

        {{-- Los modals individuales de cada máquina se mantienen igual --}}
        {{-- ===== MODAL: PRESENTACIÓN ===== --}}
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

        {{-- ===== MODAL: DESCRIPCIÓN ===== --}}
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
// El JavaScript se mantiene EXACTAMENTE igual
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
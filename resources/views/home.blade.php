@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

@php $f = $filtroDificultad ?? null; @endphp

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

<div id="ranking-jugadores" class="modal ranking-modal" role="dialog" aria-modal="true" aria-labelledby="ranking-jugadores-title" aria-hidden="true">
  <div class="modal-card" role="document">
    <header class="modal-header">
      <h3 id="ranking-jugadores-title" class="modal-title">🏆 Ranking de Jugadores</h3>
      <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
    </header>
    <div class="modal-body">
      @if(($rankingJugadores ?? collect())->isNotEmpty())
        <table class="ranking-table">
          <thead>
            <tr>
              <th>Puesto</th>
              <th>Jugador</th>
              <th>Puntos</th>
              <th>Writeups aprobados</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rankingJugadores as $i => $row)
              @php $pos = $i + 1; @endphp
              <tr class="rank-row rank-{{ $pos <= 3 ? $pos : '' }}">
                <td class="rank-medal">
                  <span class="medal">
                    <span class="ico">
                      @if($pos === 1) 🥇 @elseif($pos === 2) 🥈 @elseif($pos === 3) 🥉 @else # @endif
                    </span>
                    <span class="pos">{{ $pos }}</span>
                  </span>
                </td>
                <td>
                  <div class="player">
                    <span class="name">{{ $row->nombre }} @if($pos===1)<span class="crown">👑</span>@endif</span>
                  </div>
                </td>
                <td class="cell-num">{{ number_format($row->puntos) }}</td>
                <td class="cell-num"><span class="badge badge-muted">{{ $row->total_writeups }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p style="padding:1rem 1.25rem;">No hay datos suficientes para generar el ranking.</p>
      @endif
    </div>
    <footer class="modal-footer">
      <button class="btn btn-xs modal-close" type="button">Cerrar</button>
    </footer>
  </div>
</div>

<div id="ranking-creadores" class="modal ranking-modal" role="dialog" aria-modal="true" aria-labelledby="ranking-creadores-title" aria-hidden="true">
  <div class="modal-card" role="document">
    <header class="modal-header">
      <h3 id="ranking-creadores-title" class="modal-title">🧩 Ranking de Creadores</h3>
      <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
    </header>
    <div class="modal-body">
      @if(($rankingCreadores ?? collect())->isNotEmpty())
        <table class="ranking-table">
          <thead>
            <tr>
              <th>Puesto</th>
              <th>Creador</th>
              <th>Máquinas aprobadas</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rankingCreadores as $i => $row)
              @php $pos = $i + 1; @endphp
              <tr class="rank-row rank-{{ $pos <= 3 ? $pos : '' }}">
                <td class="rank-medal">
                  <span class="medal">
                    <span class="ico">
                      @if($pos === 1) 🥇 @elseif($pos === 2) 🥈 @elseif($pos === 3) 🥉 @else # @endif
                    </span>
                    <span class="pos">{{ $pos }}</span>
                  </span>
                </td>
                <td>
                  <div class="player">
                    <span class="name">{{ $row->nombre }} @if($pos===1)<span class="crown">👑</span>@endif</span>
                  </div>
                </td>
                <td class="cell-num">{{ number_format($row->total_maquinas) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p style="padding:1rem 1.25rem;">No hay datos suficientes para generar el ranking.</p>
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
                      @if(!empty($maquina->imagen_url))
                        <img src="{{ $maquina->imagen_url }}" alt="Imagen de {{ $maquina->nombre }}"
                             style="width:100%; height:auto; display:block; border-radius:12px;">
                      @else
                        <img src="{{ asset('images/logo.png') }}" alt="Presentación máquina"
                             style="width:100%; height:auto; display:block; border-radius:12px;">
                      @endif
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
        if (e.target.closest('.open-presentacion')) {
            const id = e.target.closest('.open-presentacion').getAttribute('data-target');
            open(document.getElementById(id));
            return;
        }
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

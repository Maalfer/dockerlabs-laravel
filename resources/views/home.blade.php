@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="machines-grid">
        @foreach ($maquinas as $maquina)
            <article class="machine-row">
                <div class="machine-main">
                    <span class="machine-name">{{ $maquina->nombre }}</span>

                    <span class="badge {{ $maquina->dificultad_clase }}">
                        {{ $maquina->dificultad_etiqueta }}
                    </span>

                    <!-- Bot�n de descripci�n (abre modal) -->
                    <button
                        class="btn btn-xs btn-outline open-desc"
                        type="button"
                        data-target="desc-{{ $maquina->id ?? $loop->index }}"
                        aria-haspopup="dialog"
                        aria-controls="desc-{{ $maquina->id ?? $loop->index }}"
                    >
                        Descripci�n
                    </button>

                    <!-- Botones de acci�n SOLO con iconos -->
                    <button
                        class="btn btn-xs btn-icon open-upload"
                        type="button"
                        title="Subir writeup"
                        data-target="upload-{{ $maquina->id ?? $loop->index }}"
                        aria-haspopup="dialog"
                        aria-controls="upload-{{ $maquina->id ?? $loop->index }}"
                    >
                        <i class="fas fa-upload" aria-hidden="true"></i>
                    </button>

                    <!-- Bot�n de Descargar: ahora vinculado al enlace de descarga -->
                    @if($maquina->enlace_descarga)
                        <a href="{{ $maquina->enlace_descarga }}" class="btn btn-xs btn-icon" target="_blank" title="Descargar">
                            <i class="fas fa-download" aria-hidden="true"></i>
                        </a>
                    @else
                        <button class="btn btn-xs btn-icon" type="button" title="Descargar" disabled>
                            <i class="fas fa-download" aria-hidden="true"></i>
                        </button>
                    @endif

                    <!-- Libro: abrir modal con writeups aprobados -->
                    <button
                        class="btn btn-xs btn-icon open-book"
                        type="button"
                        title="Ver writeups aprobados"
                        data-target="book-{{ $maquina->id ?? $loop->index }}"
                        aria-haspopup="dialog"
                        aria-controls="book-{{ $maquina->id ?? $loop->index }}"
                    >
                        <i class="fas fa-book" aria-hidden="true"></i>
                    </button>
                </div>
            </article>

            <!-- Modal de descripci�n (uno por m�quina) -->
            <div
                id="desc-{{ $maquina->id ?? $loop->index }}"
                class="modal"
                role="dialog"
                aria-modal="true"
                aria-labelledby="desc-title-{{ $maquina->id ?? $loop->index }}"
                aria-hidden="true"
            >
                <div class="modal-card" role="document">
                    <header class="modal-header">
                        <h3 id="desc-title-{{ $maquina->id ?? $loop->index }}" class="modal-title">
                            {{ $maquina->nombre }} \u2014 Descripci�n
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

            <!-- Modal de Upload (uno por m�quina) -->
            <div
                id="upload-{{ $maquina->id ?? $loop->index }}"
                class="modal"
                role="dialog"
                aria-modal="true"
                aria-labelledby="upload-title-{{ $maquina->id ?? $loop->index }}"
                aria-hidden="true"
            >
                <div class="modal-card" role="document">
                    <header class="modal-header">
                        <h3 id="upload-title-{{ $maquina->id ?? $loop->index }}" class="modal-title">
                            {{ $maquina->nombre }} \u2014 Enviar writeup
                        </h3>
                        <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
                    </header>

                    <form method="POST" action="{{ route('writeups-temporal.store') }}">
                        @csrf
                        <input type="hidden" name="maquina_id" value="{{ $maquina->id }}">

                        <div class="modal-body">
                            <div class="form-row" style="display:grid; gap:10px;">
                                <label style="display:grid; gap:6px;">
                                    Autor
                                    <input
                                        type="text"
                                        name="autor"
                                        class="form-control"
                                        required
                                        maxlength="120"
                                        placeholder="Tu nombre o alias">
                                </label>

                                <label style="display:grid; gap:6px;">
                                    Enlace
                                    <input
                                        type="url"
                                        name="enlace"
                                        class="form-control"
                                        required
                                        placeholder="https://...">
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

            <!-- Modal del Libro (writeups aprobados, uno por m�quina) -->
            <div
                id="book-{{ $maquina->id ?? $loop->index }}"
                class="modal"
                role="dialog"
                aria-modal="true"
                aria-labelledby="book-title-{{ $maquina->id ?? $loop->index }}"
                aria-hidden="true"
            >
                <div class="modal-card" role="document">
                    <header class="modal-header">
                        <h3 id="book-title-{{ $maquina->id ?? $loop->index }}" class="modal-title">
                            {{ $maquina->nombre }} \u2014 Writeups aprobados
                        </h3>
                        <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
                    </header>

                    <div class="modal-body">
                        @if(isset($maquina->writeups) && $maquina->writeups->isNotEmpty())
                            <ul style="margin:0; padding-left:18px; display:grid; gap:6px;">
                                @foreach ($maquina->writeups as $w)
                                    <li>
                                        <strong>{{ $w->autor }}:</strong>
                                        <a href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                                            {{ \Illuminate\Support\Str::limit($w->enlace, 80) }}
                                        </a>
                                        <small style="opacity:.7;"> \u2014 {{ $w->created_at->format('Y-m-d') }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>No hay writeups aprobados todav�a.</p>
                        @endif
                    </div>

                    <footer class="modal-footer">
                        <button class="btn btn-xs modal-close" type="button">Cerrar</button>
                    </footer>
                </div>
            </div>
        @endforeach
    </div>

    <!-- JS m�nimo para abrir/cerrar modales -->
    <script>
      (function () {
        const open = (el) => { el?.classList.add('open'); el?.setAttribute('aria-hidden', 'false'); };
        const close = (el) => { el?.classList.remove('open'); el?.setAttribute('aria-hidden', 'true'); };

        document.addEventListener('click', (e) => {
          // Abrir modal de descripci�n
          if (e.target.closest('.open-desc')) {
            const btn = e.target.closest('.open-desc');
            const id = btn.getAttribute('data-target');
            const modal = document.getElementById(id);
            open(modal);
          }

          // Abrir modal de upload
          if (e.target.closest('.open-upload')) {
            const btn = e.target.closest('.open-upload');
            const id = btn.getAttribute('data-target');
            const modal = document.getElementById(id);
            open(modal);
          }

          // Abrir modal del libro (writeups aprobados)
          if (e.target.closest('.open-book')) {
            const btn = e.target.closest('.open-book');
            const id = btn.getAttribute('data-target');
            const modal = document.getElementById(id);
            open(modal);
          }

          // Cerrar (bot�n cerrar o clic en overlay)
          if (e.target.matches('.modal-close') || (e.target.matches('.modal') && !e.target.querySelector('.modal-card:hover'))) {
            const modal = e.target.closest('.modal');
            close(modal);
          }
        });

        // Cerrar con ESC
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') {
            document.querySelectorAll('.modal.open').forEach(close);
          }
        });
      })();
    </script>
@endsection

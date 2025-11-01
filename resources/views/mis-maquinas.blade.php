@extends('layouts.app')

@section('title', 'Tus máquinas aprobadas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<div class="admin-page container">
    <div class="submit-machine-header" style="margin-bottom:1.25rem;">
        <h2 class="submit-machine-title" style="margin-bottom:.35rem;">🧩 Tus máquinas aprobadas</h2>
        <p class="submit-machine-subtitle">
            Aquí aparecen las máquinas que has creado y han sido aprobadas por el equipo.
        </p>
    </div>

    @if ($maquinas->isEmpty())
        <div class="alert alert-info" role="status">
            <div class="alert-icon">ℹ️</div>
            <div class="alert-content">Aún no tienes máquinas aprobadas.</div>
        </div>
        <a href="{{ route('dockerlabs.home') }}" class="btn btn-outline">← Volver al inicio</a>
    @else
        <div class="machines-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
            @foreach ($maquinas as $maquina)
                <article class="card" style="background:var(--surface); border:1px solid var(--border); border-radius:16px; overflow:hidden; box-shadow:var(--shadow); display:flex; flex-direction:column;">
                    <div style="aspect-ratio:16/9; background:var(--surface-2); display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        @php $img = $maquina->imagen_url ?? asset('images/logo.png'); @endphp
                        <img src="{{ $img }}" alt="Imagen de {{ $maquina->nombre }}" style="width:100%; height:100%; object-fit:cover;">
                    </div>

                    <div style="padding:1rem; display:grid; gap:.5rem; flex:1;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:.5rem;">
                            <h3 style="margin:0; font-size:1.05rem; font-weight:800;">{{ $maquina->nombre }}</h3>
                            <span class="badge {{ $maquina->dificultad_clase }}">{{ $maquina->dificultad_etiqueta }}</span>
                        </div>

                        <p class="muted" style="margin:0; color:var(--muted); line-height:1.45;">
                            {{ \Illuminate\Support\Str::limit($maquina->descripcion, 140) }}
                        </p>

                        <div style="display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; margin-top:.25rem;">
                            <button type="button"
                                    class="btn btn-xs btn-outline"
                                    onclick="openMachineModal('pres-{{ $maquina->id }}')">
                                Presentación
                            </button>

                            <button type="button"
                                    class="btn btn-xs btn-primary"
                                    onclick="openMachineModal('edit-{{ $maquina->id }}')">
                                Editar
                            </button>

                            @if($maquina->enlace_descarga)
                                <a href="{{ $maquina->enlace_descarga }}" target="_blank" rel="noopener" class="btn btn-xs btn-success">
                                    Descargar
                                </a>
                            @endif
                        </div>

                        <small class="muted" style="opacity:.8;">Creada: {{ optional($maquina->created_at)->format('Y-m-d') }}</small>
                    </div>
                </article>

                <div id="pres-{{ $maquina->id }}" class="modal" role="dialog"
                     aria-modal="true" aria-labelledby="pres-title-{{ $maquina->id }}" aria-hidden="true">
                    <div class="modal-card" role="document">
                        <header class="modal-header">
                            <h3 id="pres-title-{{ $maquina->id }}" class="modal-title">
                                {{ $maquina->nombre }} — Presentación
                            </h3>
                            <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
                        </header>
                        <div class="modal-body">
                          <div style="display:grid; grid-template-columns: 240px 1fr; gap:16px; align-items:start;">
                            <div>
                              <img src="{{ $maquina->imagen_url ?? asset('images/logo.png') }}"
                                   alt="Imagen de {{ $maquina->nombre }}"
                                   style="width:100%; height:auto; display:block; border-radius:12px;">
                            </div>
                            <div style="display:grid; gap:12px;">
                              <dl style="display:grid; grid-template-columns: 140px 1fr; gap:8px 12px; align-items:center;">
                                <dt style="color:var(--muted,#a8b8d6);">Autor</dt>
                                <dd>{{ $maquina->autor ?? '—' }}</dd>
                                <dt style="color:var(--muted,#a8b8d6);">Creación</dt>
                                <dd>{{ optional($maquina->created_at)->format('Y-m-d') }}</dd>
                              </dl>
                              <div>
                                <strong>Descripción</strong>
                                <p style="margin:.25rem 0 0;">{{ $maquina->descripcion }}</p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <footer class="modal-footer">
                            <button class="btn btn-xs modal-close" type="button">Cerrar</button>
                        </footer>
                    </div>
                </div>

                <div id="edit-{{ $maquina->id }}" class="modal" role="dialog"
                     aria-modal="true" aria-labelledby="edit-title-{{ $maquina->id }}" aria-hidden="true">
                    <div class="modal-card" role="document">
                        <header class="modal-header">
                            <h3 id="edit-title-{{ $maquina->id }}" class="modal-title">
                                Editar — {{ $maquina->nombre }}
                            </h3>
                            <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
                        </header>
                        <form action="{{ route('dockerlabs.mis-maquinas.solicitar-edicion', $maquina->id) }}" method="POST">
                            @csrf
                            <div class="modal-body" style="display:grid; gap:12px;">
                                <div>
                                    <label class="label">Nombre</label>
                                    <input type="text" name="nombre" value="{{ old('nombre', $maquina->nombre) }}" class="input" required>
                                </div>
                                <div>
                                    <label class="label">Descripción</label>
                                    <textarea name="descripcion" rows="5" class="textarea" required>{{ old('descripcion', $maquina->descripcion) }}</textarea>
                                </div>
                                <div>
                                    <label class="label">Dificultad</label>
                                    <select name="dificultad" class="input" required>
                                        @php $difs = ['muy-facil'=>'Muy fácil','facil'=>'Fácil','medio'=>'Medio','dificil'=>'Difícil']; @endphp
                                        @foreach($difs as $val=>$txt)
                                            <option value="{{ $val }}" @if(old('dificultad', \Illuminate\Support\Str::slug($maquina->dificultad))==$val) selected @endif>{{ $txt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="label">Enlace de descarga (opcional)</label>
                                    <input type="url" name="enlace_descarga" value="{{ old('enlace_descarga', $maquina->enlace_descarga) }}" class="input" placeholder="https://...">
                                </div>
                                <div>
                                    <label class="label">Comentario para el admin (opcional)</label>
                                    <input type="text" name="comentario" value="{{ old('comentario') }}" class="input" maxlength="500">
                                </div>
                            </div>
                            <footer class="modal-footer">
                                <button class="btn btn-xs modal-close" type="button">Cancelar</button>
                                <button type="submit" class="btn btn-xs btn-primary">Enviar</button>
                            </footer>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:1rem;">
            {{ $maquinas->links() }}
        </div>

        <div style="margin-top:1rem;">
            <a href="{{ url('/perfil') }}" class="btn btn-outline">← Volver a tu perfil</a>
        </div>
    @endif
</div>

<script>
function openMachineModal(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.add('open');
    el.setAttribute('aria-hidden','false');
}
document.addEventListener('click', (e) => {
    if (e.target.matches('.modal-close')) {
        const m = e.target.closest('.modal');
        m?.classList.remove('open');
        m?.setAttribute('aria-hidden','true');
    } else if (e.target.classList && e.target.classList.contains('modal')) {
        e.target.classList.remove('open');
        e.target.setAttribute('aria-hidden','true');
    }
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.open').forEach(m => {
            m.classList.remove('open'); m.setAttribute('aria-hidden','true');
        });
    }
});
</script>
@endsection

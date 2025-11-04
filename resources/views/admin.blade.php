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

            <form action="{{ route('admin.maquinas.store') }}" method="POST" class="form" enctype="multipart/form-data">
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

                @php($difOld = old('dificultad', $prefill['dificultad'] ?? 'medio'))
                <div class="form-row">
                    <label for="dificultad">Dificultad</label>
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
                    <label for="autor_email">Autor Email</label>
                    <input type="email" id="autor_email" name="autor_email" class="form-control"
                           value="{{ old('autor_email', $prefill['autor_email'] ?? ($prefill['autor_url'] ?? '')) }}">
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

                <div class="form-row">
                    <label for="imagen">Imagen de la máquina</label>
                    @if(!empty($prefill['imagen_path']))
                        <div style="margin-bottom:10px; max-width:320px;">
                            <img src="{{ asset('storage/'.$prefill['imagen_path']) }}" alt="Imagen pre-cargada" style="width:100%; height:auto; border-radius:10px;">
                        </div>
                        <input type="hidden" name="imagen_path_prefill" value="{{ $prefill['imagen_path'] }}">
                    @endif
                    <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*">
                    <small class="form-hint">Si subes una imagen aquí, sustituirá a la recibida.</small>
                </div>

                @php($destOld = old('destino', 'principal'))
                <div class="form-row">
                    <label for="destino">Guardar en</label>
                    <select id="destino" name="destino" class="form-control" required>
                        <option value="principal" {{ $destOld==='principal' ? 'selected' : '' }}>Tabla principal (maquinas)</option>
                        <option value="bunker" {{ $destOld==='bunker' ? 'selected' : '' }}>Bunker (maquinas_bunkerlabs)</option>
                    </select>
                    <small class="form-hint">Elige dónde se guardará esta máquina.</small>
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

            <hr class="divider">

            <h2>Máquinas BunkerLabs</h2>

            @if(isset($maquinasBunker) && $maquinasBunker->count())
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Nombre</th>
                                <th style="width:160px;">Dificultad</th>
                                <th>Descripción</th>
                                <th style="width:240px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($maquinasBunker as $maq)
                                <tr>
                                    <td>#{{ $maq->id }}</td>
                                    <td>{{ $maq->nombre }}</td>
                                    <td>
                                        <span class="badge {{ $maq->dificultad_clase }}">
                                            {{ $maq->dificultad_etiqueta }}
                                        </span>
                                    </td>
                                    <td>{{ $maq->descripcion }}</td>
                                    <td>
                                        @if($maq->enlace_descarga)
                                            <a href="{{ filter_var($maq->enlace_descarga, FILTER_VALIDATE_URL) ? $maq->enlace_descarga : '#' }}"
                                               class="btn btn-success" target="_blank" rel="noopener">
                                                Descargar
                                            </a>
                                        @endif
                                        @if(!empty($maq->imagen_url))
                                            <a href="{{ $maq->imagen_url }}" class="btn" target="_blank" rel="noopener">
                                                Imagen
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.bunker.maquinas.destroy', $maq) }}"
                                              method="POST"
                                              style="display:inline-block; margin-left:.25rem;"
                                              onsubmit="return confirm('¿Seguro que quieres eliminar la máquina Bunker «{{ $maq->nombre }}»?');">
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
                <p class="muted">No hay máquinas BunkerLabs registradas todavía.</p>
            @endif
        </div>
    </div>

    @if(auth()->check() && (method_exists(auth()->user(), 'isAdmin') ? auth()->user()->isAdmin() : (auth()->user()->role ?? null) === 'admin'))
    <div id="modal-tokens"
         style="position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,.6); z-index:9999;">
        <div style="width:clamp(320px, 92vw, 760px); background:#0f172a; color:#fff; border:1px solid #334155; border-radius:12px; overflow:hidden;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:.75rem 1rem; border-bottom:1px solid #334155;">
                <strong>Tokens Bunkerlabs</strong>
                <button id="btn-cerrar-modal" type="button"
                        style="background:transparent; color:#fff; border:0; font-size:1.25rem; cursor:pointer;">×</button>
            </div>

            <div style="padding:1rem;">
                <form id="form-crear-token" style="display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem;">
                    <input type="text" name="name" placeholder="Etiqueta (opcional)" style="padding:.5rem; flex:1; min-width:220px;">
                    <button type="submit"
                            style="padding:.5rem .9rem; background:#1f2937; color:#fff; border:0; border-radius:8px; cursor:pointer;">
                        Crear token
                    </button>
                </form>

                <div id="nuevo-token"
                     style="display:none; background:#064e3b; color:#ecfdf5; border:1px solid #10b981; border-radius:8px; padding:.6rem .75rem; margin-bottom:1rem;">
                </div>

                <div style="overflow:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:640px;">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155;">ID</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155;">Nombre</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155;">Activo</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155;">Creado</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tokens-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
(function(){
  const btnOpen = document.getElementById('btn-gestionar-tokens');
  const modal   = document.getElementById('modal-tokens');
  if(!btnOpen || !modal) return;

  const btnClose= document.getElementById('btn-cerrar-modal');
  const tbody   = document.getElementById('tokens-tbody');
  const form    = document.getElementById('form-crear-token');
  const msgNew  = document.getElementById('nuevo-token');
  const csrfTag = document.querySelector('meta[name="csrf-token"]');
  const csrf    = csrfTag ? csrfTag.getAttribute('content') : '';

  function show(){ modal.style.display = 'flex'; loadTokens(); }
  function hide(){ modal.style.display = 'none'; msgNew.style.display = 'none'; msgNew.textContent=''; }

  btnOpen.addEventListener('click', show);
  btnClose.addEventListener('click', hide);
  modal.addEventListener('click', (e)=>{ if(e.target === modal) hide(); });

  async function loadTokens(){
    tbody.innerHTML = '<tr><td colspan="5" style="padding:.75rem; color:#9ca3af;">Cargando...</td></tr>';
    try{
      const res  = await fetch(`{{ route('admin.bunker.tokens.index') }}`, { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      if(!Array.isArray(data) || data.length === 0){
        tbody.innerHTML = '<tr><td colspan="5" style="padding:.75rem; color:#9ca3af;">No hay tokens creados.</td></tr>';
        return;
      }
      tbody.innerHTML = '';
      for(const t of data){
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td style="padding:.5rem;">${t.id}</td>
          <td style="padding:.5rem;">${t.name ?? ''}</td>
          <td style="padding:.5rem;">${t.active ? 'Sí' : 'No'}</td>
          <td style="padding:.5rem;">${new Date(t.created_at).toLocaleString()}</td>
          <td style="padding:.5rem;">
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
              <button data-id="${t.id}" data-action="toggle"
                      style="padding:.35rem .65rem; border-radius:6px; background:#1e293b; color:#fff; border:0; cursor:pointer;">
                ${t.active ? 'Desactivar' : 'Activar'}
              </button>
              <button data-id="${t.id}" data-action="delete"
                      style="padding:.35rem .65rem; border-radius:6px; background:#3b0d16; color:#fca5a5; border:1px solid #7a1631; cursor:pointer;">
                Eliminar
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(tr);
      }
    }catch(e){
      tbody.innerHTML = '<tr><td colspan="5" style="padding:.75rem; color:#fca5a5;">Error cargando tokens.</td></tr>';
    }
  }

  tbody.addEventListener('click', async (e)=>{
    const btn = e.target.closest('button[data-action]');
    if(!btn) return;
    const id = btn.getAttribute('data-id');
    const action = btn.getAttribute('data-action');
    try{
      if(action === 'toggle'){
        await fetch(`{{ url('/admin/bunkerlabs/tokens') }}/${id}/toggle`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });
        loadTokens();
      }else if(action === 'delete'){
        if(!confirm('¿Eliminar token?')) return;
        await fetch(`{{ url('/admin/bunkerlabs/tokens') }}/${id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });
        loadTokens();
      }
    }catch(e){
      alert('Ocurrió un error realizando la acción.');
    }
  });

  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(form);
    try{
      const res = await fetch(`{{ route('admin.bunker.tokens.store') }}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: fd
      });
      const data = await res.json();
      if(data && data.ok && data.plain){
        msgNew.style.display = 'block';
        msgNew.textContent = 'Nuevo token (cópialo ahora): ' + data.plain;
        form.reset();
        loadTokens();
      }else{
        alert('No se pudo crear el token.');
      }
    }catch(e){
      alert('Error al crear el token.');
    }
  });
})();
</script>
@endpush

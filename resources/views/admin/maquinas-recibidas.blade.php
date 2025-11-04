@extends('layouts.app')

@section('title', 'Máquinas recibidas')

@section('content')
<div class="admin-page container">
    <h2>Máquinas recibidas</h2>

    @if ($maquinas->isEmpty())
        <p>No se han recibido máquinas todavía.</p>
    @else
        <table class="table" style="width:100%; border-collapse:collapse; margin-top:20px;">
            <thead>
                <tr>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">ID</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Nombre</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Dificultad</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Autor</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Fecha creación</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Writeup</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Enlace de descarga</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Imagen</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Enviado</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Destino</th> {{-- NUEVO --}}
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maquinas as $m)
                    <tr>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">{{ $m->id }}</td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">{{ $m->nombre_maquina }}</td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">{{ ucfirst($m->dificultad) }}</td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            {{ $m->autor_nombre }}<br>
                            @if($m->autor_enlace)
                                <a href="{{ $m->autor_enlace }}" target="_blank" rel="noopener">Perfil</a>
                            @endif
                        </td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            {{ $m->fecha_creacion ? \Carbon\Carbon::parse($m->fecha_creacion)->format('d/m/Y') : '-' }}
                        </td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            @if($m->writeup)
                                <a href="{{ $m->writeup }}" target="_blank" rel="noopener">Ver writeup</a>
                            @else
                                -
                            @endif
                        </td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            @if($m->enlace_descarga)
                                <a href="{{ $m->enlace_descarga }}" target="_blank" rel="noopener">Descargar</a>
                            @else
                                -
                            @endif
                        </td>

                        <td style="border-bottom:1px solid #ddd; padding:8px; text-align:center;">
                            @if($m->imagen_path)
                                <button type="button"
                                        class="btn btn-sm"
                                        style="padding:.35rem .7rem; border-radius:6px; cursor:pointer;"
                                        onclick="document.getElementById('modal-img-{{ $m->id }}').removeAttribute('aria-hidden')">
                                    Ver imagen
                                </button>

                                <div id="modal-img-{{ $m->id }}" class="modal" role="dialog" aria-modal="true" aria-hidden="true"
                                     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999;">
                                    <div class="modal-card" role="document"
                                         style="max-width:900px; margin:5vh auto; background:#111827; color:#e5e7eb; border-radius:12px; overflow:hidden;">
                                        <header class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid #374151;">
                                            <h3 class="modal-title" style="margin:0; font-size:1rem;">Imagen — {{ $m->nombre_maquina }}</h3>
                                            <button class="modal-close" type="button" aria-label="Cerrar"
                                                    style="background:none; border:0; color:#e5e7eb; font-size:1.2rem; cursor:pointer;"
                                                    onclick="document.getElementById('modal-img-{{ $m->id }}').setAttribute('aria-hidden','true')">
                                                &times;
                                            </button>
                                        </header>
                                        <div class="modal-body" style="padding:16px;">
                                            <img src="{{ asset('storage/'.$m->imagen_path) }}"
                                                 alt="Imagen de {{ $m->nombre_maquina }}"
                                                 style="width:100%; height:auto; display:block; border-radius:10px; max-height:75vh; object-fit:contain;">
                                        </div>
                                        <footer class="modal-footer" style="padding:12px 16px; border-top:1px solid #374151; text-align:right;">
                                            <button class="btn btn-sm" type="button"
                                                    onclick="document.getElementById('modal-img-{{ $m->id }}').setAttribute('aria-hidden','true')">
                                                Cerrar
                                            </button>
                                        </footer>
                                    </div>
                                </div>
                                <script>
                                    (function(){
                                        const m = document.getElementById('modal-img-{{ $m->id }}');
                                        if (!m) return;
                                        const toggle = () => m.style.display = (m.getAttribute('aria-hidden')==='true') ? 'none' : 'block';
                                        const obs = new MutationObserver(toggle);
                                        obs.observe(m, { attributes:true, attributeFilter:['aria-hidden'] });
                                        toggle();
                                    })();
                                </script>
                            @else
                                -
                            @endif
                        </td>

                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            {{ optional($m->created_at)->diffForHumans() ?? '-' }}
                        </td>

                        {{-- NUEVO: Destino elegido en el envío --}}
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            @php
                                $dest = $m->destino ?? 'dockerlabs';
                            @endphp
                            {{ $dest === 'bunkerlabs' ? 'BunkerLabs' : 'DockerLabs' }}
                        </td>

                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            <form method="POST" action="{{ route('dockerlabs.admin.maquinas.recibidas.prefill', $m->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Aprobar Máquina</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:20px;">
            {{ $maquinas->links() }}
        </div>
    @endif

    <hr style="margin:32px 0; border-color:#ddd;">

    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
        <h3 style="margin:0;">Máquinas editadas</h3>
        <a href="{{ route('dockerlabs.admin.maquinas-editadas.index') }}" class="btn btn-primary">Ver ediciones pendientes</a>
    </div>
    <p class="muted">Aquí verás las solicitudes de los usuarios para modificar máquinas ya publicadas.</p>
</div>
@endsection

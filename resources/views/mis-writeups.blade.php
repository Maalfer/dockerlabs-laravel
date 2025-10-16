@extends('layouts.app')

@section('title', 'Mis Writeups')

@section('content')
<div class="container" style="display:grid; gap:24px;">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert" style="padding:.75rem; background:#e6ffed; border:1px solid #b7f5c9; color:#0a572a;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert" style="padding:.75rem; background:#fff5f5; border:1px solid #ffd6d6; color:#7a1a1a;">
            {{ $errors->first() }}
        </div>
    @endif

    <header>
        <h1 style="margin:0;">Mis Writeups</h1>
        <p style="opacity:.8; margin:.25rem 0 0;">
            {{ $user->name }} — aquí tienes todos tus envíos.
        </p>
    </header>

    {{-- Aprobados --}}
    <section>
        <h2 style="margin:0 0 .5rem 0;">Aprobados ({{ $aprobados->count() }})</h2>

        @if($aprobados->isEmpty())
            <div class="alert" style="padding:.75rem; background:#f6f6f6; border:1px solid #eee;">
                Aún no tienes writeups aprobados.
            </div>
        @else
            <ul style="margin:0; padding-left:18px; display:grid; gap:12px;">
                @foreach($aprobados as $w)
                    @php
                        // Si el controlador pasó $pendientesEdicion como keyBy('writeup_id'):
                        $pend = isset($pendientesEdicion) ? $pendientesEdicion->get($w->id ?? null) : null;
                    @endphp
                    <li style="display:grid; gap:6px;">
                        <div style="display:flex; gap:.5rem; align-items:center; flex-wrap:wrap;">
                            <span>
                                <strong>{{ $w->maquina->nombre ?? 'Máquina' }}</strong>
                                <small style="opacity:.7;">— {{ $w->created_at?->format('Y-m-d') }}</small>
                            </span>
                            <a href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                                {{ \Illuminate\Support\Str::limit($w->enlace, 90) }}
                            </a>

                            @if($pend)
                                <span class="badge" style="margin-left:auto; font-size:.75rem; background:#fff3cd; border:1px solid #ffeeba; padding:.15rem .4rem; border-radius:4px;">
                                    Edición pendiente
                                </span>
                            @endif
                        </div>

                        {{-- Formulario para solicitar cambio de enlace (solo si NO hay edición pendiente) --}}
                        @unless($pend)
                        <form method="POST" action="{{ route('mis-writeups.solicitar-cambio', $w->id) }}"
                              style="display:grid; gap:6px; max-width:720px;">
                            @csrf
                            <label style="display:grid; gap:4px;">
                                <span style="font-size:.9rem; opacity:.8;">Cambiar enlace</span>
                                <input
                                    type="url"
                                    name="enlace"
                                    class="form-control"
                                    value="{{ old('enlace', $w->enlace) }}"
                                    required
                                    placeholder="https://..."
                                    maxlength="2048">
                            </label>

                            <label style="display:grid; gap:4px;">
                                <span style="font-size:.9rem; opacity:.8;">Comentario (opcional)</span>
                                <input
                                    type="text"
                                    name="comentario"
                                    class="form-control"
                                    maxlength="500"
                                    placeholder="Motivo del cambio (opcional)">
                            </label>

                            <div>
                                <button class="btn btn-xs btn-primary" type="submit">
                                    Solicitar cambio
                                </button>
                            </div>
                        </form>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Enviados/Pendientes (si existe la tabla/modelo temporal) --}}
    @if($enviados !== null && $enviados->count() >= 0)
    <section>
        <h2 style="margin:0 0 .5rem 0;">Enviados / Pendientes ({{ $enviados->count() }})</h2>

        @if($enviados->isEmpty())
            <div class="alert" style="padding:.75rem; background:#f6f6f6; border:1px solid #eee;">
                No tienes envíos pendientes.
            </div>
        @else
            <ul style="margin:0; padding-left:18px; display:grid; gap:8px;">
                @foreach($enviados as $w)
                    <li style="display:flex; flex-wrap:wrap; gap:.5rem; align-items:center;">
                        <span>
                            <strong>{{ $w->maquina->nombre ?? 'Máquina' }}</strong>
                            <small style="opacity:.7;">— {{ $w->created_at?->format('Y-m-d') }}</small>
                        </span>
                        <a href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                            {{ \Illuminate\Support\Str::limit($w->enlace, 90) }}
                        </a>

                        <span class="badge" style="margin-left:auto; font-size:.75rem; opacity:.8;">
                            {{ $w->tipo === 'edicion' ? 'Edición' : 'Nuevo' }} — {{ ucfirst($w->estado ?? 'pendiente') }}
                        </span>

                        @if(!empty($w->comentario))
                            <div style="width:100%;">
                                <small style="opacity:.8;">📝 {{ \Illuminate\Support\Str::limit($w->comentario, 200) }}</small>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
    @endif
</div>
@endsection

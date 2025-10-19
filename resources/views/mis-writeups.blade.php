@extends('layouts.app')

@section('title', 'Mis Writeups')

@section('content')
<div class="container writeups" style="display:grid; gap:24px;">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert--success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert--error">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="wu-header">
        <h1 class="wu-title">Mis Writeups</h1>
        <p class="wu-subtitle">
            {{ $user->name }} — aquí tienes todos tus envíos.
        </p>
    </header>

    {{-- Aprobados --}}
    <section class="wu-section">
        <h2 class="wu-h2">Aprobados ({{ $aprobados->count() }})</h2>

        @if($aprobados->isEmpty())
            <div class="alert alert--empty">
                Aún no tienes writeups aprobados.
            </div>
        @else
            <ul class="wu-list">
                @foreach($aprobados as $w)
                    @php
                        $pend = isset($pendientesEdicion) ? $pendientesEdicion->get($w->id ?? null) : null;
                    @endphp

                    <li class="wu-item card">
                        <div class="wu-row">
                            <span class="wu-meta">
                                <strong>{{ $w->maquina->nombre ?? 'Máquina' }}</strong>
                                <small class="wu-date">— {{ $w->created_at?->format('Y-m-d') }}</small>
                            </span>

                            <a class="wu-link" href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                                {{ \Illuminate\Support\Str::limit($w->enlace, 90) }}
                            </a>

                            @if($pend)
                                <span class="badge-soft badge-soft--warn" style="margin-left:auto;">
                                    Edición pendiente
                                </span>
                            @endif
                        </div>

                        @unless($pend)
                        <form method="POST"
                              action="{{ route('mis-writeups.solicitar-cambio', $w->id) }}"
                              class="wu-form">
                            @csrf

                            <label class="wu-field">
                                <span class="wu-label">Cambiar enlace</span>
                                <input
                                    type="url"
                                    name="enlace"
                                    class="form-control"
                                    value="{{ old('enlace', $w->enlace) }}"
                                    required
                                    placeholder="https://..."
                                    maxlength="2048">
                            </label>

                            <label class="wu-field">
                                <span class="wu-label">Comentario (opcional)</span>
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

    {{-- Enviados/Pendientes --}}
    @if($enviados !== null && $enviados->count() >= 0)
    <section class="wu-section">
        <h2 class="wu-h2">Enviados / Pendientes ({{ $enviados->count() }})</h2>

        @if($enviados->isEmpty())
            <div class="alert alert--empty">
                No tienes envíos pendientes.
            </div>
        @else
            <ul class="wu-list">
                @foreach($enviados as $w)
                    <li class="wu-item card">
                        <div class="wu-row">
                            <span class="wu-meta">
                                <strong>{{ $w->maquina->nombre ?? 'Máquina' }}</strong>
                                <small class="wu-date">— {{ $w->created_at?->format('Y-m-d') }}</small>
                            </span>

                            <a class="wu-link" href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                                {{ \Illuminate\Support\Str::limit($w->enlace, 90) }}
                            </a>

                            <span class="badge-soft" style="margin-left:auto;">
                                {{ $w->tipo === 'edicion' ? 'Edición' : 'Nuevo' }} — {{ ucfirst($w->estado ?? 'pendiente') }}
                            </span>
                        </div>

                        @if(!empty($w->comentario))
                            <div class="wu-comment">
                                <small>📝 {{ \Illuminate\Support\Str::limit($w->comentario, 200) }}</small>
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

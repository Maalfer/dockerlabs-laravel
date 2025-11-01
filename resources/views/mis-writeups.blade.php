@extends('layouts.app')

@section('title', 'Mis Writeups')

@section('content')
<div class="writeups-container">
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success success-message">
            <div class="alert-icon">✅</div>
            <div class="alert-content">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error error-message">
            <div class="alert-icon">⚠️</div>
            <div class="alert-content">
                <strong>Error:</strong> {{ $errors->first() }}
            </div>
        </div>
    @endif

    {{-- Header Section --}}
    <header class="writeups-header">
        <div class="header-content">
            <h1 class="writeups-title">📝 Mis Writeups</h1>
            <p class="writeups-subtitle">
                {{ $user->name }} — aquí tienes todos tus envíos y su estado actual.
            </p>
            <div class="user-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $aprobados->count() }}</span>
                    <span class="stat-label">Aprobados</span>
                </div>
                @if($enviados !== null && $enviados->count() >= 0)
                <div class="stat-item">
                    <span class="stat-number">{{ $enviados->count() }}</span>
                    <span class="stat-label">Pendientes</span>
                </div>
                @endif
            </div>
        </div>
        <div class="header-decoration"></div>
    </header>

    {{-- Aprobados Section --}}
    <section class="writeups-section approved-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-icon">✅</span>
                Writeups Aprobados
                <span class="section-count">{{ $aprobados->count() }}</span>
            </h2>
            <div class="section-subtitle">Tus writeups que han sido aprobados y son visibles para la comunidad.</div>
        </div>

        @if($aprobados->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📄</div>
                <h3 class="empty-title">Aún no tienes writeups aprobados</h3>
                <p class="empty-description">Cuando tus writeups sean revisados y aprobados, aparecerán aquí.</p>
            </div>
        @else
            <div class="writeups-grid">
                @foreach($aprobados as $w)
                    @php
                        $pend = isset($pendientesEdicion) ? $pendientesEdicion->get($w->id ?? null) : null;
                    @endphp

                    <article class="writeup-card {{ $pend ? 'has-pending-edit' : '' }}">
                        <div class="writeup-header">
                            <div class="writeup-meta">
                                <h3 class="writeup-machine">{{ $w->maquina->nombre ?? 'Máquina' }}</h3>
                                <div class="writeup-details">
                                    <span class="writeup-date">📅 {{ $w->created_at?->format('d/m/Y') }}</span>
                                    <span class="writeup-status status-approved">Aprobado</span>
                                </div>
                            </div>
                            <div class="writeup-actions">
                                @if($pend)
                                    <span class="badge badge-warning">
                                        ✏️ Edición pendiente
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="writeup-content">
                            <a class="writeup-link" href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                                <span class="link-icon">🔗</span>
                                <span class="link-text">{{ \Illuminate\Support\Str::limit($w->enlace, 70) }}</span>
                                <span class="link-external">↗</span>
                            </a>
                        </div>

                        @unless($pend)
                        <div class="writeup-edit-form">
                            <form method="POST"
                                  action="{{ route('dockerlabs.mis-writeups.solicitar-cambio', $w->id) }}"
                                  class="edit-form">
                                @csrf
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="enlace-{{ $w->id }}" class="form-label">
                                            <span class="label-text">Nuevo enlace</span>
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <input
                                            id="enlace-{{ $w->id }}"
                                            type="url"
                                            name="enlace"
                                            class="form-control"
                                            value="{{ old('enlace', $w->enlace) }}"
                                            required
                                            placeholder="https://tu-nuevo-writeup.com"
                                            maxlength="2048">
                                        <div class="form-hint">Actualiza el enlace de tu writeup</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="comentario-{{ $w->id }}" class="form-label">
                                            <span class="label-text">Comentario (opcional)</span>
                                        </label>
                                        <input
                                            id="comentario-{{ $w->id }}"
                                            type="text"
                                            name="comentario"
                                            class="form-control"
                                            maxlength="500"
                                            placeholder="Motivo del cambio..."
                                            value="{{ old('comentario') }}">
                                        <div class="form-hint">Explica por qué solicitas el cambio</div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button class="btn btn-primary edit-button" type="submit">
                                        <span class="button-icon">✏️</span>
                                        Solicitar cambio
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endunless
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Enviados/Pendientes Section --}}
    @if($enviados !== null && $enviados->count() >= 0)
    <section class="writeups-section pending-section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-icon">⏳</span>
                Envíos Pendientes
                <span class="section-count">{{ $enviados->count() }}</span>
            </h2>
            <div class="section-subtitle">Tus writeups que están en proceso de revisión.</div>
        </div>

        @if($enviados->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">✅</div>
                <h3 class="empty-title">No tienes envíos pendientes</h3>
                <p class="empty-description">Todos tus writeups han sido procesados.</p>
            </div>
        @else
            <div class="writeups-grid">
                @foreach($enviados as $w)
                    <article class="writeup-card pending-card">
                        <div class="writeup-header">
                            <div class="writeup-meta">
                                <h3 class="writeup-machine">{{ $w->maquina->nombre ?? 'Máquina' }}</h3>
                                <div class="writeup-details">
                                    <span class="writeup-date">📅 {{ $w->created_at?->format('d/m/Y') }}</span>
                                    <span class="writeup-status status-{{ $w->estado ?? 'pending' }}">
                                        {{ ucfirst($w->estado ?? 'pendiente') }}
                                    </span>
                                </div>
                            </div>
                            <div class="writeup-actions">
                                <span class="badge badge-{{ $w->tipo === 'edicion' ? 'info' : 'secondary' }}">
                                    {{ $w->tipo === 'edicion' ? '✏️ Edición' : '🆕 Nuevo' }}
                                </span>
                            </div>
                        </div>

                        <div class="writeup-content">
                            <a class="writeup-link" href="{{ $w->enlace }}" target="_blank" rel="noopener noreferrer">
                                <span class="link-icon">🔗</span>
                                <span class="link-text">{{ \Illuminate\Support\Str::limit($w->enlace, 70) }}</span>
                                <span class="link-external">↗</span>
                            </a>
                        </div>

                        @if(!empty($w->comentario))
                            <div class="writeup-comment">
                                <div class="comment-header">
                                    <span class="comment-icon">💬</span>
                                    <strong>Comentario:</strong>
                                </div>
                                <p class="comment-text">{{ \Illuminate\Support\Str::limit($w->comentario, 200) }}</p>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
    @endif
</div>

<style>
.writeups-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
    display: grid;
    gap: 2.5rem;
}

/* Header Styles */
.writeups-header {
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.writeups-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--brand-500), var(--brand-300), var(--brand-500));
}

.header-content {
    position: relative;
    z-index: 2;
}

.writeups-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    background: linear-gradient(135deg, var(--text), var(--brand-300));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: -0.02em;
}

.writeups-subtitle {
    font-size: 1.1rem;
    color: var(--muted);
    margin: 0 0 2rem 0;
    opacity: 0.9;
}

.user-stats {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: var(--brand-300);
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Alert Styles */
.success-message,
.error-message {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid;
    backdrop-filter: blur(10px);
    animation: slideInDown 0.5s var(--ease);
}

.success-message {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
    border-color: rgba(34, 197, 94, 0.3);
    color: #4ade80;
}

.error-message {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
    border-color: rgba(239, 68, 68, 0.3);
    color: #f87171;
}

.alert-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
}

.alert-content {
    flex: 1;
}

/* Section Styles */
.writeups-section {
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all var(--dur-med) var(--ease);
}

.writeups-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(31, 94, 215, 0.15);
}

.section-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: var(--text);
}

.title-icon {
    font-size: 1.25em;
}

.section-count {
    background: var(--brand-500);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
    margin-left: auto;
}

.section-subtitle {
    color: var(--muted);
    font-size: 1rem;
    margin: 0;
}

/* Writeup Grid */
.writeups-grid {
    display: grid;
    gap: 1.5rem;
}

/* Writeup Card */
.writeup-card {
    background: linear-gradient(135deg, var(--bg-elev), var(--surface));
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all var(--dur-med) var(--ease);
    position: relative;
}

.writeup-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 94, 215, 0.15);
    border-color: rgba(138, 180, 248, 0.3);
}

.writeup-card.has-pending-edit {
    border-left: 4px solid #f59e0b;
}

.writeup-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.writeup-meta {
    flex: 1;
}

.writeup-machine {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: var(--text);
}

.writeup-details {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.writeup-date {
    font-size: 0.9rem;
    color: var(--muted);
}

.writeup-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-approved {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.status-pending {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.writeup-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

/* Badge Styles */
.badge {
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge-warning {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.badge-info {
    background: rgba(59, 130, 246, 0.15);
    color: var(--brand-300);
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-secondary {
    background: rgba(100, 116, 139, 0.15);
    color: var(--muted);
    border: 1px solid rgba(100, 116, 139, 0.3);
}

/* Writeup Content */
.writeup-content {
    margin-bottom: 1rem;
}

.writeup-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(138, 180, 248, 0.08);
    border: 1px solid rgba(138, 180, 248, 0.2);
    border-radius: 8px;
    color: var(--brand-300);
    text-decoration: none;
    transition: all var(--dur-fast) var(--ease);
    word-break: break-all;
}

.writeup-link:hover {
    background: rgba(138, 180, 248, 0.15);
    border-color: var(--brand-300);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.link-icon {
    font-size: 1rem;
    flex-shrink: 0;
}

.link-text {
    flex: 1;
    font-size: 0.9rem;
}

.link-external {
    font-size: 1rem;
    opacity: 0.7;
    flex-shrink: 0;
}

/* Comment Styles */
.writeup-comment {
    background: rgba(138, 180, 248, 0.05);
    border: 1px solid rgba(138, 180, 248, 0.1);
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: var(--muted);
    font-weight: 600;
}

.comment-icon {
    font-size: 1rem;
}

.comment-text {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text);
    line-height: 1.5;
}

/* Edit Form Styles */
.writeup-edit-form {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

.edit-form {
    display: grid;
    gap: 1rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 600;
    color: var(--text);
    font-size: 0.9rem;
}

.required-asterisk {
    color: #ef4444;
    font-weight: 700;
}

.form-control {
    background: var(--bg-deep);
    border: 2px solid var(--border);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    color: var(--text);
    font-size: 0.9rem;
    transition: all var(--dur-fast) var(--ease);
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--brand-400);
    box-shadow: 0 0 0 3px rgba(138, 180, 248, 0.15);
    background: var(--bg-elev);
}

.form-control::placeholder {
    color: var(--muted);
    opacity: 0.7;
}

.form-hint {
    font-size: 0.8rem;
    color: var(--muted);
    line-height: 1.4;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
}

.edit-button {
    background: var(--grad-2);
    border: 2px solid rgba(138, 180, 248, 0.4);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(31, 94, 215, 0.25);
    transition: all var(--dur-med) var(--ease);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.edit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(31, 94, 215, 0.35);
    filter: brightness(1.1);
}

.button-icon {
    font-size: 1rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--muted);
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: var(--text);
}

.empty-description {
    margin: 0;
    font-size: 1rem;
    opacity: 0.8;
}

/* Animations */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.writeups-section {
    animation: fadeInUp 0.6s var(--ease) both;
}

.approved-section { animation-delay: 0.1s; }
.pending-section { animation-delay: 0.2s; }

/* Responsive Design */
@media (max-width: 768px) {
    .writeups-container {
        padding: 1rem;
        gap: 2rem;
    }

    .writeups-header {
        padding: 2rem 1.5rem;
    }

    .writeups-title {
        font-size: 2rem;
    }

    .user-stats {
        gap: 1.5rem;
    }

    .stat-number {
        font-size: 1.75rem;
    }

    .writeups-section {
        padding: 1.5rem;
    }

    .section-title {
        font-size: 1.25rem;
    }

    .writeup-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .writeup-actions {
        align-self: flex-start;
    }

    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .section-count {
        margin-left: 0;
    }

    .section-title {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .writeups-title {
        font-size: 1.75rem;
    }

    .user-stats {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .stat-item {
        flex-direction: row;
        gap: 0.5rem;
        align-items: center;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .writeup-card {
        padding: 1.25rem;
    }

    .writeup-details {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
@endsection
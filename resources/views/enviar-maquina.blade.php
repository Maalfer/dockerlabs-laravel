@extends('layouts.app')

@section('title', 'Enviar máquina')

@section('content')
<div class="admin-page submit-machine-container">
    <div class="submit-machine-header">
        <h2 class="submit-machine-title">🚀 Enviar máquina</h2>
        <p class="submit-machine-subtitle">Completa el formulario para proponer una nueva máquina a la comunidad.</p>
    </div>

    @guest
        <div class="alert alert-info guest-alert" role="alert">
            <div class="alert-icon">💡</div>
            <div class="alert-content">
                <strong>¿Aún no tienes cuenta?</strong> Para enviar una máquina más rápido y con tu autoría, 
                <a href="{{ route('dockerlabs.register') }}" class="alert-link">regístrate</a> o 
                <a href="{{ route('dockerlabs.login') }}" class="alert-link">inicia sesión</a>.
            </div>
        </div>
    @endguest

    @if (session('success'))
        <div class="alert alert-success success-alert" role="status">
            <div class="alert-icon">✅</div>
            <div class="alert-content">{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger error-alert" role="alert">
            <div class="alert-icon">⚠️</div>
            <div class="alert-content">
                <strong>Por favor, corrige los siguientes errores:</strong>
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- IMPORTANTE: enctype para permitir subida de archivos -->
    <form method="POST" action="{{ route('dockerlabs.enviar-maquina.store') }}" enctype="multipart/form-data" novalidate class="submit-machine-form">
        @csrf

        <div class="form-section">
            <h3 class="form-section-title">Información básica</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre_maquina" class="form-label">
                        <span class="label-text">Nombre de la máquina</span>
                        <span class="required-asterisk">*</span>
                    </label>
                    <input id="nombre_maquina" name="nombre_maquina" type="text" class="form-control"
                           value="{{ old('nombre_maquina') }}" placeholder="Ej: Mi Máquina HackTheBox" required>
                    <div class="form-hint">El nombre que identificará tu máquina</div>
                </div>

                <div class="form-group">
                    <label for="dificultad" class="form-label">
                        <span class="label-text">Dificultad</span>
                        <span class="required-asterisk">*</span>
                    </label>
                    <select id="dificultad" name="dificultad" class="form-control" required>
                        <option value="">Selecciona la dificultad...</option>
                        <option value="facil" {{ old('dificultad')==='facil' ? 'selected' : '' }} data-difficulty="easy">🟢 Fácil</option>
                        <option value="medio" {{ old('dificultad')==='medio' ? 'selected' : '' }} data-difficulty="medium">🟡 Medio</option>
                        <option value="dificil" {{ old('dificultad')==='dificil' ? 'selected' : '' }} data-difficulty="hard">🔴 Difícil</option>
                    </select>
                    <div class="form-hint">Nivel de dificultad estimado</div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="form-section-title">Información del autor</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="autor_nombre" class="form-label">
                        <span class="label-text">Nombre del autor</span>
                        <span class="required-asterisk">*</span>
                    </label>
                    @auth
                        <div class="auth-user-field">
                            <input id="autor_nombre" name="autor_nombre" type="text" class="form-control"
                                   value="{{ old('autor_nombre', auth()->user()->name) }}" required readonly>
                            <div class="auth-badge">👤 Identificado</div>
                        </div>
                    @else
                        <input id="autor_nombre" name="autor_nombre" type="text" class="form-control"
                               value="{{ old('autor_nombre') }}" placeholder="Tu nombre o alias" required>
                    @endauth
                    <div class="form-hint">Tu nombre o alias público</div>
                </div>

                <div class="form-group">
                    <label for="autor_enlace" class="form-label">
                        <span class="label-text">Enlace del autor (URL)</span>
                    </label>
                    <input id="autor_enlace" name="autor_enlace" type="url" class="form-control"
                           value="{{ old('autor_enlace') }}" placeholder="https://tupagina.com o perfil social">
                    <div class="form-hint">Opcional: tu sitio web o perfil</div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="form-section-title">Detalles adicionales</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="fecha_creacion" class="form-label">
                        <span class="label-text">Fecha de creación</span>
                    </label>
                    <input id="fecha_creacion" name="fecha_creacion" type="date" class="form-control"
                           value="{{ old('fecha_creacion') }}">
                    <div class="form-hint">Cuándo creaste la máquina</div>
                </div>

                <div class="form-group">
                    <label for="writeup" class="form-label">
                        <span class="label-text">Writeup (URL)</span>
                    </label>
                    <input id="writeup" name="writeup" type="url" class="form-control"
                           value="{{ old('writeup') }}" placeholder="https://tuwriteup.com">
                    <div class="form-hint">Enlace a tu writeup o solución</div>
                </div>

                <div class="form-group full-width">
                    <label for="enlace_descarga" class="form-label">
                        <span class="label-text">Enlace de descarga (URL)</span>
                    </label>
                    <input id="enlace_descarga" name="enlace_descarga" type="url" class="form-control"
                           value="{{ old('enlace_descarga') }}" placeholder="https://descarga.com/maquina.zip">
                    <div class="form-hint">Enlace para descargar la máquina</div>
                </div>

                <!-- NUEVO: Campo para adjuntar imagen -->
                <div class="form-group full-width">
                    <label for="imagen" class="form-label">
                        <span class="label-text">Imagen (opcional)</span>
                    </label>
                    <input id="imagen" name="imagen" type="file" class="form-control" accept="image/*">
                    <div class="form-hint">Formatos admitidos: JPG, PNG, WEBP. Tamaño máximo: 2 MB.</div>
                </div>
                <!-- FIN NUEVO -->
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary submit-button">
                <span class="button-icon">🚀</span>
                Enviar máquina
            </button>
            <a href="{{ route('dockerlabs.home') }}" class="btn btn-outline cancel-button">
                <span class="button-icon">←</span>
                Cancelar
            </a>
        </div>
    </form>
</div>

<style>
.submit-machine-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.submit-machine-header {
    text-align: center;
    margin-bottom: 2.5rem;
    padding: 2rem;
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 20px;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.submit-machine-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--brand-500), var(--brand-300), var(--brand-500));
}

.submit-machine-title {
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    background: linear-gradient(135deg, var(--text), var(--brand-300));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: -0.02em;
}

.submit-machine-subtitle {
    font-size: 1.1rem;
    color: var(--muted);
    margin: 0;
    opacity: 0.9;
}

/* Alert Styles */
.guest-alert,
.success-alert,
.error-alert {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    border: 1px solid;
    backdrop-filter: blur(10px);
}

.guest-alert {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(96, 165, 250, 0.05));
    border-color: rgba(96, 165, 250, 0.3);
    color: var(--brand-300);
}

.success-alert {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
    border-color: rgba(34, 197, 94, 0.3);
    color: #4ade80;
}

.error-alert {
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

.alert-link {
    color: inherit;
    font-weight: 600;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.alert-link:hover {
    color: var(--text);
}

.error-list {
    margin: 0.5rem 0 0 0;
    padding-left: 1.25rem;
    color: var(--text);
}

.error-list li {
    margin: 0.25rem 0;
}

/* Form Styles */
.submit-machine-form {
    display: grid;
    gap: 2.5rem;
}

.form-section {
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all var(--dur-med) var(--ease);
}

.form-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(31, 94, 215, 0.15);
    border-color: rgba(138, 180, 248, 0.2);
}

.form-section-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 1.5rem 0;
    color: var(--brand-300);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 600;
    color: var(--text);
    font-size: 0.95rem;
}

.required-asterisk {
    color: #ef4444;
    font-weight: 700;
}

.form-control {
    background: var(--bg-elev);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    color: var(--text);
    font-size: 1rem;
    transition: all var(--dur-fast) var(--ease);
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--brand-400);
    box-shadow: 0 0 0 4px rgba(138, 180, 248, 0.15);
    background: var(--bg-deep);
    transform: translateY(-1px);
}

.form-control::placeholder {
    color: var(--muted);
    opacity: 0.7;
}

.form-control:read-only {
    background: rgba(138, 180, 248, 0.08);
    border-color: rgba(138, 180, 248, 0.2);
    color: var(--brand-300);
    cursor: not-allowed;
}

.form-hint {
    font-size: 0.85rem;
    color: var(--muted);
    line-height: 1.4;
}

/* Auth User Field */
.auth-user-field {
    position: relative;
}

.auth-badge {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

/* Select Styles */
select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%238ab4f8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.25rem;
    padding-right: 3rem;
}

/* Difficulty-specific select options */
select.form-control option[data-difficulty="easy"] {
    color: #10b981;
}

select.form-control option[data-difficulty="medium"] {
    color: #f59e0b;
}

select.form-control option[data-difficulty="hard"] {
    color: #ef4444;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: var(--shadow);
}

.submit-button {
    background: var(--grad-2);
    border: 2px solid rgba(138, 180, 248, 0.4);
    color: white;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 8px 25px rgba(31, 94, 215, 0.3);
    transition: all var(--dur-med) var(--ease);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.submit-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(31, 94, 215, 0.4);
    filter: brightness(1.1);
}

.cancel-button {
    background: transparent;
    border: 2px solid var(--border);
    color: var(--text);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all var(--dur-med) var(--ease);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cancel-button:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: var(--brand-300);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(31, 94, 215, 0.15);
}

.button-icon {
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .submit-machine-container {
        padding: 1rem;
    }
    
    .submit-machine-header {
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .submit-machine-title {
        font-size: 1.75rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .form-section {
        padding: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
        padding: 1.5rem;
    }
    
    .submit-button,
    .cancel-button {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .submit-machine-title {
        font-size: 1.5rem;
    }
    
    .guest-alert,
    .success-alert,
    .error-alert {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .form-section {
        padding: 1.25rem;
    }
}

/* Animation for form sections */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-section {
    animation: slideInUp 0.6s var(--ease) both;
}

.form-section:nth-child(1) { animation-delay: 0.1s; }
.form-section:nth-child(2) { animation-delay: 0.2s; }
.form-section:nth-child(3) { animation-delay: 0.3s; }
.form-actions { animation-delay: 0.4s; }
</style>
@endsection

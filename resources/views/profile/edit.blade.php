@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div class="profile-container">
        <div class="profile-header">
            <h2 class="profile-title">Mi Perfil</h2>
            <span class="role-badge">
                Rol: {{ strtoupper($user->role ?? 'user') }}
            </span>
        </div>

        @if (session('status'))
            <div class="alert alert--success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert--error">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('bunker_token_plain'))
            <div class="token-alert">
                <div class="token-alert__header">
                    <strong>Nuevo token Bunkerlabs:</strong>
                </div>
                <code class="token-alert__code">{{ session('bunker_token_plain') }}</code>
                <div class="token-alert__note">Cópialo y guárdalo ahora. No volverá a mostrarse.</div>
            </div>
        @endif

        <section class="profile-section">
            <h3 class="section-title">Resumen</h3>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card__label">Writeups enviados (pendientes)</div>
                    <div class="stat-card__value">{{ $stats['writeups_pendientes_user'] ?? 0 }}</div>
                    <div class="stat-card__action">
                        @if(($stats['writeups_pendientes_user'] ?? 0) > 0)
                            <a href="{{ route('dockerlabs.admin.writeups-temporal.index') }}" class="stat-link">Revisar</a>
                        @endif
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__label">Tus writeups aprobados</div>
                    <div class="stat-card__value">{{ $stats['writeups_aprobados_user'] ?? 0 }}</div>
                    <div class="stat-card__action">
                        <a href="{{ route('dockerlabs.mis-writeups.index') }}" class="stat-link">Ver mis writeups</a>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__label">Máquinas enviadas</div>
                    <div class="stat-card__value">{{ $stats['maquinas_enviadas_user'] ?? 0 }}</div>
                    <div class="stat-card__action">
                        <a href="{{ route('dockerlabs.enviar-maquina.form') }}" class="stat-link">Enviar nueva</a>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__label">
                        {{ ($stats['maquinas_aprobadas_user_known'] ?? false) ? 'Tus máquinas aprobadas' : 'Máquinas aprobadas (global)' }}
                    </div>
                    <div class="stat-card__value">
                        {{ ($stats['maquinas_aprobadas_user_known'] ?? false) ? ($stats['maquinas_aprobadas_user'] ?? 0) : ($stats['maquinas_aprobadas_global'] ?? 0) }}
                    </div>
                    <div class="stat-card__action" style="display:flex; gap:1rem; flex-wrap:wrap;">
                        <a href="{{ route('dockerlabs.mis-maquinas') }}" class="stat-link">Ver mis máquinas</a>
                        @if($user->isAdmin() || $user->isModerator())
                            <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}" class="stat-link">Ver recibidas</a>
                        @endif
                    </div>
                </div>
            </div>

            @if($user->isAdmin() || $user->isModerator())
                <div class="global-stats">
                    <strong>Global:</strong>
                    {{ ($stats['writeups_aprobados_global'] ?? 0) }} writeups aprobados ·
                    {{ ($stats['maquinas_aprobadas_global'] ?? 0) }} máquinas aprobadas ·
                    {{ ($stats['writeups_pendientes_global'] ?? 0) }} writeups pendientes
                </div>
            @endif
        </section>

        <section class="profile-section">
            <h3 class="section-title">Accesos rápidos</h3>
            <div class="quick-links">
                <a href="{{ route('dockerlabs.mis-writeups.index') }}" class="quick-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Mis Writeups</span>
                </a>
                <a href="{{ route('dockerlabs.mis-maquinas') }}" class="quick-link">
                    <i class="fas fa-cubes"></i>
                    <span>Mis Máquinas</span>
                </a>
                <a href="{{ route('dockerlabs.enviar-maquina.form') }}" class="quick-link">
                    <i class="fas fa-upload"></i>
                    <span>Enviar Máquina</span>
                </a>
                <a href="{{ route('bunkerlabs.login') }}" class="quick-link quick-link--bunker">
                    <i class="fas fa-shield-alt"></i>
                    <span>Login Bunkerlabs</span>
                </a>

                @if($user->isAdmin() || $user->isModerator())
                    <a href="{{ route('dockerlabs.admin.writeups-temporal.index') }}" class="quick-link quick-link--mod">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Moderar Writeups</span>
                    </a>
                    <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}" class="quick-link quick-link--mod">
                        <i class="fas fa-inbox"></i>
                        <span>Máquinas Recibidas</span>
                    </a>
                @endif

                @if($user->isAdmin())
                    <a href="{{ route('dockerlabs.admin.dashboard') }}" class="quick-link quick-link--admin">
                        <i class="fas fa-cogs"></i>
                        <span>Agregar Máquina</span>
                    </a>
                    <a href="{{ route('dockerlabs.profile.roles.index') }}" class="quick-link quick-link--admin">
                        <i class="fas fa-users-cog"></i>
                        <span>Gestión de roles</span>
                    </a>
                @endif
            </div>
        </section>

        @if($user->isAdmin())
            <section class="profile-section">
                <h3 class="section-title">Bunkerlabs — Gestión de tokens</h3>

                <form method="POST" action="{{ route('bunkerlabs.perfil.tokens.create') }}" class="token-form">
                    @csrf
                    <div class="token-form__grid">
                        <input type="text" name="name" placeholder="Etiqueta (opcional)" class="token-input">
                        <input type="datetime-local" name="expires_at" class="token-date">
                        <button type="submit" class="btn btn-primary">
                            Crear token
                        </button>
                    </div>
                </form>

                @php
                    $tokens = \App\Models\BunkerToken::orderByDesc('id')->limit(100)->get();
                @endphp

                <div class="table-container">
                    <table class="tokens-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Token (visible)</th>
                                <th>Activo</th>
                                <th>Expira</th>
                                <th>Usado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $t)
                                @php
                                    $visible = null;
                                    try {
                                        $visible = $t->token_ciphertext
                                            ? \Illuminate\Support\Facades\Crypt::decryptString($t->token_ciphertext)
                                            : null;
                                    } catch (\Throwable $e) {
                                        $visible = null;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $t->id }}</td>
                                    <td>{{ $t->name }}</td>
                                    <td>
                                        <span class="{{ $visible ? '' : 'hidden' }}">
                                            <code class="token-plain">{{ $visible }}</code>
                                            <button type="button" class="btn btn-outline btn-sm" onclick='navigator.clipboard.writeText(@json($visible))'>Copiar</button>
                                        </span>
                                        <span class="{{ $visible ? 'hidden' : '' }}">
                                            <span class="empty-state">—</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-badge--{{ $t->active ? 'active' : 'inactive' }}">
                                            {{ $t->active ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td>{{ $t->expires_at ? $t->expires_at->format('Y-m-d H:i') : '—' }}</td>
                                    <td>{{ $t->used_at ? $t->used_at->format('Y-m-d H:i') : '—' }}</td>
                                    <td>
                                        <div class="token-actions">
                                            <form method="POST" action="{{ route('bunkerlabs.perfil.tokens.toggle', $t->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-outline btn-sm">
                                                    {{ $t->active ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('bunkerlabs.perfil.tokens.delete', $t->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state">No hay tokens creados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <section class="profile-section">
            <h3 class="section-title">Datos de la cuenta</h3>

            <form method="POST" action="{{ route('dockerlabs.profile.update') }}" class="profile-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="form-input">
                </div>

                <div class="form-divider"></div>

                <p class="password-title">Cambio de contraseña (opcional)</p>

                <div class="form-group">
                    <label for="current_password" class="form-label">Contraseña actual</label>
                    <input id="current_password" name="current_password" type="password" class="form-input">
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Nueva contraseña</label>
                    <input id="new_password" name="new_password" type="password" class="form-input">
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                    <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="form-input">
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    Guardar cambios
                </button>
            </form>
        </section>
    </div>
@endsection

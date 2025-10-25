@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div class="profile-container">
        {{-- Título + rol actual --}}
        <div class="profile-header">
            <h2 class="profile-title">Mi Perfil</h2>
            <span class="role-badge">
                Rol: {{ strtoupper($user->role ?? 'user') }}
            </span>
        </div>

        {{-- Flashes --}}
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

        {{-- Mostrar token plano de Bunkerlabs cuando se crea (solo una vez) --}}
        @if (session('bunker_token_plain'))
            <div class="token-alert">
                <div class="token-alert__header">
                    <strong>Nuevo token Bunkerlabs:</strong>
                </div>
                <code class="token-alert__code">{{ session('bunker_token_plain') }}</code>
                <div class="token-alert__note">Cópialo y guárdalo ahora. No volverá a mostrarse.</div>
            </div>
        @endif

        {{-- RESUMEN / STATS --}}
        <section class="profile-section">
            <h3 class="section-title">Resumen</h3>

            {{-- Tarjetas en grid --}}
            <div class="stats-grid">
                {{-- Writeups enviados (pendientes) del usuario --}}
                <div class="stat-card">
                    <div class="stat-card__label">Writeups enviados (pendientes)</div>
                    <div class="stat-card__value">{{ $stats['writeups_pendientes_user'] ?? 0 }}</div>
                    <div class="stat-card__action">
                        @if(($stats['writeups_pendientes_user'] ?? 0) > 0)
                            <a href="{{ route('dockerlabs.admin.writeups-temporal.index') }}" class="stat-link">Revisar</a>
                        @endif
                    </div>
                </div>

                {{-- Writeups aprobados del usuario --}}
                <div class="stat-card">
                    <div class="stat-card__label">Tus writeups aprobados</div>
                    <div class="stat-card__value">{{ $stats['writeups_aprobados_user'] ?? 0 }}</div>
                    <div class="stat-card__action">
                        <a href="{{ route('dockerlabs.mis-writeups.index') }}" class="stat-link">Ver mis writeups</a>
                    </div>
                </div>

                {{-- Máquinas enviadas por el usuario --}}
                <div class="stat-card">
                    <div class="stat-card__label">Máquinas enviadas</div>
                    <div class="stat-card__value">{{ $stats['maquinas_enviadas_user'] ?? 0 }}</div>
                    <div class="stat-card__action">
                        <a href="{{ route('dockerlabs.enviar-maquina.form') }}" class="stat-link">Enviar nueva</a>
                    </div>
                </div>

                {{-- Máquinas aprobadas --}}
                <div class="stat-card">
                    <div class="stat-card__label">
                        {{ ($stats['maquinas_aprobadas_user_known'] ?? false) ? 'Tus máquinas aprobadas' : 'Máquinas aprobadas (global)' }}
                    </div>
                    <div class="stat-card__value">
                        {{ $stats['maquinas_aprobadas_user_known'] ? ($stats['maquinas_aprobadas_user'] ?? 0) : ($stats['maquinas_aprobadas_global'] ?? 0) }}
                    </div>
                    @if(($user->isAdmin() || $user->isModerator()))
                        <div class="stat-card__action">
                            <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}" class="stat-link">Ver recibidas</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen global (opcional, visible a admin/moderador) --}}
            @if($user->isAdmin() || $user->isModerator())
                <div class="global-stats">
                    <strong>Global:</strong>
                    {{ ($stats['writeups_aprobados_global'] ?? 0) }} writeups aprobados ·
                    {{ ($stats['maquinas_aprobadas_global'] ?? 0) }} máquinas aprobadas ·
                    {{ ($stats['writeups_pendientes_global'] ?? 0) }} writeups pendientes
                </div>
            @endif
        </section>

        {{-- ACCESOS RÁPIDOS según rol --}}
        <section class="profile-section">
            <h3 class="section-title">Accesos rápidos</h3>
            <div class="quick-links">
                {{-- Todos los usuarios --}}
                <a href="{{ route('dockerlabs.mis-writeups.index') }}" class="quick-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Mis Writeups</span>
                </a>
                <a href="{{ route('dockerlabs.enviar-maquina.form') }}" class="quick-link">
                    <i class="fas fa-upload"></i>
                    <span>Enviar Máquina</span>
                </a>
                <a href="{{ route('bunkerlabs.login') }}" class="quick-link quick-link--bunker">
                    <i class="fas fa-shield-alt"></i>
                    <span>Login Bunkerlabs</span>
                </a>

                {{-- Moderación (admin o moderador) --}}
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

                {{-- Admin exclusivo --}}
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

        {{-- BUNKERLABS: Gestión de tokens (solo admin) --}}
        @if($user->isAdmin())
            <section class="profile-section">
                <h3 class="section-title">Bunkerlabs — Gestión de tokens</h3>

                {{-- Crear token --}}
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

                {{-- Listado de tokens --}}
                @php($tokens = \App\Models\BunkerToken::orderByDesc('id')->limit(100)->get())
                <div class="table-container">
                    <table class="tokens-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Activo</th>
                                <th>Expira</th>
                                <th>Usado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $t)
                                <tr>
                                    <td>{{ $t->id }}</td>
                                    <td>{{ $t->name }}</td>
                                    <td>
                                        <span class="status-badge status-badge--{{ $t->active ? 'active' : 'inactive' }}">
                                            {{ $t->active ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $t->expires_at ? $t->expires_at->format('Y-m-d H:i') : '—' }}
                                    </td>
                                    <td>
                                        {{ $t->used_at ? $t->used_at->format('Y-m-d H:i') : '—' }}
                                    </td>
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
                                    <td colspan="6" class="empty-state">No hay tokens creados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- FORMULARIO PERFIL --}}
        <section class="profile-section">
            <h3 class="section-title">Datos de la cuenta</h3>

            <form method="POST" action="{{ route('dockerlabs.profile.update') }}" class="profile-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" 
                           required class="form-input">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" 
                           required class="form-input">
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

    <style>
        /* Profile Container */
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.25rem;
        }

        /* Header */
        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .profile-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--brand-300), var(--brand-200));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--brand-600), var(--brand-500));
            color: white;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
            border: 1px solid rgba(138, 180, 248, 0.3);
            box-shadow: var(--shadow);
        }

        /* Sections */
        .profile-section {
            margin: 2.5rem 0;
        }

        .section-title {
            margin: 0 0 1.5rem 0;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-500), transparent);
            border-radius: 2px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--surface), var(--surface-2));
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            color: var(--text);
            box-shadow: var(--shadow);
            transition: all var(--dur-med) var(--ease);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-500), var(--brand-300));
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(31, 94, 215, 0.15);
            border-color: rgba(138, 180, 248, 0.3);
        }

        .stat-card__label {
            opacity: 0.8;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .stat-card__value {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            margin: 0.5rem 0;
            background: linear-gradient(135deg, var(--text), var(--brand-200));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .stat-card__action {
            margin-top: 1rem;
        }

        .stat-link {
            color: var(--brand-300);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color var(--dur-fast) var(--ease);
        }

        .stat-link:hover {
            color: var(--brand-200);
        }

        /* Global Stats */
        .global-stats {
            padding: 1rem;
            background: var(--bg-elev);
            border: 1px dashed var(--border);
            border-radius: 12px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, var(--surface), var(--surface-2));
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            text-decoration: none;
            transition: all var(--dur-med) var(--ease);
            font-weight: 600;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            border-color: var(--brand-300);
            box-shadow: 0 8px 25px rgba(31, 94, 215, 0.15);
            color: var(--brand-200);
        }

        .quick-link--bunker {
            border-color: rgba(220, 38, 38, 0.3);
        }

        .quick-link--bunker:hover {
            border-color: #ef4444;
        }

        .quick-link--mod {
            border-color: rgba(245, 158, 11, 0.3);
        }

        .quick-link--mod:hover {
            border-color: #f59e0b;
        }

        .quick-link--admin {
            border-color: rgba(139, 92, 246, 0.3);
        }

        .quick-link--admin:hover {
            border-color: #8b5cf6;
        }

        /* Token Alert */
        .token-alert {
            margin: 1.5rem 0;
            padding: 1.5rem;
            background: linear-gradient(135deg, #064e3b, #022c22);
            border: 1px solid #10b981;
            border-radius: 12px;
            color: #ecfdf5;
        }

        .token-alert__header {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .token-alert__code {
            display: block;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            font-family: monospace;
            word-break: break-all;
            margin: 0.5rem 0;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .token-alert__note {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        /* Token Form */
        .token-form {
            margin-bottom: 2rem;
        }

        .token-form__grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }

        @media (max-width: 768px) {
            .token-form__grid {
                grid-template-columns: 1fr;
            }
        }

        .token-input,
        .token-date {
            padding: 0.75rem;
            background: var(--bg-deep);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.9rem;
        }

        .token-input:focus,
        .token-date:focus {
            outline: none;
            border-color: var(--brand-300);
            box-shadow: 0 0 0 3px rgba(138, 180, 248, 0.1);
        }

        /* Tokens Table */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .tokens-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
        }

        .tokens-table th {
            background: var(--bg-deep);
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: var(--brand-200);
            border-bottom: 1px solid var(--border);
        }

        .tokens-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        .tokens-table tr:last-child td {
            border-bottom: none;
        }

        .tokens-table tr:hover {
            background: rgba(138, 180, 248, 0.05);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .status-badge--active {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-badge--inactive {
            background: rgba(148, 163, 184, 0.2);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .token-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            color: var(--muted);
            font-style: italic;
            padding: 2rem !important;
        }

        /* Profile Form */
        .profile-form {
            max-width: 600px;
            background: linear-gradient(135deg, var(--surface), var(--surface-2));
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--brand-200);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--bg-deep);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 1rem;
            transition: all var(--dur-fast) var(--ease);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--brand-300);
            box-shadow: 0 0 0 3px rgba(138, 180, 248, 0.15);
        }

        .form-divider {
            height: 1px;
            background: var(--border);
            margin: 2rem 0;
        }

        .password-title {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text);
            font-size: 1.1rem;
        }

        .btn-full {
            width: 100%;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }

        /* Error List */
        .error-list {
            margin: 0;
            padding-left: 1rem;
        }

        .error-list li {
            margin-bottom: 0.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-container {
                padding: 1rem;
            }

            .profile-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .quick-links {
                grid-template-columns: 1fr;
            }

            .token-form__grid {
                grid-template-columns: 1fr;
            }

            .token-actions {
                flex-direction: column;
            }

            .profile-form {
                padding: 1.5rem;
            }
        }
    </style>
@endsection
@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div style="max-width:1100px; margin:0 auto; padding:1rem;">
        {{-- Título + rol actual --}}
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
            <h2 style="margin:0;">Mi Perfil</h2>
            <span title="Rol actual"
                  style="display:inline-block; padding:0.35rem 0.6rem; border-radius:999px;
                         background:#0f172a; color:#fff; border:1px solid rgba(255,255,255,.15); font-weight:600;">
                Rol: {{ strtoupper($user->role ?? 'user') }}
            </span>
        </div>

        {{-- Flashes --}}
        @if (session('status'))
            <div style="margin:1rem 0; padding:0.75rem; background:#dcfce7; border:1px solid #16a34a;">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div style="margin:1rem 0; padding:0.75rem; background:#fee2e2; border:1px solid #ef4444;">
                <ul style="margin:0; padding-left:1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Mostrar token plano de Bunkerlabs cuando se crea (solo una vez) --}}
        @if (session('bunker_token_plain'))
            <div style="margin:1rem 0; padding:0.75rem; background:#064e3b; color:#ecfdf5; border:1px solid #10b981; border-radius:8px;">
                <strong>Nuevo token Bunkerlabs:</strong>
                <span style="word-break:break-all;">{{ session('bunker_token_plain') }}</span>
                <div style="margin-top:0.4rem; opacity:.9;">Cópialo y guárdalo ahora. No volverá a mostrarse.</div>
            </div>
        @endif

        {{-- RESUMEN / STATS --}}
        <section style="margin:1.25rem 0;">
            <h3 style="margin:0 0 0.75rem 0;">Resumen</h3>

            {{-- Tarjetas en grid --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px;">
                {{-- Writeups enviados (pendientes) del usuario --}}
                <div style="background:#0f172a; color:#fff; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:0.9rem;">
                    <div style="opacity:.8; font-size:.9rem;">Writeups enviados (pendientes)</div>
                    <div style="font-size:2rem; font-weight:700; line-height:1;">
                        {{ $stats['writeups_pendientes_user'] ?? 0 }}
                    </div>
                    <div style="margin-top:8px;">
                        @if(($stats['writeups_pendientes_user'] ?? 0) > 0)
                            <a href="{{ route('dockerlabs.admin.writeups-temporal.index') }}" style="text-decoration:none; color:#93c5fd;">Revisar</a>
                        @endif
                    </div>
                </div>

                {{-- Writeups aprobados del usuario (si se puede asociar) --}}
                <div style="background:#0f172a; color:#fff; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:0.9rem;">
                    <div style="opacity:.8; font-size:.9rem;">Tus writeups aprobados</div>
                    <div style="font-size:2rem; font-weight:700; line-height:1;">
                        {{ $stats['writeups_aprobados_user'] ?? 0 }}
                    </div>
                    <div style="margin-top:8px;">
                        <a href="{{ route('dockerlabs.mis-writeups.index') }}" style="text-decoration:none; color:#93c5fd;">Ver mis writeups</a>
                    </div>
                </div>

                {{-- Máquinas enviadas por el usuario --}}
                <div style="background:#0f172a; color:#fff; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:0.9rem;">
                    <div style="opacity:.8; font-size:.9rem;">Máquinas enviadas</div>
                    <div style="font-size:2rem; font-weight:700; line-height:1;">
                        {{ $stats['maquinas_enviadas_user'] ?? 0 }}
                    </div>
                    <div style="margin-top:8px;">
                        <a href="{{ route('dockerlabs.enviar-maquina.form') }}" style="text-decoration:none; color:#93c5fd;">Enviar nueva</a>
                    </div>
                </div>

                {{-- Máquinas aprobadas del usuario (si hay trazabilidad) o global --}}
                <div style="background:#0f172a; color:#fff; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:0.9rem;">
                    <div style="opacity:.8; font-size:.9rem;">
                        {{ ($stats['maquinas_aprobadas_user_known'] ?? false) ? 'Tus máquinas aprobadas' : 'Máquinas aprobadas (global)' }}
                    </div>
                    <div style="font-size:2rem; font-weight:700; line-height:1;">
                        {{ $stats['maquinas_aprobadas_user_known'] ? ($stats['maquinas_aprobadas_user'] ?? 0) : ($stats['maquinas_aprobadas_global'] ?? 0) }}
                    </div>
                    @if(($user->isAdmin() || $user->isModerator()))
                        <div style="margin-top:8px;">
                            <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}" style="text-decoration:none; color:#93c5fd;">Ver recibidas</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen global (opcional, visible a admin/moderador) --}}
            @if($user->isAdmin() || $user->isModerator())
                <div style="margin-top:12px; padding:0.9rem; background:#0b1220; border:1px dashed rgba(255,255,255,.15); border-radius:10px; color:#e5e7eb;">
                    <strong>Global:</strong>
                    {{ ($stats['writeups_aprobados_global'] ?? 0) }} writeups aprobados ·
                    {{ ($stats['maquinas_aprobadas_global'] ?? 0) }} máquinas aprobadas ·
                    {{ ($stats['writeups_pendientes_global'] ?? 0) }} writeups pendientes
                </div>
            @endif
        </section>

        {{-- ACCESOS RÁPIDOS según rol --}}
        <section style="margin:1.5rem 0;">
            <h3 style="margin:0 0 0.75rem 0;">Accesos rápidos</h3>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                {{-- Todos los usuarios --}}
                <a href="{{ route('dockerlabs.mis-writeups.index') }}"
                   style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#1e293b; color:#fff; text-decoration:none;">
                    <i class="fas fa-file-alt"></i> Mis Writeups
                </a>
                <a href="{{ route('dockerlabs.enviar-maquina.form') }}"
                   style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#1e293b; color:#fff; text-decoration:none;">
                    <i class="fas fa-upload"></i> Enviar Máquina
                </a>

                {{-- Acceso a login Bunkerlabs (token) para cualquiera que tenga el link --}}
                <a href="{{ route('bunkerlabs.login') }}"
                   style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#9f1239; color:#fff; text-decoration:none;">
                    <i class="fas fa-shield-alt"></i> Login Bunkerlabs (token)
                </a>

                {{-- Moderación (admin o moderador) --}}
                @if($user->isAdmin() || $user->isModerator())
                    <a href="{{ route('dockerlabs.admin.writeups-temporal.index') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#334155; color:#fff; text-decoration:none;">
                        <i class="fas fa-hourglass-half"></i> Moderar Writeups Pendientes
                    </a>
                    <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#334155; color:#fff; text-decoration:none;">
                        <i class="fas fa-inbox"></i> Máquinas Recibidas
                    </a>
                @endif

                {{-- Admin exclusivo --}}
                @if($user->isAdmin())
                    <a href="{{ route('dockerlabs.admin.dashboard') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#475569; color:#fff; text-decoration:none;">
                        <i class="fas fa-cogs"></i> Agregar Máquina
                    </a>
                    <a href="{{ route('dockerlabs.profile.roles.index') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#475569; color:#fff; text-decoration:none;">
                        <i class="fas fa-users-cog"></i> Gestión de roles
                    </a>
                @endif
            </div>
        </section>

        {{-- BUNKERLABS: Gestión de tokens (solo admin) --}}
        @if($user->isAdmin())
            <section style="margin:1.5rem 0;">
                <h3 style="margin:0 0 0.75rem 0;">Bunkerlabs — Gestión de tokens</h3>

                {{-- Crear token --}}
                <form method="POST" action="{{ route('bunkerlabs.perfil.tokens.create') }}" style="margin-bottom:1rem;">
                    @csrf
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <input type="text" name="name" placeholder="Etiqueta (opcional)" style="padding:.5rem; flex:1; min-width:220px;">
                        <input type="datetime-local" name="expires_at" style="padding:.5rem;">
                        <button type="submit"
                                style="padding:.55rem 1rem; background:#1f2937; color:#fff; border:0; border-radius:6px; cursor:pointer;">
                            Crear token
                        </button>
                    </div>
                </form>

                {{-- Listado de tokens --}}
                @php($tokens = \App\Models\BunkerToken::orderByDesc('id')->limit(100)->get())
                <div style="overflow:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:680px;">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155; color:#e5e7eb;">ID</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155; color:#e5e7eb;">Nombre</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155; color:#e5e7eb;">Activo</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155; color:#e5e7eb;">Expira</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155; color:#e5e7eb;">Usado</th>
                                <th style="text-align:left; padding:.5rem; border-bottom:1px solid #334155; color:#e5e7eb;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $t)
                                <tr>
                                    <td style="padding:.5rem; color:#e5e7eb;">{{ $t->id }}</td>
                                    <td style="padding:.5rem; color:#e5e7eb;">{{ $t->name }}</td>
                                    <td style="padding:.5rem; color:#e5e7eb;">{{ $t->active ? 'Sí' : 'No' }}</td>
                                    <td style="padding:.5rem; color:#e5e7eb;">
                                        {{ $t->expires_at ? $t->expires_at->format('Y-m-d H:i') : '—' }}
                                    </td>
                                    <td style="padding:.5rem; color:#e5e7eb;">
                                        {{ $t->used_at ? $t->used_at->format('Y-m-d H:i') : '—' }}
                                    </td>
                                    <td style="padding:.5rem;">
                                        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                                            <form method="POST" action="{{ route('bunkerlabs.perfil.tokens.toggle', $t->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        style="padding:.35rem .65rem; border-radius:6px; background:#1e293b; color:#fff; border:0; cursor:pointer;">
                                                    {{ $t->active ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('bunkerlabs.perfil.tokens.delete', $t->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        style="padding:.35rem .65rem; border-radius:6px; background:#3b0d16; color:#fca5a5; border:1px solid #7a1631; cursor:pointer;">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding:.75rem; color:#9ca3af;">No hay tokens creados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- FORMULARIO PERFIL --}}
        <section style="margin:1.5rem 0;">
            <h3 style="margin:0 0 0.75rem 0;">Datos de la cuenta</h3>

            <form method="POST" action="{{ route('dockerlabs.profile.update') }}" style="max-width:600px;">
                @csrf
                @method('PUT')

                <div style="margin-bottom:1rem;">
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                           required style="width:100%; padding:0.5rem; margin-top:0.25rem;">
                </div>

                <div style="margin-bottom:1rem;">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                           required style="width:100%; padding:0.5rem; margin-top:0.25rem;">
                </div>

                <hr style="margin:1.5rem 0; opacity:0.3;">

                <p style="margin:0 0 0.5rem 0; font-weight:600;">Cambio de contraseña (opcional)</p>

                <div style="margin-bottom:1rem;">
                    <label for="current_password">Contraseña actual</label>
                    <input id="current_password" name="current_password" type="password"
                           style="width:100%; padding:0.5rem; margin-top:0.25rem;">
                </div>

                <div style="margin-bottom:1rem;">
                    <label for="new_password">Nueva contraseña</label>
                    <input id="new_password" name="new_password" type="password"
                           style="width:100%; padding:0.5rem; margin-top:0.25rem;">
                </div>

                <div style="margin-bottom:1rem;">
                    <label for="new_password_confirmation">Confirmar nueva contraseña</label>
                    <input id="new_password_confirmation" name="new_password_confirmation" type="password"
                           style="width:100%; padding:0.5rem; margin-top:0.25rem;">
                </div>

                <button type="submit"
                        style="padding:0.75rem 1.25rem; background:#1e293b; color:#fff; border:none; cursor:pointer; border-radius:8px;">
                    Guardar cambios
                </button>
            </form>
        </section>
    </div>
@endsection

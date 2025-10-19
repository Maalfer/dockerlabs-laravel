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
                            <a href="{{ route('admin.writeups-temporal.index') }}" style="text-decoration:none; color:#93c5fd;">Revisar</a>
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
                        <a href="{{ route('mis-writeups.index') }}" style="text-decoration:none; color:#93c5fd;">Ver mis writeups</a>
                    </div>
                </div>

                {{-- Máquinas enviadas por el usuario --}}
                <div style="background:#0f172a; color:#fff; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:0.9rem;">
                    <div style="opacity:.8; font-size:.9rem;">Máquinas enviadas</div>
                    <div style="font-size:2rem; font-weight:700; line-height:1;">
                        {{ $stats['maquinas_enviadas_user'] ?? 0 }}
                    </div>
                    <div style="margin-top:8px;">
                        <a href="{{ route('enviar-maquina.form') }}" style="text-decoration:none; color:#93c5fd;">Enviar nueva</a>
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
                            <a href="{{ route('admin.maquinas.recibidas') }}" style="text-decoration:none; color:#93c5fd;">Ver recibidas</a>
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
                <a href="{{ route('mis-writeups.index') }}"
                   style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#1e293b; color:#fff; text-decoration:none;">
                    <i class="fas fa-file-alt"></i> Mis Writeups
                </a>
                <a href="{{ route('enviar-maquina.form') }}"
                   style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#1e293b; color:#fff; text-decoration:none;">
                    <i class="fas fa-upload"></i> Enviar Máquina
                </a>

                {{-- Moderación (admin o moderador) --}}
                @if($user->isAdmin() || $user->isModerator())
                    <a href="{{ route('admin.writeups-temporal.index') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#334155; color:#fff; text-decoration:none;">
                        <i class="fas fa-hourglass-half"></i> Moderar Writeups Pendientes
                    </a>
                    <a href="{{ route('admin.maquinas.recibidas') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#334155; color:#fff; text-decoration:none;">
                        <i class="fas fa-inbox"></i> Máquinas Recibidas
                    </a>
                @endif

                {{-- Admin exclusivo --}}
                @if($user->isAdmin())
                    <a href="{{ route('admin') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#475569; color:#fff; text-decoration:none;">
                        <i class="fas fa-cogs"></i> Agregar Máquina
                    </a>
                    <a href="{{ route('profile.roles.index') }}"
                       style="display:inline-block; padding:0.5rem 0.8rem; border-radius:8px; background:#475569; color:#fff; text-decoration:none;">
                        <i class="fas fa-users-cog"></i> Gestión de roles
                    </a>
                @endif
            </div>
        </section>

        {{-- FORMULARIO PERFIL --}}
        <section style="margin:1.5rem 0;">
            <h3 style="margin:0 0 0.75rem 0;">Datos de la cuenta</h3>

            <form method="POST" action="{{ route('profile.update') }}" style="max-width:600px;">
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

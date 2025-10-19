<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>DockerLabs - @yield('title')</title>

    {{-- Cargar CSS vanilla desde public/css --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    {{-- Header --}}
    @include('partials.header')

    {{-- Enlace adicional: Mis Writeups, solo para usuarios logueados --}}
    @auth
        <nav class="nav" style="margin: 10px auto; max-width:1100px; padding: 0 1rem;">
            {{-- Botones de administración (solo visibles a los logueados con permisos) --}}
            
            {{-- Agregar Máquina: SOLO ADMIN --}}
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin') }}">
                    <i class="fas fa-cogs"></i> Agregar Máquina
                </a>
            @endif

            {{-- Pendientes: ADMIN o MODERADOR --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isModerator())
                <a href="{{ route('admin.writeups-temporal.index') }}">
                    <i class="fas fa-hourglass-half"></i> Pendientes
                </a>
            @endif

            {{-- Aprobados: ADMIN o MODERADOR --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isModerator())
                <a href="{{ route('admin.writeups.index') }}">
                    <i class="fas fa-check-circle"></i> Aprobados
                </a>
            @endif

            {{-- Recibidas: ADMIN o MODERADOR --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isModerator())
                <a href="{{ route('admin.maquinas.recibidas') }}">
                    <i class="fas fa-inbox"></i> Recibidas
                </a>
            @endif
        </nav>
    @endauth

    {{-- Contenido principal --}}
    <main>
        <div class="main-inner">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    @include('partials.footer')
</body>
</html>

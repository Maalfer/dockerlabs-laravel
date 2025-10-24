<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>DockerLabs - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body class="site">
    @include('partials.header')

    @auth
        <nav class="nav" style="margin: 10px auto; max-width:1100px; padding: 0 1rem;">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('dockerlabs.admin.dashboard') }}">
                    <i class="fas fa-cogs"></i> Agregar Máquina
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isModerator())
                <a href="{{ route('dockerlabs.admin.writeups-temporal.index') }}">
                    <i class="fas fa-hourglass-half"></i> Pendientes
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isModerator())
                <a href="{{ route('dockerlabs.admin.writeups.index') }}">
                    <i class="fas fa-check-circle"></i> Aprobados
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isModerator())
                <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}">
                    <i class="fas fa-inbox"></i> Recibidas
                </a>
            @endif
        </nav>
    @endauth

    <main class="site-main">
        <div class="main-inner">
            @yield('content')
        </div>
    </main>

    @include('partials.footer')
</body>
</html>

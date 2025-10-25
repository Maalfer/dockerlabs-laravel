{{-- resources/views/layouts/app-bunkerlabs.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>BunkerLabs - @yield('title')</title>

    {{-- Base styles (tema general) --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    {{-- Overrides morado/rosa para BunkerLabs --}}
    <link rel="stylesheet" href="{{ asset('css/bunkerlabs.css') }}">
</head>
<body class="site bunkerlabs">
    @include('partials.header')

    @auth
        @php
            $u = auth()->user();
            $isAdmin = $u && method_exists($u, 'isAdmin') ? $u->isAdmin() : false;
            $isModerator = $u && method_exists($u, 'isModerator') ? $u->isModerator() : false;
        @endphp

        <nav class="nav" style="margin: 10px auto; max-width:1100px; padding: 0 1rem; display:flex; gap:.75rem; flex-wrap:wrap;">
            @if($isAdmin)
                @if(Route::has('dockerlabs.admin.dashboard'))
                    <a href="{{ route('dockerlabs.admin.dashboard') }}">
                        <i class="fas fa-cogs"></i> Agregar Máquina
                    </a>
                @endif
            @endif

            @if($isAdmin || $isModerator)
                @if(Route::has('dockerlabs.admin.writeups.index'))
                    <a href="{{ route('dockerlabs.admin.writeups.index') }}">
                        <i class="fa-solid fa-book"></i> Revisar Writeups
                    </a>
                @endif

                @if(Route::has('dockerlabs.admin.writeups.temporal.index'))
                    <a href="{{ route('dockerlabs.admin.writeups.temporal.index') }}">
                        <i class="fa-regular fa-clock"></i> Writeups temporales
                    </a>
                @endif

                @if(Route::has('dockerlabs.admin.maquinas.recibidas'))
                    <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}">
                        <i class="fa-solid fa-server"></i> Máquinas recibidas
                    </a>
                @endif
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
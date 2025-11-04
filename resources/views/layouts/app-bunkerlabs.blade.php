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
            {{-- Eliminados en todos los casos:
                 - Agregar Máquina
                 - Revisar Writeups
                 - Máquinas recibidas
               Se mantiene el resto de enlaces válidos del layout. --}}
            @if($isAdmin || $isModerator)
                @if(Route::has('dockerlabs.admin.writeups.temporal.index'))
                    <a href="{{ route('dockerlabs.admin.writeups.temporal.index') }}">
                        <i class="fa-regular fa-clock"></i> Writeups temporales
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

<header class="site-header">
    <h1>DockerLabs</h1>
    <nav class="site-nav">
        <a href="/">Inicio</a>
        <a href="{{ route('enviar-maquina.form') }}">Enviar máquina</a>
        <a href="/">El Búnker</a>
        <a href="/">Opciones</a>
        <a href="{{ route('mis-writeups.index') }}">Mis Writeups</a>

        @guest
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Registro</a>
        @endguest

        @auth
            <div class="perfil-dd">
                <button id="perfil-btn" class="perfil-btn">
                    Perfil <i class="fa fa-caret-down" aria-hidden="true"></i>
                </button>
                <div id="perfil-menu" class="perfil-menu">
                    <a href="{{ route('profile.edit') }}">Ver / Editar perfil</a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('profile.roles.index') }}">Gestión de roles</a>
                    @endif

                    <div class="perfil-divider"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="perfil-logout">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            <script>
                (function () {
                    const btn = document.getElementById('perfil-btn');
                    const menu = document.getElementById('perfil-menu');

                    if (!btn || !menu) return;

                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
                    });

                    document.addEventListener('click', function () {
                        if (menu.style.display === 'block') {
                            menu.style.display = 'none';
                        }
                    });

                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') menu.style.display = 'none';
                    });
                })();
            </script>
        @endauth
    </nav>
</header>

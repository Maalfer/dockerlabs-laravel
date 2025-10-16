<header style="background:#1e293b; color:white; padding:1rem; text-align:center;">
    <h1>DockerLabs</h1>
    <nav>
        <a href="/" style="margin:0 10px; color:white;">Inicio</a>
        <a href="{{ route('enviar-maquina.form') }}" style="margin:0 10px; color:white;">
            Enviar máquina
        </a>
        <a href="/" style="margin:0 10px; color:white;">El Búnker</a>
        <a href="/" style="margin:0 10px; color:white;">Opciones</a>
        <a href="{{ route('mis-writeups.index') }}" style="margin:0 10px; color:white;">Mis Writeups</a>

        @guest
            <a href="{{ route('login') }}" style="margin:0 10px; color:white;">Login</a>
            <a href="{{ route('register') }}" style="margin:0 10px; color:white;">Registro</a>
        @endguest

        @auth

            {{-- Dropdown Perfil --}}
            <div id="perfil-dd" style="display:inline-block; position:relative; margin:0 10px;">
                <button id="perfil-btn"
                        style="background:none; border:1px solid rgba(255,255,255,.3); color:white; padding:0.35rem 0.6rem; cursor:pointer; border-radius:6px;">
                    Perfil <i class="fa fa-caret-down" aria-hidden="true"></i>
                </button>
                <div id="perfil-menu"
                     style="display:none; position:absolute; right:0; top:115%; background:#0f172a; border:1px solid rgba(255,255,255,.15); min-width:220px; border-radius:8px; overflow:hidden; z-index:50;">
                    <a href="{{ route('profile.edit') }}"
                       style="display:block; padding:0.6rem 0.8rem; color:#fff; text-decoration:none;">
                        Ver / Editar perfil
                    </a>
                    <div style="height:1px; background:rgba(255,255,255,.1);"></div>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit"
                                style="display:block; width:100%; text-align:left; padding:0.6rem 0.8rem; background:none; border:none; color:#fff; cursor:pointer;">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>

            {{-- Script para abrir/cerrar el menú --}}
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

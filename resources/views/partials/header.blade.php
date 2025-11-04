<header class="site-header">
    @php
        $isBunker = request()->is('bunkerlabs*') || str_contains(Route::currentRouteName() ?? '', 'bunkerlabs');
    @endphp
    <h1>{{ $isBunker ? 'BunkerLabs' : 'DockerLabs' }}</h1>
    <nav class="site-nav">
        <a href="{{ route('dockerlabs.home') }}">Inicio</a>
        <a href="{{ route('dockerlabs.enviar-maquina.form') }}">Enviar máquina</a>
        @php($bunker = session('bunkerlabs_authenticated'))
        <a href="{{ $bunker ? route('bunkerlabs.home') : route('bunkerlabs.login') }}" class="nav-link">
            El Bunker
        </a>
        <button type="button" id="open-opciones" class="nav-link">Opciones</button>


        <a href="{{ route('dockerlabs.mis-writeups.index') }}">Mis Writeups</a>
        @guest
            <a href="{{ route('dockerlabs.login') }}">Login</a>
            <a href="{{ route('dockerlabs.register') }}">Registro</a>
        @endguest
        @auth
            <div class="perfil-dd">
                <button id="perfil-btn" class="perfil-btn">
                    Perfil <i class="fa fa-caret-down" aria-hidden="true"></i>
                </button>
                <div id="perfil-menu" class="perfil-menu">
                    <a href="{{ route('dockerlabs.profile.edit') }}">Ver / Editar perfil</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('dockerlabs.profile.roles.index') }}">Gestión de roles</a>
                    @endif
                    <div class="perfil-divider"></div>
                    <form action="{{ route('dockerlabs.logout') }}" method="POST">
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

        <div id="opciones-modal" class="modal" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="opciones-title">
                <div class="modal-header">
                    <h3 id="opciones-title" class="modal-title">Opciones</h3>
                    <button type="button" class="modal-close" id="close-opciones" aria-label="Cerrar">✕</button>
                </div>
                <div class="modal-body">
                    <div class="options-list">
                        <section class="option">
                            <h4 class="option-heading">Guía para enviar</h4>
                            <p><a class="option-link" href="{{ route('dockerlabs.enviar-maquina.form') }}">Cómo enviar una máquina</a></p>
                        </section>
                        <section class="option">
                            <h4 class="option-heading">Legales</h4>
                            <p><a class="option-link" href="#">términos y condiciones</a></p>
                        </section>
                        <section class="option">
                            <h4 class="option-heading">Ayuda</h4>
                            <p><a class="option-link" href="{{ route('dockerlabs.home') }}">Cómo utilizar dockerlabs</a></p>
                        </section>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="close-opciones-2">Cerrar</button>
                </div>
            </div>
        </div>
        <script>
            (function(){
                var openBtn = document.getElementById('open-opciones');
                var modal = document.getElementById('opciones-modal');
                var closeBtns = [document.getElementById('close-opciones'), document.getElementById('close-opciones-2')];
                if (!openBtn || !modal) return;
                var card = modal.querySelector('.modal-card');
                function openModal(){
                    modal.classList.add('open');
                    modal.setAttribute('aria-hidden','false');
                    var focusable = card.querySelectorAll('a[href], button:not([disabled])');
                    if (focusable.length) focusable[0].focus();
                }
                function closeModal(){
                    modal.classList.remove('open');
                    modal.setAttribute('aria-hidden','true');
                    openBtn.focus();
                }
                openBtn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
                closeBtns.forEach(function(b){ if(b){ b.addEventListener('click', closeModal); }});
                modal.addEventListener('click', function(e){ if(e.target === modal){ closeModal(); }});
                document.addEventListener('keydown', function(e){ if(modal.classList.contains('open') && e.key === 'Escape'){ closeModal(); }});
                card.addEventListener('keydown', function(e){
                    if (!modal.classList.contains('open')) return;
                    if (e.key !== 'Tab') return;
                    var focusable = card.querySelectorAll('a[href], button:not([disabled])');
                    if (!focusable.length) return;
                    var first = focusable[0];
                    var last = focusable[focusable.length - 1];
                    if (e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); }
                    else if (!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); }
                });
            })();
        </script>

    </nav>
</header>

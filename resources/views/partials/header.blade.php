<header style="background:#1e293b; color:white; padding:1rem; text-align:center;">
    <h1>DockerLabs</h1>
    <nav>
        <a href="/" style="margin:0 10px; color:white;">Inicio</a>
        <a href="{{ route('enviar-maquina.form') }}" style="margin:0 10px; color:white;">
            Enviar máquina
        </a>

        <a href="/" style="margin:0 10px; color:white;">El Búnker</a>
        <a href="/" style="margin:0 10px; color:white;">Opciones</a>
        @guest
            <a href="{{ route('login') }}" style="margin:0 10px; color:white;">Login</a>
            <a href="{{ route('register') }}" style="margin:0 10px; color:white;">Registro</a>
        @endguest

        @auth
            <a href="{{ route('dashboard') }}" style="margin:0 10px; color:white;">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:none; border:none; color:white; cursor:pointer;">
                    Salir
                </button>
            </form>
        @endauth
    </nav>
</header>

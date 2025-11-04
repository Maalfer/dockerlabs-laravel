@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<style>
/* ===== ENHANCED LOGIN PAGE STYLES ===== */
.auth-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, var(--bg-deep), var(--surface-2));
  position: relative;
  overflow: hidden;
}

.auth-page::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 80%, rgba(36, 107, 240, 0.15) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(82, 141, 245, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 40% 40%, rgba(149, 192, 255, 0.08) 0%, transparent 50%);
  pointer-events: none;
}

.auth-page .container {
  background: linear-gradient(135deg, var(--surface), var(--surface-2));
  border: 2px solid var(--border);
  border-radius: 24px;
  padding: 3rem 2.5rem;
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(16px);
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 480px;
  margin: 0 auto;
  animation: slideInUp 0.6s var(--ease) both;
}

.auth-page .container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--brand-500), var(--brand-300), var(--brand-500));
  border-radius: 24px 24px 0 0;
}

.auth-page h2 {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  background: linear-gradient(135deg, var(--text), var(--brand-200));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  text-align: center;
  letter-spacing: -0.5px;
  animation: slideInUp 0.6s var(--ease) 0.1s both;
}

.auth-page p {
  text-align: center;
  margin-bottom: 2rem;
  opacity: 0.85;
  font-size: 1.1rem;
  line-height: 1.6;
  animation: slideInUp 0.6s var(--ease) 0.2s both;
}

.auth-page form {
  display: grid;
  gap: 1.5rem;
  animation: slideInUp 0.6s var(--ease) 0.3s both;
}

.auth-page label {
  display: grid;
  gap: 0.75rem;
  font-weight: 600;
  color: var(--brand-200);
  font-size: 0.95rem;
}

.auth-page .form-control {
  background: var(--bg-deep);
  border: 2px solid var(--border);
  border-radius: 12px;
  padding: 1rem 1.25rem;
  color: var(--text);
  font-size: 1rem;
  transition: all var(--dur-fast) var(--ease);
  width: 100%;
}

.auth-page .form-control:focus {
  border-color: var(--brand-300);
  box-shadow: 0 0 0 4px rgba(138, 180, 248, 0.2);
  background: var(--bg-elev);
  outline: none;
}

.auth-page .form-control::placeholder {
  color: var(--muted);
  opacity: 0.7;
}

.auth-page .alert {
  padding: 1rem 1.25rem;
  border-radius: 12px;
  border: 2px solid transparent;
  background: linear-gradient(135deg, var(--surface), var(--surface-2));
  color: var(--text);
  box-shadow: var(--shadow);
  margin-bottom: 1rem;
  animation: slideInUp 0.6s var(--ease) both;
}

.auth-page .alert-danger {
  border-color: rgba(239, 68, 68, 0.4);
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) inset;
  background: linear-gradient(135deg, rgba(63, 10, 10, 0.9), rgba(63, 10, 10, 0.75));
}

.auth-page .alert-info {
  border-color: rgba(59, 130, 246, 0.4);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) inset;
  background: linear-gradient(135deg, rgba(30, 58, 138, 0.9), rgba(30, 58, 138, 0.75));
}

.auth-page .form-options {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  margin: 0.5rem 0;
  animation: slideInUp 0.6s var(--ease) 0.4s both;
}

.auth-page .remember-me {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
  font-weight: 500;
  cursor: pointer;
}

.auth-page .remember-me input[type="checkbox"] {
  width: 18px;
  height: 18px;
  border: 2px solid var(--border);
  border-radius: 4px;
  background: var(--bg-deep);
  cursor: pointer;
  transition: all var(--dur-fast) var(--ease);
}

.auth-page .remember-me input[type="checkbox"]:checked {
  background: var(--brand-500);
  border-color: var(--brand-500);
}

.auth-page .link {
  color: var(--brand-300);
  text-decoration: none;
  font-weight: 500;
  transition: all var(--dur-fast) var(--ease);
}

.auth-page .link:hover {
  color: var(--brand-200);
  text-decoration: underline;
}

.auth-page .form-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-top: 1rem;
  animation: slideInUp 0.6s var(--ease) 0.5s both;
}

.auth-page .btn {
  flex: 1;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  transition: all var(--dur-fast) var(--ease);
  text-align: center;
  border: 2px solid transparent;
  cursor: pointer;
}

.auth-page .btn-primary {
  background: var(--grad-2);
  color: white;
  border-color: rgba(138, 180, 248, 0.5);
  box-shadow: 0 8px 25px rgba(31, 94, 215, 0.3);
}

.auth-page .btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(31, 94, 215, 0.4);
  filter: brightness(1.1);
}

.auth-page .btn-secondary {
  background: transparent;
  color: var(--text);
  border: 2px solid var(--border);
}

.auth-page .btn-secondary:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: var(--brand-300);
  transform: translateY(-2px);
}

.auth-page .bunker-link {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border);
  animation: slideInUp 0.6s var(--ease) 0.6s both;
}

.auth-page .bunker-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #9f1239, #be123c);
  color: white;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 700;
  transition: all var(--dur-fast) var(--ease);
  border: 2px solid rgba(190, 18, 60, 0.5);
  box-shadow: 0 8px 25px rgba(159, 18, 57, 0.3);
}

.auth-page .bunker-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(159, 18, 57, 0.4);
  filter: brightness(1.1);
  text-decoration: none;
  color: white;
}

/* Animation for form elements */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive adjustments for login page */
@media (max-width: 640px) {
  .auth-page {
    padding: 1rem;
  }
  
  .auth-page .container {
    padding: 2rem 1.5rem;
  }
  
  .auth-page h2 {
    font-size: 1.75rem;
  }
  
  .auth-page .form-options {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .auth-page .form-actions {
    flex-direction: column;
  }
  
  .auth-page .btn {
    width: 100%;
  }
}
</style>

<div class="auth-page">
    <div class="container">
        <h2>Iniciar sesión — DockerLabs</h2>
        <p>Accede con tu cuenta para gestionar writeups y tu perfil.</p>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-info" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('dockerlabs.login.post') }}">
            @csrf

            <label>
                Email
                <input type="email" name="email" class="form-control" required autofocus
                       value="{{ old('email') }}" placeholder="tu@email.com">
            </label>

            <label>
                Contraseña
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </label>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Recuérdame
                </label>

                <div>
                    <a href="{{ route('dockerlabs.register') }}" class="link">¿No tienes cuenta? Regístrate</a>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Entrar</button>
                <a href="{{ route('dockerlabs.home') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

       
    </div>
</div>
@endsection
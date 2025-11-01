@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<style>
/* ===== ENHANCED REGISTER PAGE STYLES ===== */
.register-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 2rem 1rem;
  background: var(--bg-deep);
  position: relative;
  overflow: hidden;
}

.register-page::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 80%, rgba(36, 107, 240, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(82, 141, 245, 0.05) 0%, transparent 50%);
  pointer-events: none;
}

.register-page .container {
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

.register-page .container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--brand-500), var(--brand-300), var(--brand-500));
  border-radius: 24px 24px 0 0;
}

.register-page h2 {
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

.register-page p {
  text-align: center;
  margin-bottom: 2rem;
  opacity: 0.85;
  font-size: 1.1rem;
  line-height: 1.6;
  animation: slideInUp 0.6s var(--ease) 0.2s both;
}

.register-page form {
  display: grid;
  gap: 1.5rem;
  animation: slideInUp 0.6s var(--ease) 0.3s both;
}

.register-page label {
  display: grid;
  gap: 0.75rem;
  font-weight: 600;
  color: var(--brand-200);
  font-size: 0.95rem;
}

.register-page .form-control {
  background: var(--bg-deep);
  border: 2px solid var(--border);
  border-radius: 12px;
  padding: 1rem 1.25rem;
  color: var(--text);
  font-size: 1rem;
  transition: all var(--dur-fast) var(--ease);
  width: 100%;
}

.register-page .form-control:focus {
  border-color: var(--brand-300);
  box-shadow: 0 0 0 4px rgba(138, 180, 248, 0.2);
  background: var(--bg-elev);
  outline: none;
}

.register-page .form-control::placeholder {
  color: var(--muted);
  opacity: 0.7;
}

.register-page .alert {
  padding: 1rem 1.25rem;
  border-radius: 12px;
  border: 2px solid transparent;
  background: linear-gradient(135deg, var(--surface), var(--surface-2));
  color: var(--text);
  box-shadow: var(--shadow);
  margin-bottom: 1rem;
  animation: slideInUp 0.6s var(--ease) both;
}

.register-page .alert-danger {
  border-color: rgba(239, 68, 68, 0.4);
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) inset;
  background: linear-gradient(135deg, rgba(63, 10, 10, 0.9), rgba(63, 10, 10, 0.75));
}

.register-page .alert-success {
  border-color: rgba(34, 197, 94, 0.4);
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12) inset;
  background: linear-gradient(135deg, rgba(5, 46, 28, 0.9), rgba(5, 46, 28, 0.75));
}

.register-page .form-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-top: 1rem;
  animation: slideInUp 0.6s var(--ease) 0.5s both;
}

.register-page .btn {
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

.register-page .btn-primary {
  background: var(--grad-2);
  color: white;
  border-color: rgba(138, 180, 248, 0.5);
  box-shadow: 0 8px 25px rgba(31, 94, 215, 0.3);
}

.register-page .btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(31, 94, 215, 0.4);
  filter: brightness(1.1);
}

.register-page .btn-secondary {
  background: transparent;
  color: var(--text);
  border: 2px solid var(--border);
}

.register-page .btn-secondary:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: var(--brand-300);
  transform: translateY(-2px);
}

.register-page .login-link {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border);
  animation: slideInUp 0.6s var(--ease) 0.6s both;
}

.register-page .login-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: transparent;
  color: var(--brand-300);
  text-decoration: none;
  border-radius: 12px;
  font-weight: 700;
  transition: all var(--dur-fast) var(--ease);
  border: 2px solid var(--border);
}

.register-page .login-btn:hover {
  background: rgba(138, 180, 248, 0.1);
  border-color: var(--brand-300);
  transform: translateY(-2px);
  text-decoration: none;
  color: var(--brand-200);
}

/* Password strength indicator */
.password-strength {
  margin-top: 0.5rem;
  display: grid;
  gap: 0.5rem;
}

.strength-bar {
  height: 4px;
  background: var(--border);
  border-radius: 2px;
  overflow: hidden;
}

.strength-fill {
  height: 100%;
  width: 0%;
  transition: all var(--dur-med) var(--ease);
  border-radius: 2px;
}

.strength-fill.weak {
  background: #ef4444;
  width: 33%;
}

.strength-fill.medium {
  background: #f59e0b;
  width: 66%;
}

.strength-fill.strong {
  background: #10b981;
  width: 100%;
}

.strength-text {
  font-size: 0.8rem;
  color: var(--muted);
  text-align: right;
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

/* Responsive adjustments for register page */
@media (max-width: 640px) {
  .register-page {
    padding: 1rem;
  }
  
  .register-page .container {
    padding: 2rem 1.5rem;
  }
  
  .register-page h2 {
    font-size: 1.75rem;
  }
  
  .register-page .form-actions {
    flex-direction: column;
  }
  
  .register-page .btn {
    width: 100%;
  }
}
</style>

<div class="register-page">
    <div class="container">
        <h2>Crear una cuenta — DockerLabs</h2>
        <p>Únete a nuestra comunidad para gestionar writeups y tu perfil.</p>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('dockerlabs.register.post') }}">
            @csrf

            <label>
                Nombre completo
                <input type="text" name="name" class="form-control" required autofocus
                       value="{{ old('name') }}" placeholder="Tu nombre completo">
            </label>

            <label>
                Correo electrónico
                <input type="email" name="email" class="form-control" required
                       value="{{ old('email') }}" placeholder="tu@email.com">
            </label>

            <label>
                Contraseña
                <input type="password" name="password" class="form-control" required 
                       placeholder="••••••••" id="password-input">
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <div class="strength-text" id="strength-text">Seguridad de la contraseña</div>
                </div>
            </label>

            <label>
                Confirmar contraseña
                <input type="password" name="password_confirmation" class="form-control" required 
                       placeholder="••••••••">
            </label>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrarse</button>
                <a href="{{ route('dockerlabs.home') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

        <div class="login-link">
            <a href="{{ route('dockerlabs.login') }}" class="login-btn">
                ¿Ya tienes cuenta? Inicia sesión
            </a>
        </div>
    </div>
</div>

<script>
// Password strength indicator
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password-input');
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    
    if (passwordInput && strengthFill && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let text = 'Débil';
            let className = 'weak';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            if (strength >= 4) {
                text = 'Fuerte';
                className = 'strong';
            } else if (strength >= 3) {
                text = 'Media';
                className = 'medium';
            } else if (password.length > 0) {
                text = 'Débil';
                className = 'weak';
            } else {
                text = 'Seguridad de la contraseña';
                className = '';
            }
            
            strengthFill.className = 'strength-fill ' + className;
            strengthText.textContent = text;
        });
    }
});
</script>
@endsection
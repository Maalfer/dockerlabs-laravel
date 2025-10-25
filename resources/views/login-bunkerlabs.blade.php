@extends('layouts.app-bunkerlabs')

@section('title', 'BunkerLabs — Iniciar sesión')

@section('content')
<style>
  .bunker-card{
    max-width: 540px;
    margin: 1.5rem auto;
    padding: 1.25rem 1.25rem 1rem;
    border-radius: 12px;
    background: #0f1116;
    border: 1px solid #212838;
    box-shadow: 0 6px 24px rgba(0,0,0,.35);
  }
  .bunker-title{ margin:0 0 .5rem; font-size:1.25rem; }
  .bunker-sub{ margin:0 0 1rem; opacity:.8; }
  .bunker-input{
    width:100%;
    background:#0b0d12;
    color:#e6e8ef;
    border:1px solid #2a3244;
    border-radius:10px;
    padding:.65rem .8rem;
    outline: none;
  }
  .bunker-input:focus{ border-color:#3a80f6; box-shadow: 0 0 0 3px rgba(58,128,246,.2); }
  .bunker-actions{ display:flex; gap:.6rem; align-items:center; flex-wrap:wrap; }
  .btn-primary{
    background:#3a80f6; border:1px solid #2b62ba; color:#fff;
    padding:.6rem 1rem; border-radius:10px; cursor:pointer;
  }
  .btn-secondary{
    background:#151a22; border:1px solid #2a3244; color:#e6e8ef;
    padding:.6rem 1rem; border-radius:10px; text-decoration:none; display:inline-block;
  }
</style>

<div class="bunker-card">
  <h2 class="bunker-title">Acceso a BunkerLabs</h2>
  <p class="bunker-sub">Introduce tu token de acceso para entrar al búnker.</p>

  @if ($errors->any())
    <div style="background:#5b1024; border:1px solid #7a1631; padding:.5rem .75rem; border-radius:8px; margin-bottom:1rem;">
      {{ $errors->first() }}
    </div>
  @endif

  @if (session('status'))
    <div style="background:#0f3b2f; border:1px solid #165c49; padding:.5rem .75rem; border-radius:8px; margin-bottom:1rem;">
      {{ session('status') }}
    </div>
  @endif

  <form method="POST" action="{{ route('bunkerlabs.login.submit') }}">
    @csrf
    <div style="margin-bottom:1rem;">
      <label for="token">Token de acceso:</label>
      <input class="bunker-input" type="text" id="token" name="token" required autocomplete="off" placeholder="Pega tu token aquí">
    </div>

    <div class="bunker-actions">
      <button type="submit" class="btn-primary">Entrar</button>
      <a href="{{ route('dockerlabs.home') }}" class="btn-secondary">Volver</a>
    </div>
  </form>

  @auth
    <hr style="border:none; border-top:1px solid #212838; margin:1rem 0;">
    <p style="margin:.5rem 0 0; opacity:.8;">
      ¿Eres admin? Gestiona tokens desde
      <a href="{{ route('bunkerlabs.perfil.tokens.index') }}">Perfil → Tokens</a>.
    </p>
  @endauth
</div>
@endsection

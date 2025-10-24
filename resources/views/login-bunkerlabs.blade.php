@extends('layouts.app')

@section('title', 'Acceso Bunkerlabs')

@section('content')
<style>
  body { background: #1a0a10; }
  .bunker-card{background:#2a0f1a;border:1px solid #3b0f1f;border-radius:10px;padding:1.25rem;color:#fff;max-width:420px;margin:1.5rem auto;box-shadow:0 8px 30px rgba(0,0,0,.35);}
  .bunker-input{width:100%;padding:.65rem .75rem;border-radius:8px;border:1px solid #542033;background:#1c0c13;color:#fff;margin-top:.35rem;}
  .bunker-btn{padding:.75rem 1.25rem;border:0;border-radius:8px;background:#e11d48;color:#fff;cursor:pointer;box-shadow:0 6px 18px rgba(225,29,72,.28);}
  .bunker-btn:hover{transform:translateY(-1px);}
  .bunker-back{color:#ffd1dc;text-decoration:none;font-size:.95rem;}
</style>

<div class="bunker-card">
  <h2 style="margin-top:0;">Acceso Bunkerlabs</h2>

  @if ($errors->any())
    <div style="background:#5b1024; border:1px solid #7a1631; padding:.5rem .75rem; border-radius:8px; margin-bottom:1rem;">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login.bunkerlabs.submit') }}">
    @csrf
    <div style="margin-bottom:1rem;">
      <label for="token">Token de acceso:</label>
      <input class="bunker-input" type="text" id="token" name="token" required autocomplete="off" placeholder="Pega tu token aqu�">
    </div>
    <button type="submit" class="bunker-btn">Entrar</button>
  </form>

  <div style="margin-top: 1rem; text-align:center;">
    <a class="bunker-back" href="{{ route('login') }}">Volver al login normal</a>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Writeups aprobados')

@section('content')
<div class="admin-page container">
  <h2>Writeups aprobados</h2>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Máquina</th>
          <th>Autor</th>
          <th>Enlace</th>
          <th>Fecha</th>
          @auth
            @if(auth()->user()->isAdmin())
              <th>Acciones</th>
            @endif
          @endauth
        </tr>
      </thead>
      <tbody>
        @forelse ($items as $item)
          <tr>
            <td>{{ $item->maquina?->nombre ?? '—' }}</td>
            <td>{{ $item->autor }}</td>
            <td>
              <a href="{{ $item->enlace }}" target="_blank" rel="noopener noreferrer">
                {{ \Illuminate\Support\Str::limit($item->enlace, 80) }}
              </a>
            </td>
            <td>{{ optional($item->created_at)->format('Y-m-d H:i') }}</td>

            @auth
              @if(auth()->user()->isAdmin())
                <td>
                  <form method="POST" action="{{ route('dockerlabs.admin.writeups.destroy', $item) }}"
                        onsubmit="return confirm('¿Eliminar este writeup? Esta acción no se puede deshacer.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            style="padding:0.35rem 0.6rem; background:#dc2626; color:white; border:none; border-radius:4px; cursor:pointer;">
                      Eliminar
                    </button>
                  </form>
                </td>
              @endif
            @endauth
          </tr>
        @empty
          <tr>
            <td colspan="5">Sin writeups aprobados todavía.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px;">
    {{ $items->links() }}
  </div>
</div>
@endsection

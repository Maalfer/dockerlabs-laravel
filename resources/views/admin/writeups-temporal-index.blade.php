@extends('layouts.app')

@section('title', 'Writeups temporales')

@section('content')
<div class="admin-page container">
  <h2>Writeups temporales</h2>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Máquina</th>
          <th>Autor</th>
          <th>Email</th>
          <th>Enlace</th>
          <th>Fecha</th>
          <th style="width:160px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($items as $item)
          <tr>
            <td>{{ $item->maquina?->nombre ?? '—' }}</td>
            <td>{{ $item->autor }}</td>
            <td>{{ $item->autor_email ?? '—' }}</td>
            <td>
              <a href="{{ $item->enlace }}" target="_blank" rel="noopener noreferrer">
                {{ \Illuminate\Support\Str::limit($item->enlace, 60) }}
              </a>
            </td>
            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
            <td>
              <form method="POST" action="{{ route('admin.writeups-temporal.approve', $item->id) }}" style="display:inline-block;">
                @csrf
                <button class="btn btn-xs btn-primary" type="submit">Aprobar</button>
              </form>

              <form method="POST" action="{{ route('admin.writeups-temporal.destroy', $item->id) }}" style="display:inline-block;" onsubmit="return confirm('¿Eliminar este envío?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-xs btn-danger" type="submit">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6">Sin writeups enviados todavía.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px;">
    {{ $items->links() }}
  </div>
</div>
@endsection

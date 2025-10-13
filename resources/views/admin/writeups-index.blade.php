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
          <th>M�quina</th>
          <th>Autor</th>
          <th>Enlace</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($items as $item)
          <tr>
            <td>{{ $item->maquina?->nombre ?? '\u2014' }}</td>
            <td>{{ $item->autor }}</td>
            <td>
              <a href="{{ $item->enlace }}" target="_blank" rel="noopener noreferrer">
                {{ \Illuminate\Support\Str::limit($item->enlace, 80) }}
              </a>
            </td>
            <td>{{ optional($item->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="4">Sin writeups aprobados todav�a.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px;">
    {{ $items->links() }}
  </div>
</div>
@endsection

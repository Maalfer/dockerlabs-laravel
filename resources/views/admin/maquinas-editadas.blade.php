@extends('layouts.app')

@section('title', 'Máquinas editadas')

@section('content')
<div class="admin-page container">
  <h2>Máquinas editadas</h2>

  @if ($ediciones->isEmpty())
    <p>No hay ediciones pendientes.</p>
  @else
    <table class="table" style="width:100%; border-collapse:collapse; margin-top:20px;">
      <thead>
        <tr>
          <th style="border-bottom:1px solid #ddd; padding:8px;">ID</th>
          <th style="border-bottom:1px solid #ddd; padding:8px;">Máquina</th>
          <th style="border-bottom:1px solid #ddd; padding:8px;">Propuesto</th>
          <th style="border-bottom:1px solid #ddd; padding:8px;">Actual</th>
          <th style="border-bottom:1px solid #ddd; padding:8px;">Usuario</th>
          <th style="border-bottom:1px solid #ddd; padding:8px;">Comentario</th>
          <th style="border-bottom:1px solid #ddd; padding:8px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($ediciones as $e)
          @php
            $m = $e->maquina;
            $c = $e->cambios ?? [];
          @endphp
          <tr>
            <td style="border-bottom:1px solid #ddd; padding:8px;">{{ $e->id }}</td>
            <td style="border-bottom:1px solid #ddd; padding:8px;">
              #{{ $m->id }} — {{ $m->nombre }}
            </td>
            <td style="border-bottom:1px solid #ddd; padding:8px; font-size:.9rem;">
              <div><strong>Nombre:</strong> {{ $c['nombre'] ?? '—' }}</div>
              <div><strong>Dificultad:</strong> {{ $c['dificultad'] ?? '—' }}</div>
              <div><strong>Enlace:</strong>
                @if(!empty($c['enlace_descarga']))
                  <a href="{{ $c['enlace_descarga'] }}" target="_blank" rel="noopener">Descarga</a>
                @else
                  —
                @endif
              </div>
              <div><strong>Descripción:</strong> {{ \Illuminate\Support\Str::limit($c['descripcion'] ?? '—', 140) }}</div>
            </td>
            <td style="border-bottom:1px solid #ddd; padding:8px; font-size:.9rem;">
              <div><strong>Nombre:</strong> {{ $m->nombre }}</div>
              <div><strong>Dificultad:</strong> {{ $m->dificultad }}</div>
              <div><strong>Enlace:</strong>
                @if($m->enlace_descarga)
                  <a href="{{ $m->enlace_descarga }}" target="_blank" rel="noopener">Descarga</a>
                @else
                  —
                @endif
              </div>
              <div><strong>Descripción:</strong> {{ \Illuminate\Support\Str::limit($m->descripcion, 140) }}</div>
            </td>
            <td style="border-bottom:1px solid #ddd; padding:8px;">
              {{ optional($e->user)->name ?? '—' }}
            </td>
            <td style="border-bottom:1px solid #ddd; padding:8px;">
              {{ $e->comentario ?? '—' }}
            </td>
            <td style="border-bottom:1px solid #ddd; padding:8px;">
              <form action="{{ route('dockerlabs.admin.maquinas-editadas.approve', $e->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
              </form>

              <form action="{{ route('dockerlabs.admin.maquinas-editadas.destroy', $e->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Rechazar esta edición?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Rechazar</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div style="margin-top:20px;">
      {{ $ediciones->links() }}
    </div>
  @endif

  <div style="margin-top:24px;">
    <a href="{{ route('dockerlabs.admin.maquinas.recibidas') }}" class="btn btn-outline">← Volver a “Máquinas recibidas”</a>
  </div>
</div>
@endsection

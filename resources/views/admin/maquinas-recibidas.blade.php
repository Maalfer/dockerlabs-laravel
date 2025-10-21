@extends('layouts.app')

@section('title', 'Máquinas recibidas')

@section('content')
<div class="admin-page container">
    <h2>Máquinas recibidas</h2>

    @if ($maquinas->isEmpty())
        <p>No se han recibido máquinas todavía.</p>
    @else
        <table class="table" style="width:100%; border-collapse:collapse; margin-top:20px;">
            <thead>
                <tr>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">ID</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Nombre</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Dificultad</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Autor</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Fecha creación</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Writeup</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Enviado</th>
                    <th style="border-bottom:1px solid #ddd; padding:8px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maquinas as $m)
                    <tr>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">{{ $m->id }}</td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">{{ $m->nombre_maquina }}</td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">{{ ucfirst($m->dificultad) }}</td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            {{ $m->autor_nombre }}<br>
                            @if($m->autor_enlace)
                                <a href="{{ $m->autor_enlace }}" target="_blank" rel="noopener">Perfil</a>
                            @endif
                        </td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            {{ $m->fecha_creacion ? \Carbon\Carbon::parse($m->fecha_creacion)->format('d/m/Y') : '-' }}
                        </td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            @if($m->writeup)
                                <a href="{{ $m->writeup }}" target="_blank" rel="noopener">Ver writeup</a>
                            @else
                                -
                            @endif
                        </td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            {{ $m->created_at->diffForHumans() }}
                        </td>
                        <td style="border-bottom:1px solid #ddd; padding:8px;">
                            <form method="POST" action="{{ route('admin.maquinas.recibidas.prefill', $m->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Aprobar Máquina</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:20px;">
            {{ $maquinas->links() }}
        </div>
    @endif
</div>
@endsection

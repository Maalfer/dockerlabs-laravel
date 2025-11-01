@extends('layouts.app')

@section('title', 'Writeups Aprobados')

@section('content')
<div class="writeups-page">
    <div class="writeups-header">
        <h1 class="writeups-title">Writeups Aprobados</h1>
        <p class="writeups-subtitle">Lista completa de writeups verificados y aprobados por la comunidad</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="writeups-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $items->total() }}</div>
                <div class="stat-label">Total Writeups</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $items->groupBy('autor')->count() }}</div>
                <div class="stat-label">Autores Únicos</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-server"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $items->groupBy('maquina_id')->count() }}</div>
                <div class="stat-label">Máquinas Cubiertas</div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="writeups-table">
            <thead>
                <tr>
                    <th>
                        <i class="fas fa-server"></i>
                        Máquina
                    </th>
                    <th>
                        <i class="fas fa-user"></i>
                        Autor
                    </th>
                    <th>
                        <i class="fas fa-link"></i>
                        Enlace
                    </th>
                    <th>
                        <i class="fas fa-calendar"></i>
                        Fecha
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="writeup-row">
                        <td class="machine-cell">
                            <div class="machine-info">
                                <strong>{{ $item->maquina?->nombre ?? '—' }}</strong>
                                @if($item->maquina?->dificultad)
                                    <span class="difficulty-badge {{ strtolower($item->maquina->dificultad) }}">
                                        {{ $item->maquina->dificultad }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="author-cell">
                            <div class="author-info">
                                <i class="fas fa-user-circle"></i>
                                {{ $item->autor }}
                            </div>
                        </td>
                        <td class="link-cell">
                            <a href="{{ $item->enlace }}" target="_blank" rel="noopener noreferrer" class="writeup-link">
                                <i class="fas fa-external-link-alt"></i>
                                {{ \Illuminate\Support\Str::limit($item->enlace, 50) }}
                            </a>
                        </td>
                        <td class="date-cell">
                            <div class="date-info">
                                {{ $item->created_at?->format('M d, Y') }}
                                <small>{{ $item->created_at?->diffForHumans() }}</small>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="4" class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <h3>Sin writeups aprobados todavía</h3>
                            <p>Los writeups aparecerán aquí una vez sean aprobados por los moderadores.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Mostrando {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} de {{ $items->total() }} writeups
            </div>
            <div class="pagination-links">
                {{ $items->links() }}
            </div>
        </div>
    @endif
</div>

<style>
/* Writeups Page Styles */
.writeups-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.writeups-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem;
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border-radius: 16px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
}

.writeups-title {
    margin: 0 0 0.5rem 0;
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--text), var(--brand-200));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.writeups-subtitle {
    margin: 0;
    color: var(--muted);
    font-size: 1.1rem;
}

/* Stats Section */
.writeups-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 94, 215, 0.15);
    border-color: var(--brand-300);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: var(--grad-2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: var(--muted);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
}

/* Table Styles */
.table-container {
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.writeups-table {
    width: 100%;
    border-collapse: collapse;
}

.writeups-table thead {
    background: var(--bg-deep);
}

.writeups-table th {
    padding: 1.25rem 1.5rem;
    text-align: left;
    font-weight: 700;
    color: var(--brand-200);
    border-bottom: 1px solid var(--border);
}

.writeups-table th i {
    margin-right: 0.5rem;
    opacity: 0.7;
}

.writeup-row {
    border-bottom: 1px solid var(--border);
    transition: background-color 0.2s ease;
}

.writeup-row:hover {
    background: rgba(138, 180, 248, 0.05);
}

.writeup-row:last-child {
    border-bottom: none;
}

.writeups-table td {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
}

/* Cell Styles */
.machine-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.difficulty-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.fácil, .facil, .easy {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.medio, .medium {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.difícil, .dificil, .hard {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.author-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.author-info i {
    color: var(--brand-300);
}

.writeup-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(138, 180, 248, 0.1);
    border: 1px solid rgba(138, 180, 248, 0.2);
    border-radius: 8px;
    color: var(--brand-300);
    text-decoration: none;
    transition: all 0.2s ease;
}

.writeup-link:hover {
    background: rgba(138, 180, 248, 0.2);
    border-color: var(--brand-300);
    color: var(--brand-200);
}

.date-info {
    display: flex;
    flex-direction: column;
}

.date-info small {
    color: var(--muted);
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

/* Empty State */
.empty-row td {
    padding: 3rem 2rem;
    text-align: center;
}

.empty-state i {
    font-size: 3rem;
    color: var(--muted);
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    margin: 0 0 0.5rem 0;
    color: var(--text);
    font-size: 1.25rem;
}

.empty-state p {
    margin: 0;
    color: var(--muted);
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
}

.pagination-info {
    color: var(--muted);
    font-weight: 600;
}

/* Alert */
.alert-success {
    background: linear-gradient(135deg, rgba(5, 46, 28, 0.9), rgba(5, 46, 28, 0.7));
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #d1fae5;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success i {
    color: #4ade80;
}

/* Responsive */
@media (max-width: 768px) {
    .writeups-page {
        padding: 1rem;
    }

    .writeups-header {
        padding: 1.5rem 1rem;
    }

    .writeups-title {
        font-size: 2rem;
    }

    .writeups-stats {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 1.25rem;
    }

    .writeups-table th,
    .writeups-table td {
        padding: 1rem;
    }

    .pagination-wrapper {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

@media (max-width: 640px) {
    .table-container {
        border-radius: 8px;
        overflow-x: auto;
    }

    .writeups-table {
        min-width: 600px;
    }
}
</style>
@endsection
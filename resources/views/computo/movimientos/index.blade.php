@extends('layouts.master')

@section('title', 'Movimientos Computados')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">

@section('content')
<div class="container-xxl py-4">
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-up-right fs-5 text-primary"></i>
            <h2 class="h5 mb-0">Movimientos de Equipos</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('computed_movements.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo movimiento
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Equipo</label>
                    <select name="computation_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($computations as $comp)
                            <option value="{{ $comp->id }}" @selected(request('computation_id') == $comp->id)>
                                {{ $comp->requisition }} - {{ $comp->brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Persona que recibe</label>
                    <input type="text" name="delivered_to" value="{{ request('delivered_to') }}" class="form-control" placeholder="Buscar persona">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Área</label>
                    <input type="text" name="area" value="{{ request('area') }}" class="form-control" placeholder="Buscar área">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Responsable</label>
                    <input type="text" name="taken_by" value="{{ request('taken_by') }}" class="form-control" placeholder="Buscar responsable">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sede</label>
                    <input type="text" name="seat" value="{{ request('seat') }}" class="form-control" placeholder="Buscar sede">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Buscar equipo</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Marca, requisición o serial">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('computed_movements.index') }}" class="btn btn-outline-secondary"><i class="bi bi-eraser"></i> Limpiar</a>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Equipo</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Entregado a</th>
                        <th>Área</th>
                        <th>Responsable</th>
                        <th>Sede</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($movements as $m)
                    <tr>
                        <td>{{ $m->id }}</td>
                        <td>
                            {{ $m->computation->brand ?? '' }}<br>
                            <small class="text-muted">{{ $m->computation->requisition ?? '—' }}</small>
                        </td>
                        <td>{{ $m->movement_date }}</td>
                        <td><span class="badge bg-warning text-dark text-uppercase">{{ $m->type }}</span></td>
                        <td>{{ $m->amount }}</td>
                        <td>{{ $m->delivered_to ?? '—' }}</td>
                        <td>{{ $m->area ?? '—' }}</td>
                        <td>{{ $m->taken_by ?? '—' }}</td>
                        <td>{{ $m->seat ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('computed_movements.edit', $m) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No se encontraron movimientos.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer paginación: solo sobre $movements --}}
        @if (method_exists($movements, 'hasPages') && $movements->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Mostrando {{ $movements->firstItem() }}–{{ $movements->lastItem() }} de {{ $movements->total() }}
                </div>
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

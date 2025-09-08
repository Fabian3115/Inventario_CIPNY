@extends('layouts.master')

@section('title', 'Equipos')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">

@section('content')
<div class="container-xxl py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-pc-display-horizontal fs-5 text-primary"></i>
            <h2 class="h5 mb-0">Equipos</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('computations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo equipo
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-12 col-md-3">
                    <label class="form-label">Requisición</label>
                    <input type="text" name="requisition" value="{{ request('requisition') }}" class="form-control" placeholder="Buscar requisición">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Marca</label>
                    <input type="text" name="brand" value="{{ request('brand') }}" class="form-control" placeholder="Buscar marca">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Serial (S/N)</label>
                    <input type="text" name="serial" value="{{ request('serial') }}" class="form-control" placeholder="Buscar serial">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Tipo</label>
                    <input type="text" name="type" value="{{ request('type') }}" class="form-control" placeholder="Ej: Portátil, Escritorio...">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Búsqueda rápida</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Requisición, marca, serial o tipo">
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('computations.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-eraser"></i> Limpiar
                </a>
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
                        <th>Requisición</th>
                        <th>Marca</th>
                        <th>Serial (S/N)</th>
                        <th>Tipo</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($computations as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td>{{ $c->requisition }}</td>
                        <td>{{ $c->brand }}</td>
                        <td>{{ $c->serial }}</td> {{-- usamos alias para `serial_s/n` --}}
                        <td>
                            @if($c->type)
                                <span class="badge bg-secondary">{{ $c->type }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $c->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('computations.edit', $c) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No se encontraron equipos.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($computations->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Mostrando {{ $computations->firstItem() }}–{{ $computations->lastItem() }} de {{ $computations->total() }}
                </div>
                {{ $computations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

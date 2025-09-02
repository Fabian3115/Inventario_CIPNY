@extends('layouts.master')

@section('title', 'Listado de Movimientos')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">

@section('content')

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Movimiento guardado',
                    text: @json(session('success')),
                    timer: 2200,
                    showConfirmButton: false
                });
            });
        </script>
    @endif


    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <h4 class="mb-0">Movimientos Registrados</h4>
                    <div class="d-flex gap-2">
                        {{-- Importar Excel --}}
                        <a href="{{ route('movements.import.form') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-upload"></i> Importar desde Excel
                        </a>
                        {{-- Exportar respetando los filtros actuales --}}
                        <a href="{{ route('movements.export', request()->query()) }}" class="btn btn-outline-light">
                            <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                        </a>
                        <a href="{{ route('movements.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Nuevo Movimiento
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- FILTROS --}}
                <form method="GET" action="{{ route('movements.index') }}" class="row g-3 mb-3">
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select">
                            <option value="">Todos</option>
                            <option value="entrada" {{ request('type') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="salida" {{ request('type') == 'salida' ? 'selected' : '' }}>Salida</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Producto</label>
                        <select name="product_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}"
                                    {{ (string) $p->id === request('product_id') ? 'selected' : '' }}>
                                    {{ $p->code }} - {{ $p->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Requisición</label>
                        <input type="text" name="requisition" class="form-control" value="{{ request('requisition') }}"
                            placeholder="Buscar # requisición">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Área</label>
                        <input type="text" name="area" class="form-control" value="{{ request('area') }}"
                            placeholder="Ej: Bodega A">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-12 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                        <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>

                {{-- ALERTAS --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- TABLA --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Requisición</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Entregado a</th>
                                <th>Área</th>
                                <th>Sacado por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $movement)
                                <tr>
                                    <td>{{ $movement->id }}</td>
                                    <td>
                                        {{ $movement->product->description ?? '—' }}
                                        <small class="text-muted d-block">Cod:
                                            {{ $movement->product->code ?? '—' }}</small>
                                    </td>
                                    <td>{{ $movement->requisition ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $movement->type === 'entrada' ? 'success' : 'danger' }}">
                                            {{ ucfirst($movement->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $movement->amount }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movement->date_products)->format('Y-m-d') }}</td>
                                    <td>{{ $movement->delivered_to ?? '—' }}</td>
                                    <td>{{ $movement->area ?? '—' }}</td>
                                    <td>{{ $movement->taken_by ?? '—' }}</td>
                                    <td class="d-flex gap-1">
                                        {{-- Si luego haces vista show en modal o página --}}
                                        {{-- <a href="{{ route('movements.show', $movement) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a> --}}
                                        <a href="{{ route('movements.edit', $movement->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('movements.destroy', $movement->id) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este movimiento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">No hay movimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación con filtros persistentes --}}
                <div class="mt-3">
                    {{ $movements->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

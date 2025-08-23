@extends('layouts.master')

@section('content')
    {{-- Header + breadcrumb --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-up fs-5 text-danger"></i>
            <h2 class="h5 mb-0">Salidas</h2>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Salidas</li>
            </ol>
        </nav>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-2">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Salidas (filtradas)</div>
                        <div class="h4 mb-0">{{ number_format($stats['count'] ?? 0) }}</div>
                    </div>
                    <i class="bi bi-clipboard-minus fs-2 text-danger"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Cantidad total</div>
                        <div class="h4 mb-0">{{ number_format($stats['sum_amount'] ?? 0, 2) }}</div>
                    </div>
                    <i class="bi bi-dash-circle fs-2 text-danger"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Hoy</div>
                        <div class="h4 mb-0">{{ number_format($stats['today'] ?? 0) }}</div>
                    </div>
                    <i class="bi bi-calendar-day fs-2 text-info"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('movements.out.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted">Buscar (código o descripción)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="Ej: 1001 o teclado">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted">Producto</label>
                    <select name="product_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} — {{ $p->description }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- NUEVO: Área --}}
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Área</label>
                    <select name="area" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($areas ?? [] as $a)
                            <option value="{{ $a }}" {{ request('area') === $a ? 'selected' : '' }}>
                                {{ $a }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Entregado a</label>
                    <input list="receivers_list" type="text" name="delivered_to" value="{{ request('delivered_to') }}"
                        class="form-control" placeholder="Persona que recibe">
                    <datalist id="receivers_list">
                        @foreach ($receivers ?? [] as $d)
                            <option value="{{ $d }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Registrado por</label>
                    <input type="text" name="taken_by" value="{{ request('taken_by') }}" class="form-control"
                        placeholder="Usuario">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Ordenar por</label>
                    @php $sort = request('sort','date_products'); @endphp
                    <select name="sort" class="form-select">
                        <option value="date_products" {{ $sort === 'date_products' ? 'selected' : '' }}>Fecha</option>
                        <option value="p_code" {{ $sort === 'p_code' ? 'selected' : '' }}>Código</option>
                        <option value="p_description" {{ $sort === 'p_description' ? 'selected' : '' }}>Descripción</option>
                        <option value="amount" {{ $sort === 'amount' ? 'selected' : '' }}>Cantidad</option>
                        <option value="area" {{ $sort === 'area' ? 'selected' : '' }}>Área</option> {{-- NUEVO --}}
                        <option value="delivered_to" {{ $sort === 'delivered_to' ? 'selected' : '' }}>Entregado a</option>
                        <option value="taken_by" {{ $sort === 'taken_by' ? 'selected' : '' }}>Registrado por</option>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Dirección</label>
                    @php $dir = request('dir','desc'); @endphp
                    <select name="dir" class="form-select">
                        <option value="asc" {{ $dir === 'asc' ? 'selected' : '' }}>Asc</option>
                        <option value="desc" {{ $dir === 'desc' ? 'selected' : '' }}>Desc</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-between mt-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        <a href="{{ route('movements.out.index') }}" class="btn btn-outline-secondary"><i
                                class="bi bi-arrow-counterclockwise me-1"></i> Limpiar</a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('movements.out.create') }}" class="btn btn-danger"><i
                                class="bi bi-plus-circle me-1"></i> Nueva salida</a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('movements.out.export') }}" class="btn btn-success"><i
                                class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar Excel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light position-sticky" style="top:0; z-index:1;">
                    <tr>
                        <th style="width:120px">Fecha</th>
                        <th style="width:120px">Código</th>
                        <th>Producto</th>
                        <th class="text-end" style="width:140px">Cantidad</th>
                        <th style="width:120px">Unidad</th>
                        <th style="width:160px">Almacén</th>
                        <th style="width:160px">Área</th> {{-- NUEVO --}}
                        <th style="width:200px">Entregado a</th>
                        <th style="width:160px">Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exits as $e)
                        <tr>
                            <td><span class="text-muted small"><i
                                        class="bi bi-calendar3 me-1"></i>{{ optional($e->date_products)->format('Y-m-d') ?? $e->date_products }}</span>
                            </td>
                            <td><span class="badge rounded-pill bg-light border text-dark">{{ $e->p_code }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $e->p_description }}</div>
                                <div class="text-muted small">ID #{{ $e->product_id }}</div>
                            </td>
                            <td class="text-end">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-dash-lg me-1"></i>{{ number_format($e->amount, 2) }}
                                </span>
                            </td>
                            <td><span
                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">{{ $e->p_extent ?? '—' }}</span>
                            </td>
                            <td><span class="badge bg-info-subtle text-info border border-info-subtle"><i
                                        class="bi bi-building me-1"></i>{{ $e->p_warehouse ?? '—' }}</span></td>
                            <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i
                                        class="bi bi-diagram-3 me-1"></i>{{ $e->area ?? '—' }}</span></td>
                            {{-- NUEVO --}}
                            <td>{{ $e->delivered_to ?? '—' }}</td>
                            <td>{{ $e->taken_by ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="text-center py-5">
                                    <div class="mb-2">
                                        <i class="bi bi-inboxes fs-1 text-muted"></i>
                                    </div>
                                    <h6 class="text-muted">No hay salidas que coincidan con tu búsqueda.</h6>
                                    <div class="mt-2">
                                        <a href="{{ route('movements.out.create') }}" class="btn btn-danger btn-sm"><i
                                                class="bi bi-plus-circle me-1"></i> Registrar salida</a>
                                        <a href="{{ route('movements.out.index') }}"
                                            class="btn btn-outline-secondary btn-sm"><i
                                                class="bi bi-arrow-counterclockwise me-1"></i> Limpiar filtros</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($exits instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        Mostrando {{ $exits->firstItem() }}–{{ $exits->lastItem() }} de {{ $exits->total() }}
                    </div>
                    <div>
                        {{ $exits->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

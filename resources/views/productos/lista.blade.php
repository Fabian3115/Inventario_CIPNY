@extends('layouts.master')

@section('content')
{{-- Header + breadcrumb --}}
<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
  <div class="d-flex align-items-center gap-2">
    <i class="bi bi-box-seam fs-5 text-primary"></i>
    <h2 class="h5 mb-0">Productos</h2>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Listado</li>
    </ol>
  </nav>
</div>

{{-- KPIs (opcionales) --}}
@if(!empty($stats))
<div class="row g-3 mb-2">
  <div class="col-12 col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Total productos</div>
          <div class="h4 mb-0">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>
        <i class="bi bi-collection fs-2 text-primary"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">En stock (&gt; 0)</div>
          <div class="h4 mb-0">{{ number_format($stats['in_stock'] ?? 0) }}</div>
        </div>
        <i class="bi bi-check2-circle fs-2 text-success"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Agotados (= 0)</div>
          <div class="h4 mb-0">{{ number_format($stats['out_stock'] ?? 0) }}</div>
        </div>
        <i class="bi bi-exclamation-octagon fs-2 text-danger"></i>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Filtros / acciones --}}
<div class="card shadow-sm border-0 mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-end">
      <div class="col-12 col-md-4">
        <label class="form-label small text-muted">Buscar</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Código o descripción…">
        </div>
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label small text-muted">Categoría</label>
        <select name="category" class="form-select">
          <option value="">Todas</option>
          @foreach(($categories ?? []) as $cat)
            <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected':'' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label small text-muted">Almacén</label>
        <select name="warehouse" class="form-select">
          <option value="">Todos</option>
          @foreach(($warehouses ?? []) as $wh)
            <option value="{{ $wh }}" {{ request('warehouse')===$wh ? 'selected':'' }}>{{ $wh }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label small text-muted">Ordenar por</label>
        <select name="sort" class="form-select">
          @php $sort = request('sort','date_products'); @endphp
          <option value="date_products" {{ $sort==='date_products'?'selected':'' }}>Fecha</option>
          <option value="code" {{ $sort==='code'?'selected':'' }}>Código</option>
          <option value="description" {{ $sort==='description'?'selected':'' }}>Descripción</option>
          <option value="stock" {{ $sort==='stock'?'selected':'' }}>Stock</option>
          <option value="categories" {{ $sort==='categories'?'selected':'' }}>Categoría</option>
          <option value="warehouse" {{ $sort==='warehouse'?'selected':'' }}>Almacén</option>
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label small text-muted">Dirección</label>
        <select name="dir" class="form-select">
          @php $dir = request('dir','desc'); @endphp
          <option value="asc"  {{ $dir==='asc'?'selected':'' }}>Asc</option>
          <option value="desc" {{ $dir==='desc'?'selected':'' }}>Desc</option>
        </select>
      </div>

      <div class="col-12 d-flex gap-2 justify-content-between mt-2">
        <div class="d-flex gap-2">
          <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filtrar</button>
          <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar</a>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('products.create') }}" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Nuevo</a>
          {{-- <a href="{{ route('products.export') }}" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i> Exportar</a> --}}
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
          <th style="width:110px">
            @php
              $is = request('sort')==='code';
              $next = ($is && request('dir')==='asc') ? 'desc':'asc';
              $q = array_merge(request()->all(), ['sort'=>'code','dir'=>$next]);
            @endphp
            <a href="{{ route('products.index', $q) }}" class="text-decoration-none">
              Código
              @if($is)<i class="bi bi-caret-{{ request('dir')==='asc'?'up':'down' }}-fill ms-1"></i>@endif
            </a>
          </th>
          <th>Descripción</th>
          <th class="text-end" style="width:120px">
            @php
              $is = request('sort')==='stock';
              $next = ($is && request('dir')==='asc') ? 'desc':'asc';
              $q = array_merge(request()->all(), ['sort'=>'stock','dir'=>$next]);
            @endphp
            <a href="{{ route('products.index', $q) }}" class="text-decoration-none">
              Stock
              @if($is)<i class="bi bi-caret-{{ request('dir')==='asc'?'up':'down' }}-fill ms-1"></i>@endif
            </a>
          </th>
          <th style="width:120px">Unidad</th>
          <th style="width:160px">Categoría</th>
          <th style="width:160px">Almacén</th>
          <th style="width:140px">
            @php
              $is = request('sort')==='date_products' || !request('sort');
              $next = ($is && request('dir')==='asc') ? 'desc':'asc';
              $q = array_merge(request()->all(), ['sort'=>'date_products','dir'=>$next]);
            @endphp
            <a href="{{ route('products.index', $q) }}" class="text-decoration-none">
              Fecha
              @if($is)<i class="bi bi-caret-{{ request('dir')==='asc'?'up':'down' }}-fill ms-1"></i>@endif
            </a>
          </th>
          <th class="text-end" style="width:130px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
        <tr class="align-middle">
          <td class="fw-semibold">
            <span class="badge rounded-pill bg-light border text-dark">{{ $p->code }}</span>
          </td>
          <td>
            <div class="fw-semibold">{{ $p->description }}</div>
            <div class="text-muted small">#{{ $p->id }}</div>
          </td>
          <td class="text-end">
            @if($p->stock > 0)
              <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2 me-1"></i>{{ $p->stock }}</span>
            @else
              <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-exclamation-octagon me-1"></i>{{ $p->stock }}</span>
            @endif
          </td>
          <td><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">{{ $p->extent }}</span></td>
          <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $p->categories }}</span></td>
          <td>
            @php $wh = $p->warehouse ?? '—'; @endphp
            <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-building me-1"></i>{{ $wh }}</span>
          </td>
          <td>
            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ \Illuminate\Support\Str::of(optional($p->date_products)->format('Y-m-d') ?? $p->date_products)->toString() }}</span>
          </td>
          <td class="text-end">
            <div class="btn-group">
              <a href="" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Ver">
                <i class="bi bi-eye"></i>
              </a>
              <a href="" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                      data-id="{{ $p->id }}" data-name="{{ $p->description }}">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div class="text-center py-5">
              <div class="mb-2">
                <i class="bi bi-inboxes fs-1 text-muted"></i>
              </div>
              <h6 class="text-muted">No hay productos que coincidan con tu búsqueda.</h6>
              <div class="mt-2">
                <a href="{{ route('products.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-circle me-1"></i> Crear producto</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar filtros</a>
              </div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="card-footer bg-white">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
          Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }}
        </div>
        <div>
          {{ $products->withQueryString()->links() }}
        </div>
      </div>
    </div>
  @endif
</div>

{{-- Modal eliminar --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-sm">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Eliminar producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">¿Seguro que deseas eliminar <strong id="deleteName">este producto</strong>?</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="deleteForm" method="POST" action="#">
          @csrf
          @method('DELETE')
          <button class="btn btn-danger"><i class="bi bi-trash me-1"></i> Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Helpers UX --}}
@push('scripts')
<script>
  // tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

  // modal delete
  const delModal = document.getElementById('confirmDeleteModal');
  delModal?.addEventListener('show.bs.modal', evt => {
    const btn = evt.relatedTarget;
    const id = btn?.getAttribute('data-id');
    const name = btn?.getAttribute('data-name') || 'este producto';
    delModal.querySelector('#deleteName').textContent = name;
    delModal.querySelector('#deleteForm').setAttribute('action', `{{ url('products') }}/${id}`);
  });
</script>
@endpush
@endsection

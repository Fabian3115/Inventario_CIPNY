@extends('layouts.master')

@section('content')

    <div class="glassify-page">
        <div class="container-xxl p-3 glassify-shell">

            {{-- Header + breadcrumb --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-up fs-5 text-danger"></i>
                    <h2 class="h5 mb-0">Registrar salida</h2>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Salida</li>
                    </ol>
                </nav>
            </div>

            {{-- Alertas de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="d-flex">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            <strong>Revisa los campos:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-3 align-items-start">
                {{-- Columna principal (form) --}}
                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm">
                        {{-- MISMA barra degradada animada que “Entrada” --}}
                        <div class="card-header brand">
                            <div class="d-flex align-items-center gap-2 text-white">
                                <i class="bi bi-clipboard-minus"></i>
                                <span class="fw-semibold">Formulario de salida</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('movements.out.store') }}" novalidate id="outForm">
                                @csrf
                                <div class="row g-3">

                                    {{-- Producto --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Producto</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                                            <select id="productSelect" name="product_id"
                                                class="form-select @error('product_id') is-invalid @enderror" required>
                                                <option value="" disabled selected>Seleccione…</option>
                                                @foreach ($products as $p)
                                                    <option value="{{ $p->id }}" data-stock="{{ $p->stock }}"
                                                        data-extent="{{ $p->extent }}"
                                                        data-warehouse="{{ $p->warehouse }}"
                                                        {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                                        {{ $p->code }} — {{ $p->description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">
                                            <span class="me-2">Unidad: <strong id="unitView">—</strong></span>
                                            <span class="me-2">Stock actual: <strong id="stockView">—</strong></span>
                                            <span>Almacén: <strong id="whView">—</strong></span>
                                        </div>
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Fecha de salida</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                            <input type="date" name="date_products"
                                                class="form-control @error('date_products') is-invalid @enderror"
                                                value="{{ old('date_products', now()->toDateString()) }}" required>
                                            @error('date_products')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Y-m-d</div>
                                    </div>

                                    {{-- Cantidad --}}
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Cantidad</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-dash-lg"></i></span>
                                            <input id="amountInput" type="number" step="any" min="0.0001"
                                                name="amount" class="form-control @error('amount') is-invalid @enderror"
                                                value="{{ old('amount') }}" placeholder="0.00" required>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">
                                            Quedará: <strong id="willBeView">—</strong>
                                            <span id="warnLow" class="ms-2 text-danger d-none">
                                                <i class="bi bi-exclamation-octagon"></i> Sin stock suficiente
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Entregado a --}}
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Entregado a</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                            <input type="text" name="delivered_to"
                                                class="form-control @error('delivered_to') is-invalid @enderror"
                                                value='{{ old('delivered_to') }}' placeholder="Persona que recibe"
                                                required>
                                            @error('delivered_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Área --}}
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Área</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                            <select id="areaSelect" name="area"
                                                class="form-select @error('area') is-invalid @enderror" required>
                                                <option value="" disabled {{ old('area') ? '' : 'selected' }}>
                                                    Seleccione…</option>
                                                <option value="Despacho" data-icon="bi-truck"
                                                    {{ old('area') === 'Despacho' ? 'selected' : '' }}>Despacho</option>
                                                <option value="Gestion Humana" data-icon="bi-people"
                                                    {{ old('area') === 'Gestion Humana' ? 'selected' : '' }}>Gestión Humana
                                                </option>
                                                <option value="Producción" data-icon="bi-gear"
                                                    {{ old('area') === 'Producción' ? 'selected' : '' }}>Producción</option>
                                                <option value="Calidad" data-icon="bi-patch-check"
                                                    {{ old('area') === 'Calidad' ? 'selected' : '' }}>Calidad</option>
                                                <option value="Mantenimiento" data-icon="bi-wrench"
                                                    {{ old('area') === 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento
                                                </option>
                                                <option value="Sistemas" data-icon="bi-cpu"
                                                    {{ old('area') === 'Sistemas' ? 'selected' : '' }}>Sistemas</option>
                                                <option value="Gerencia" data-icon="bi-briefcase"
                                                    {{ old('area') === 'Gerencia' ? 'selected' : '' }}>Gerencia</option>
                                                <option value="Bodega 1" data-icon="bi-boxes"
                                                    {{ old('area') === 'Bodega 1' ? 'selected' : '' }}>Bodega 1</option>
                                                <option value="Bodega 2" data-icon="bi-boxes"
                                                    {{ old('area') === 'Bodega 2' ? 'selected' : '' }}>Bodega 2</option>
                                                <option value="Bodega 3" data-icon="bi-boxes"
                                                    {{ old('area') === 'Bodega 3' ? 'selected' : '' }}>Bodega 3</option>
                                                <option value="Laboratorio" data-icon="bi-eyedropper"
                                                    {{ old('area') === 'Laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                                                <option value="Financiera" data-icon="bi-cash-coin"
                                                    {{ old('area') === 'Financiera' ? 'selected' : '' }}>Financiera</option>
                                                <option value="Compras" data-icon="bi-bag-check"
                                                    {{ old('area') === 'Compras' ? 'selected' : '' }}>Compras</option>
                                                <option value="Ambiental" data-icon="bi-tree"
                                                    {{ old('area') === 'Ambiental' ? 'selected' : '' }}>Ambiental</option>
                                                <option value="SST" data-icon="bi-shield-plus"
                                                    {{ old('area') === 'SST' ? 'selected' : '' }}>SST</option>
                                                <option value="Seguridad Corporativa" data-icon="bi-shield-lock"
                                                    {{ old('area') === 'Seguridad Corporativa' ? 'selected' : '' }}>Seguridad
                                                    Corporativa</option>
                                                <option value="Control Interno" data-icon="bi-clipboard-check"
                                                    {{ old('area') === 'Control Interno' ? 'selected' : '' }}>Control Interno
                                                </option>
                                                <option value="Juridica" data-icon="bi-file-earmark-text"
                                                    {{ old('area') === 'Juridica' ? 'selected' : '' }}>Jurídica</option>
                                                <option value="Bienestar" data-icon="bi-emoji-smile"
                                                    {{ old('area') === 'Bienestar' ? 'selected' : '' }}>Bienestar</option>
                                                <option value="Vehiculos" data-icon="bi-truck-front"
                                                    {{ old('area') === 'Vehiculos' ? 'selected' : '' }}>Vehículos</option>
                                                <option value="Electricos" data-icon="bi-lightning-charge"
                                                    {{ old('area') === 'Electricos' ? 'selected' : '' }}>Eléctricos</option>
                                                <option value="Contratista" data-icon="bi-person-badge"
                                                    {{ old('area') === 'Contratista' ? 'selected' : '' }}>Contratista</option>
                                            </select>
                                            @error('area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Sacado por --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Sacado por</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                            <input type="text" name="taken_by"
                                                class="form-control @error('taken_by') is-invalid @enderror"
                                                value='{{ old('taken_by') }}' placeholder="Persona que retira" required>
                                            @error('taken_by')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button id="submitBtn" class="btn btn-danger">
                                        <i class="bi bi-save2 me-1"></i> Registrar salida
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Panel lateral (Tips) --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm h-100" data-tips>
                        <div class="card-header tips d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-white">
                                <i class="bi bi-info-circle"></i>
                                <span class="fw-semibold">Tips</span>
                            </div>
                            <button type="button" class="btn btn-sm tips-close" data-tips-close
                                aria-label="Contraer tips">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li>Verifica el stock antes de retirar.</li>
                                <li>“Entregado a” es obligatorio para trazabilidad.</li>
                                <li>La cantidad se restará del stock actual del producto.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botón flotante “i” (abre Tips al estar colapsado) --}}
            <button type="button" class="tips-fab d-none" data-tips-open aria-label="Mostrar tips"
                title="Mostrar tips">
                <i class="bi bi-info-circle"></i>
            </button>

        </div>
    </div>

    {{-- UX: unidad/stock/almacén y “quedará” con validación --}}
    <script>
        (() => {
            const sel = document.getElementById('productSelect');
            const amt = document.getElementById('amountInput');
            const unitV = document.getElementById('unitView');
            const stockV = document.getElementById('stockView');
            const willV = document.getElementById('willBeView');
            const whV = document.getElementById('whView');
            const warn = document.getElementById('warnLow');
            const submit = document.getElementById('submitBtn');

            function updateViews() {
                const opt = sel.options[sel.selectedIndex];
                const stock = parseFloat(opt?.dataset.stock ?? 'NaN');
                const unit = opt?.dataset.extent ?? '—';
                const wh = opt?.dataset.warehouse ?? '—';
                const take = parseFloat(amt.value || '0');

                unitV.textContent = unit || '—';
                stockV.textContent = Number.isFinite(stock) ? stock : '—';
                whV.textContent = wh || '—';

                if (Number.isFinite(stock) && !isNaN(take)) {
                    const total = stock - take;
                    willV.textContent = isNaN(total) ? '—' : total;
                    if (total < 0) {
                        warn.classList.remove('d-none');
                        submit.setAttribute('disabled', 'disabled');
                    } else {
                        warn.classList.add('d-none');
                        submit.removeAttribute('disabled');
                    }
                } else {
                    willV.textContent = '—';
                    warn.classList.add('d-none');
                    submit.removeAttribute('disabled');
                }
            }

            sel?.addEventListener('change', updateViews);
            amt?.addEventListener('input', updateViews);
            updateViews();
        })();
    </script>

    {{-- TomSelect en Área (con íconos) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.getElementById('areaSelect');
            if (!el || typeof TomSelect === 'undefined') return;

            new TomSelect(el, {
                allowEmptyOption: true,
                shouldLoad: () => false,
                render: {
                    option: (data, escape) => {
                        const icon = data.icon ? `<i class="bi ${escape(data.icon)} me-2"></i>` : '';
                        return `<div class="d-flex align-items-center">${icon}<span>${escape(data.text)}</span></div>`;
                    },
                    item: (data, escape) => {
                        const icon = data.icon ? `<i class="bi ${escape(data.icon)} me-2"></i>` : '';
                        return `<div>${icon}${escape(data.text)}</div>`;
                    }
                }
            });
        });
    </script>

    {{-- Tips colapsables (mismo comportamiento que Entrada) --}}
    <script>
        (() => {
            const mqLg = window.matchMedia('(min-width: 992px)');
            const tips = document.querySelector('[data-tips]');
            const openBt = document.querySelector('[data-tips-open]');
            const closeBt = document.querySelector('[data-tips-close]');
            const formCol = document.querySelector('.col-lg-8');

            if (!tips || !openBt || !closeBt || !formCol) return;

            function setCollapsed(collapsed) {
                if (!mqLg.matches) {
                    tips.classList.remove('d-none');
                    openBt.classList.add('d-none');
                    formCol.classList.remove('col-lg-12');
                    formCol.classList.add('col-lg-8');
                    return;
                }
                if (collapsed) {
                    tips.classList.add('d-none');
                    openBt.classList.remove('d-none');
                    formCol.classList.remove('col-lg-8');
                    formCol.classList.add('col-lg-12');
                    openBt.setAttribute('aria-expanded', 'false');
                } else {
                    tips.classList.remove('d-none');
                    openBt.classList.add('d-none');
                    formCol.classList.remove('col-lg-12');
                    formCol.classList.add('col-lg-8');
                    openBt.setAttribute('aria-expanded', 'true');
                }
            }

            openBt.addEventListener('click', () => setCollapsed(false));
            closeBt.addEventListener('click', () => setCollapsed(true));
            mqLg.addEventListener?.('change', () => setCollapsed(true));

            // Estado inicial: colapsado en escritorio
            setCollapsed(true);
        })();
    </script>

@endsection

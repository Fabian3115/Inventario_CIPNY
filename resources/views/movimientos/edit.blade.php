@extends('layouts.master')

@section('title', 'Editar movimiento')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square"></i>
                        <h5 class="mb-0">Editar movimiento #{{ $movement->id }}</h5>
                    </div>

                    <div class="card-body">
                        <form id="mv-form" action="{{ route('movements.update', $movement) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                {{-- PRODUCTO --}}
                                <div class="col-md-7">
                                    <label class="form-label">Producto</label>
                                    <select name="product_id" id="product_id" class="form-select" required>
                                        <option value="" hidden>Seleccione un producto…</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}" data-code="{{ $p->code }}"
                                                data-desc="{{ $p->description }}" data-stock="{{ $p->stock }}"
                                                data-extent="{{ $p->extent }}" data-cat="{{ $p->categories }}"
                                                data-warehouse="{{ $p->warehouse }}"
                                                {{ $movement->product_id == $p->id ? 'selected' : '' }}>
                                                {{ $p->code }} — {{ $p->description }} (Stock: {{ $p->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Requisición --}}
                                <input type="text" name="requisition" id="requisition" class="form-control"
                                    value="{{ old('requisition', $movement->requisition) }}"
                                    placeholder="N° requisición / soporte">


                                {{-- FECHA --}}
                                <div class="col-md-5">
                                    <label class="form-label">Fecha del movimiento</label>
                                    <input type="date" name="date_products" id="date_products" class="form-control"
                                        value="{{ old('date_products', optional($movement->date_products)->format('Y-m-d')) }}"
                                        required>
                                </div>

                                {{-- TIPO (píldoras) --}}
                                <div class="col-12">
                                    <label class="form-label d-block">Tipo de movimiento</label>
                                    <div class="mv-type" role="group" aria-label="Tipo">
                                        <label class="pill {{ $movement->type === 'entrada' ? 'active' : '' }}"
                                            id="pill-entrada">
                                            <input type="radio" name="type" value="entrada"
                                                {{ $movement->type === 'entrada' ? 'checked' : '' }}>
                                            <i class="bi bi-box-arrow-in-down"></i> Entrada
                                        </label>
                                        <label class="pill {{ $movement->type === 'salida' ? 'active' : '' }}"
                                            id="pill-salida">
                                            <input type="radio" name="type" value="salida"
                                                {{ $movement->type === 'salida' ? 'checked' : '' }}>
                                            <i class="bi bi-box-arrow-up-right"></i> Salida
                                        </label>
                                    </div>
                                    <div class="form-text">Los campos adicionales se muestran en <b>Salida</b>.</div>
                                </div>

                                {{-- CANTIDAD --}}
                                <div class="col-md-6">
                                    <label class="form-label">Cantidad</label>
                                    <div class="input-group">
                                        <input type="number" min="1" step="1" name="amount" id="amount"
                                            class="form-control" placeholder="0"
                                            value="{{ old('amount', $movement->amount) }}" required>
                                        <span class="input-group-text" id="unit-badge">ud</span>
                                    </div>
                                    <div class="small mt-1">
                                        <span class="badge rounded-pill text-bg-light" id="max-badge"
                                            style="display:none;">Máx. salida: 0</span>
                                        <span class="badge rounded-pill text-bg-secondary" id="cat-badge"
                                            style="display:none;"></span>
                                        <span class="badge rounded-pill text-bg-secondary" id="wh-badge"
                                            style="display:none;"></span>
                                    </div>
                                </div>

                                {{-- EXTRA (solo salida) --}}
                                <div class="col-12" id="extra-salida" style="display:none;">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Entregado a</label>
                                            <input type="text" name="delivered_to" id="delivered_to" class="form-control"
                                                value="{{ old('delivered_to', $movement->delivered_to) }}"
                                                placeholder="Quién recibe">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Área</label>
                                            <select name="area" id="area" class="form-select">
                                                <option value="" hidden>Seleccione un área…</option>
                                                @foreach ($areas as $a)
                                                    <option value="{{ $a }}"
                                                        {{ old('area', $movement->area) === $a ? 'selected' : '' }}>
                                                        {{ $a }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Sacado por</label>
                                            <select name="taken_by" id="taken_by" class="form-select">
                                                <option value="" hidden>Seleccione responsable…</option>
                                                @foreach ($takenByList as $t)
                                                    <option value="{{ $t }}"
                                                        {{ old('taken_by', $movement->taken_by) === $t ? 'selected' : '' }}>
                                                        {{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <button type="button" id="previewBtn" class="btn btn-info">
                                    <i class="bi bi-eye"></i> Previsualizar
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-primary">
                                    <i class="bi bi-check2-circle"></i> Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- PANEL LATERAL --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 88px;">
                    <div class="card-header">
                        <i class="bi bi-graph-up-arrow me-2"></i>Estado del producto
                    </div>
                    <div class="card-body">
                        <div id="prod-empty" class="text-muted small" style="display:none;">
                            Selecciona un producto para ver su información.
                        </div>

                        <div id="prod-info">
                            <h6 class="mb-1" id="pi-desc">—</h6>
                            <div class="text-muted mb-2">
                                <span class="me-2">Código: <b id="pi-code">—</b></span>
                                <span>Unidad: <b id="pi-unit">—</b></span>
                            </div>

                            <div class="mb-2 d-flex align-items-center justify-content-between">
                                <span class="small text-muted">Stock actual</span>
                                <span class="badge text-bg-info" id="pi-stock">0</span>
                            </div>
                            <div class="progress mb-3" style="height:10px;">
                                <div class="progress-bar" id="pi-bar" role="progressbar" style="width:0%"></div>
                            </div>

                            <div class="mb-2 d-flex align-items-center justify-content-between">
                                <span class="small text-muted">Stock resultante</span>
                                <span class="badge text-bg-secondary" id="pi-after">0</span>
                            </div>

                            <div id="pi-warning" class="alert alert-warning py-2 px-3 small" style="display:none;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                El stock resultante sería negativo. Ajusta la cantidad o cambia a entrada.
                            </div>
                            <div id="pi-low" class="alert alert-secondary py-2 px-3 small" style="display:none;">
                                <i class="bi bi-info-circle me-1"></i>
                                Stock bajo (&lt;= 10). Considera reabastecer.
                            </div>
                            <div id="pi-ok" class="alert alert-success py-2 px-3 small" style="display:none;">
                                <i class="bi bi-check2-circle me-1"></i>
                                Cambios listos para guardar.
                            </div>

                            <div class="small text-muted mt-2">
                                <span class="d-block" id="pi-cat">Categoría: —</span>
                                <span class="d-block" id="pi-wh">Bodega: —</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        Si cambias el <b>producto</b> o el <b>tipo</b>, el sistema revierte el efecto anterior y aplica el
                        nuevo automáticamente.
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        (function() {
            const selProduct = document.getElementById('product_id');
            const amountInput = document.getElementById('amount');
            const submitBtn = document.getElementById('submitBtn');
            const previewBtn = document.getElementById('previewBtn');
            const unitBadge = document.getElementById('unit-badge');
            const radioEntrada = document.querySelector('input[name="type"][value="entrada"]');
            const radioSalida = document.querySelector('input[name="type"][value="salida"]');
            const pillEntrada = document.getElementById('pill-entrada');
            const pillSalida = document.getElementById('pill-salida');
            const extraSalida = document.getElementById('extra-salida');
            const deliveredTo = document.getElementById('delivered_to');
            const area = document.getElementById('area');
            const takenBy = document.getElementById('taken_by');
            const maxBadge = document.getElementById('max-badge');
            const catBadge = document.getElementById('cat-badge');
            const whBadge = document.getElementById('wh-badge');

            // Panel lateral
            const piDesc = document.getElementById('pi-desc');
            const piCode = document.getElementById('pi-code');
            const piUnit = document.getElementById('pi-unit');
            const piStock = document.getElementById('pi-stock');
            const piBar = document.getElementById('pi-bar');
            const piAfter = document.getElementById('pi-after');
            const piWarn = document.getElementById('pi-warning');
            const piLow = document.getElementById('pi-low');
            const piOk = document.getElementById('pi-ok');
            const piCat = document.getElementById('pi-cat');
            const piWh = document.getElementById('pi-wh');

            function getSelectedData() {
                const opt = selProduct.options[selProduct.selectedIndex];
                if (!opt) return null;
                return {
                    id: selProduct.value,
                    code: opt.getAttribute('data-code'),
                    desc: opt.getAttribute('data-desc'),
                    stock: Number(opt.getAttribute('data-stock') || 0),
                    unit: opt.getAttribute('data-extent') || 'ud',
                    cat: opt.getAttribute('data-cat') || '',
                    wh: opt.getAttribute('data-warehouse') || ''
                };
            }

            function setRequired(el, on) {
                if (!el) return;
                el.toggleAttribute('required', !!on);
            }

            function updatePills() {
                const isSalida = radioSalida.checked;
                pillEntrada.classList.toggle('active', !isSalida);
                pillSalida.classList.toggle('active', isSalida);
                extraSalida.style.display = isSalida ? '' : 'none';
                setRequired(deliveredTo, isSalida);
                setRequired(area, isSalida);
                setRequired(takenBy, isSalida);
                if (!isSalida) {
                    if (deliveredTo) deliveredTo.value = '';
                    if (area) area.value = '';
                    if (takenBy) takenBy.value = '';
                }
                updateComputed();
            }

            function updateComputed() {
                const data = getSelectedData();
                const qty = Number(amountInput.value || 0);
                const isSalida = radioSalida.checked;

                if (!data) {
                    submitBtn.disabled = true;
                    return;
                }

                // Base
                piDesc.textContent = data.desc || '—';
                piCode.textContent = data.code || '—';
                piUnit.textContent = data.unit || 'ud';
                piStock.textContent = data.stock;
                unitBadge.textContent = data.unit || 'ud';
                piCat.textContent = 'Categoría: ' + (data.cat || '—');
                piWh.textContent = 'Bodega: ' + (data.wh || '—');

                const pct = Math.max(0, Math.min(100, (data.stock / 100) * 100));
                piBar.style.width = pct + '%';

                // Máximo en salida
                if (isSalida) {
                    amountInput.setAttribute('max', String(data.stock));
                    maxBadge.style.display = '';
                    maxBadge.textContent = 'Máx. salida: ' + data.stock;
                } else {
                    amountInput.removeAttribute('max');
                    maxBadge.style.display = 'none';
                }

                const after = isSalida ? (data.stock - qty) : (data.stock + qty);
                piAfter.textContent = (isNaN(after) ? data.stock : after);

                const invalidSalida = isSalida && qty > data.stock;
                const hasQty = qty > 0 && Number.isFinite(qty);

                piWarn.style.display = invalidSalida ? '' : 'none';
                piLow.style.display = (!invalidSalida && after <= 10) ? '' : 'none';
                piOk.style.display = (!invalidSalida && hasQty) ? '' : 'none';

                submitBtn.disabled = !data.id || !hasQty || invalidSalida;
            }

            // Previsualización
            function buildPreviewHTML() {
                const data = getSelectedData();
                const isSalida = radioSalida.checked;
                const qty = Number(amountInput.value || 0);

                const extra = isSalida ? `
            <tr><td>Entregado a</td><td>${(deliveredTo?.value || '—')}</td></tr>
            <tr><td>Área</td><td>${(area?.value || '—')}</td></tr>
            <tr><td>Sacado por</td><td>${(takenBy?.value || '—')}</td></tr>
        ` : '';

                const after = isSalida ? (data.stock - qty) : (data.stock + qty);

                return `
            <div class="text-start">
                <table class="table table-sm">
                    <tr><td style="width:40%;">Producto</td><td>${data.code} — ${data.desc}</td></tr>
                    <tr><td>Tipo</td><td><b>${isSalida ? 'Salida' : 'Entrada'}</b></td></tr>
                    <tr><td>Cantidad</td><td>${qty} ${data.unit}</td></tr>
                    <tr><td>Stock actual</td><td>${data.stock}</td></tr>
                    <tr><td>Stock resultante</td><td><b>${isNaN(after) ? data.stock : after}</b></td></tr>
                    ${extra}
                </table>
                <div class="small text-muted">Confirma para actualizar el movimiento.</div>
            </div>
        `;
            }

            [selProduct, amountInput, radioEntrada, radioSalida].forEach(el => {
                el && el.addEventListener('input', updateComputed);
                el && el.addEventListener('change', updateComputed);
            });
            [radioEntrada, radioSalida].forEach(r => r.addEventListener('change', updatePills));

            const form = document.getElementById('mv-form');
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.closest('.card').animate(
                        [{
                            transform: 'translateX(0)'
                        }, {
                            transform: 'translateX(-6px)'
                        }, {
                            transform: 'translateX(6px)'
                        }, {
                            transform: 'translateX(0)'
                        }], {
                            duration: 220
                        }
                    );
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos incompletos',
                        text: 'Revisa los campos requeridos.'
                    });
                }
            });

            previewBtn.addEventListener('click', () => {
                if (!form.checkValidity()) {
                    form.reportValidity?.();
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Completa los campos',
                        text: 'Verifica producto, cantidad y tipo.'
                    });
                }
                const data = getSelectedData();
                const qty = Number(amountInput.value || 0);
                const isSalida = radioSalida.checked;
                if (isSalida && data && qty > data.stock) {
                    return Swal.fire({
                        icon: 'error',
                        title: 'Stock insuficiente',
                        text: 'No puedes retirar más de lo disponible.'
                    });
                }

                Swal.fire({
                    title: 'Previsualización',
                    html: buildPreviewHTML(),
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar y actualizar',
                    cancelButtonText: 'Volver',
                    width: 680,
                    preConfirm: () => form.submit()
                });
            });

            // Init con estado del movimiento actual
            updatePills();
            updateComputed();
        })();
    </script>
@endsection

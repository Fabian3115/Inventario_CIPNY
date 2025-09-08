@extends('layouts.master')

@section('title', 'Registrar movimiento')

{{-- Tu CSS general --}}
<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">
{{-- SweetAlert2 (puedes moverlo al layout si lo usas en todo el sitio) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left-right"></i>
                        <h5 class="mb-0">Registrar movimiento</h5>
                    </div>

                    <div class="card-body">
                        {{-- Catálogos locales opcionales --}}
                        @php
                            $areas = $areas ?? [
                                'Bodega 1',
                                'Bodega 2',
                                'Bodega 3',
                                'Mantenimiento',
                                'Producción',
                                'Sistemas',
                                'Calidad',
                                'Despacho',
                                'Gerencia',
                                'Gestion Humana',
                                'Laboratorio',
                                'Financiera',
                                'Compras',
                                'SST',
                                'Seguridad Corporativa',
                                '----',
                            ];
                            $takenByList = $takenByList ?? [
                                'Yuderly Chavarro',
                                'Yuli Hernandez',
                                'Diego Salazar',
                                'Kevin Murcia',
                                'Fabian Torres',
                                'Kevin Aranque',
                                '----',
                            ];
                        @endphp

                        <form id="mv-form" action="{{ route('movements.store') }}" method="POST" novalidate>
                            @csrf
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
                                                data-warehouse="{{ $p->warehouse }}">
                                                {{ $p->code }} — {{ $p->description }} (Stock: {{ $p->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- FECHA --}}
                                <div class="col-md-5">
                                    <label class="form-label">Fecha del movimiento</label>
                                    <input type="date" name="date_products" id="date_products" class="form-control"
                                        value="{{ now()->format('Y-m-d') }}" required>
                                </div>

                                {{-- REQUISICIÓN (solo ENTRADA) --}}
                                <div class="col-md-6" id="requisition-wrap">
                                    <label class="form-label">Requisición</label>
                                    <input type="text" name="requisition" id="requisition" class="form-control"
                                        placeholder="N° requisición / soporte (obligatorio)">
                                </div>


                                {{-- TIPO (píldoras) --}}
                                <div class="col-12">
                                    <label class="form-label d-block">Tipo de movimiento</label>
                                    <div class="mv-type" role="group" aria-label="Tipo">
                                        <label class="pill active" id="pill-entrada">
                                            <input type="radio" name="type" value="entrada" checked>
                                            <i class="bi bi-box-arrow-in-down"></i> Entrada
                                        </label>
                                        <label class="pill" id="pill-salida">
                                            <input type="radio" name="type" value="salida">
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
                                            class="form-control" placeholder="0" required>
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

                                {{-- EXTRA: SOLO SALIDA --}}
                                <div class="col-12" id="extra-salida" style="display:none;">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Entregado a</label>
                                            <input type="text" name="delivered_to" id="delivered_to" class="form-control"
                                                placeholder="Quién recibe">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Área</label>
                                            <select name="area" id="area" class="form-select">
                                                <option value="" hidden>Seleccione un área…</option>
                                                @foreach ($areas as $a)
                                                    <option value="{{ $a }}">{{ $a }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Sacado por</label>
                                            <select name="taken_by" id="taken_by" class="form-select">
                                                <option value="" hidden>Seleccione responsable…</option>
                                                @foreach ($takenByList as $t)
                                                    <option value="{{ $t }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <hr class="my-4">

                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Cancelar
                                </a>
                                <button type="button" id="previewBtn" class="btn btn-info">
                                    <i class="bi bi-eye"></i> Previsualizar
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-primary">
                                    <i class="bi bi-check2-circle"></i> Guardar movimiento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- PANEL LATERAL: Estado + Historial --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 88px;">
                    <div class="card-header">
                        <i class="bi bi-graph-up-arrow me-2"></i>Estado del producto
                    </div>
                    <div class="card-body">
                        <div id="prod-empty" class="text-muted small">
                            Selecciona un producto para ver su información.
                        </div>

                        <div id="prod-info" style="display:none;">
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
                                Todo listo para registrar este movimiento.
                            </div>

                            <div class="small text-muted mt-2">
                                <span class="d-block" id="pi-cat">Categoría: —</span>
                                <span class="d-block" id="pi-wh">Bodega: —</span>
                            </div>

                            <hr>
                            <h6 class="mb-2">Historial reciente</h6>
                            <ul id="pi-history" class="list-unstyled small mb-0">
                                <li class="text-muted">Sin datos…</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        Tip: en <b>Salida</b>, la cantidad máxima se ajusta automáticamente al stock disponible.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactividad + SweetAlert2 --}}
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
            const dateInput = document.getElementById('date_products');

            // Panel lateral
            const piEmpty = document.getElementById('prod-empty');
            const piInfo = document.getElementById('prod-info');
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
            const piHistory = document.getElementById('pi-history');
            const requisitionInput = document.getElementById('requisition');
            const requisitionWrap = document.getElementById('requisition-wrap');


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

                // Píldoras y extra de salida
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

                // === Requisición: SOLO en ENTRADA ===
                if (requisitionWrap && requisitionInput) {
                    if (isSalida) {
                        requisitionWrap.style.display = 'none';
                        requisitionInput.disabled = true; // no se envía al backend
                        requisitionInput.value = ''; // opcional: limpiar al ocultar
                    } else {
                        requisitionWrap.style.display = '';
                        requisitionInput.disabled = false;
                        // Si quieres que sea obligatorio en ENTRADA, descomenta la línea:
                        // requisitionInput.setAttribute('required', 'required');
                    }
                }

                updateComputed();
            }


            function renderHistory(list) {
                if (!Array.isArray(list) || list.length === 0) {
                    piHistory.innerHTML = '<li class="text-muted">Sin movimientos recientes…</li>';
                    return;
                }
                piHistory.innerHTML = list.map(h => {
                    const badge = h.type === 'entrada' ?
                        '<span class="badge text-bg-success">Entrada</span>' :
                        '<span class="badge text-bg-danger">Salida</span>';
                    const extra = h.type === 'salida' ?
                        `<div class="text-muted">→ ${h.delivered_to ?? ''} ${h.area ? '('+h.area+')' : ''}</div>` :
                        '';
                    return `
                <li class="mb-2">
                    <div class="d-flex justify-content-between">
                        <div>${badge} &nbsp; x${h.amount}</div>
                        <div class="text-muted">${h.date ?? ''}</div>
                    </div>
                    ${extra}
                </li>
            `;
                }).join('');
            }

            function loadHistory(productId) {
                if (!productId) {
                    renderHistory([]);
                    return;
                }
                fetch('{{ route('movements.history', ['product' => '__ID__']) }}'.replace('__ID__', productId))
                    .then(r => r.json())
                    .then(data => renderHistory(data.history))
                    .catch(() => renderHistory([]));
            }

            function updateComputed() {
                const data = getSelectedData();
                const qty = Number(amountInput.value || 0);
                const isSalida = radioSalida.checked;

                if (!data) {
                    piEmpty.style.display = '';
                    piInfo.style.display = 'none';
                    submitBtn.disabled = true;
                    return;
                }

                // Mostrar info base
                piEmpty.style.display = 'none';
                piInfo.style.display = '';
                piDesc.textContent = data.desc || '—';
                piCode.textContent = data.code || '—';
                piUnit.textContent = data.unit || 'ud';
                piStock.textContent = data.stock;
                unitBadge.textContent = data.unit || 'ud';
                piCat.textContent = 'Categoría: ' + (data.cat || '—');
                piWh.textContent = 'Bodega: ' + (data.wh || '—');

                // Barra (0..100 arbitrario)
                const pct = Math.max(0, Math.min(100, (data.stock / 100) * 100));
                piBar.style.width = pct + '%';

                // Ajustes para salida
                if (isSalida) {
                    amountInput.setAttribute('max', String(data.stock));
                    maxBadge.style.display = '';
                    maxBadge.textContent = 'Máx. salida: ' + data.stock;
                } else {
                    amountInput.removeAttribute('max');
                    maxBadge.style.display = 'none';
                }

                // Badges categoría/bodega
                if (data.cat) {
                    catBadge.style.display = 'inline-block';
                    catBadge.textContent = data.cat;
                } else {
                    catBadge.style.display = 'none';
                }
                if (data.wh) {
                    whBadge.style.display = 'inline-block';
                    whBadge.textContent = data.wh;
                } else {
                    whBadge.style.display = 'none';
                }

                // Cálculo de stock resultante
                const after = isSalida ? (data.stock - qty) : (data.stock + qty);
                piAfter.textContent = (isNaN(after) ? data.stock : after);

                // Estados / validaciones
                const invalidSalida = isSalida && qty > data.stock;
                const hasQty = qty > 0 && Number.isFinite(qty);

                piWarn.style.display = invalidSalida ? '' : 'none';
                piLow.style.display = (!invalidSalida && after <= 10) ? '' : 'none';
                piOk.style.display = (!invalidSalida && hasQty) ? '' : 'none';

                submitBtn.disabled = !data.id || !hasQty || invalidSalida;
            }

            // SweetAlert2: Previsualización antes de guardar
            function buildPreviewHTML() {
                const data = getSelectedData();
                const isSalida = radioSalida.checked;
                const qty = Number(amountInput.value || 0);

                const salidaExtra = isSalida ? `
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
                <tr><td>Fecha</td><td>${dateInput.value || '—'}</td></tr>
                <tr><td>Stock actual</td><td>${data.stock}</td></tr>
                <tr><td>Stock resultante</td><td><b>${isNaN(after) ? data.stock : after}</b></td></tr>
                ${salidaExtra}
            </table>
            <div class="small text-muted">Verifica los datos antes de confirmar.</div>
        </div>`;
            }

            // Eventos base
            [selProduct, amountInput, radioEntrada, radioSalida].forEach(el => {
                el && el.addEventListener('input', updateComputed);
                el && el.addEventListener('change', updateComputed);
            });

            [radioEntrada, radioSalida].forEach(r => r.addEventListener('change', () => {
                updatePills();
                const data = getSelectedData();
                if (data?.id) loadHistory(data.id);
            }));

            // Cargar historial al cambiar producto
            selProduct.addEventListener('change', () => {
                const data = getSelectedData();
                if (data?.id) loadHistory(data.id);
            });

            // Validación amable al enviar directo
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
                        text: 'Revisa los campos requeridos.',
                        confirmButtonText: 'Entendido'
                    });
                }
            });

            // Botón PREVISUALIZAR con confirmación de guardado
            previewBtn.addEventListener('click', () => {
                if (!form.checkValidity()) {
                    form.reportValidity?.();
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Completa los campos',
                        text: 'Selecciona producto, cantidad y fecha (y datos de salida si aplica).'
                    });
                }
                // Validación extra de stock en salida
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
                    confirmButtonText: 'Confirmar y guardar',
                    cancelButtonText: 'Volver',
                    focusConfirm: false,
                    width: 680,
                    preConfirm: () => {
                        // Enviar el formulario tras confirmar
                        form.submit();
                    }
                });
            });

            // Init
            updatePills();
            updateComputed();

            // Si ya hay un producto precargado, trae historial
            const initData = getSelectedData();
            if (initData?.id) loadHistory(initData.id);
        })();


        // --- Hacer el select buscable con Tom Select ---
        const productTS = new TomSelect('#product_id', {
            plugins: {
                clear_button: {
                    title: 'Limpiar'
                }
            },
            placeholder: 'Escribe código o descripción…',
            searchField: ['text', 'code', 'desc'], // busca por texto visible, código y descripción
            maxOptions: 5000,
            preload: true,
            render: {
                option: function(data, escape) {
                    // data.code / data.desc vienen de los data-* del <option>
                    const code = escape(data.code || '');
                    const desc = escape(data.desc || data.text || '');
                    const stock = escape(data.stock || '');
                    return `
                <div class="py-1">
                    <div><strong>${code}</strong> — ${desc}</div>
                    <div class="text-muted small">Stock: ${stock}</div>
                </div>
            `;
                },
                item: function(data, escape) {
                    const code = escape(data.code || '');
                    const desc = escape(data.desc || data.text || '');
                    return `<div>${code} — ${desc}</div>`;
                }
            },
            onChange: function(val) {
                // Asegura que tu lógica de panel/historial se dispare como antes
                selProduct.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            }
        });
    </script>
@endsection

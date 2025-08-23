@extends('layouts.master')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">

@section('content')

    {{-- Header + breadcrumb --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-in-down fs-5 text-primary"></i>
            <h2 class="h5 mb-0">Registrar entrada</h2>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Entrada</li>
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

    <div class="row g-3 entry-form position-relative" id="entryLayoutRow">
        {{-- Columna principal (formulario) --}}
        <div class="col-12 col-lg-8" id="formCol">
            <div class="card shadow-sm entry-card">
                <div class="card-header brand">
                    <div class="d-flex align-items-center gap-2 text-white">
                        <i class="bi bi-clipboard-plus"></i>
                        <span class="fw-semibold">Formulario de entrada</span>
                    </div>
                </div>
                <div class="card-body">
                    {{-- === TU FORM TAL CUAL === --}}
                    <form method="POST" action="{{ route('movements.in.store') }}" novalidate>
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
                                    <span>Stock actual: <strong id="stockView">—</strong></span>
                                </div>
                            </div>

                            {{-- Fecha --}}
                            <div class="col-6">
                                <label class="form-label fw-semibold">Fecha de entrada</label>
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
                                    <span class="input-group-text"><i class="bi bi-plus-lg"></i></span>
                                    <input id="amountInput" type="number" step="any" min="0.0001" name="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Quedará: <strong id="willBeView">—</strong></div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button class="btn btn-success">
                                <i class="bi bi-save2 me-1"></i> Registrar entrada
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
        <div class="col-12 col-lg-4" id="tipsCol">
            <div class="card shadow-sm h-100 tips-card">
                <div class="card-header tips d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2 text-white">
                        <i class="bi bi-info-circle"></i>
                        <span class="fw-semibold">Tips</span>
                    </div>
                    <button type="button" id="closeTipsBtn" class="btn btn-sm btn-light-subtle text-white tips-close"
                        aria-label="Contraer tips" title="Contraer tips">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small tips-list">
                        <li>- Confirma el producto y su unidad antes de registrar.</li>
                        <li>- La cantidad se sumará al stock actual del producto.</li>
                        <li>- Usa “Observaciones” para lotes u OCs.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Botón flotante cuando Tips está contraído (solo en ≥lg) --}}
        <button type="button" id="openTipsBtn" class="btn tips-fab d-none" aria-label="Mostrar tips" title="Mostrar tips">
            <i class="bi bi-info-circle"></i>
        </button>
    </div>


    {{-- Script UX: muestra unidad/stock actual y calcula "quedará" --}}
    <script>
        (() => {
            const sel = document.getElementById('productSelect');
            const amt = document.getElementById('amountInput');
            const unitV = document.getElementById('unitView');
            const stockV = document.getElementById('stockView');
            const willV = document.getElementById('willBeView');

            function updateViews() {
                const opt = sel.options[sel.selectedIndex];
                const stock = parseFloat(opt?.dataset.stock ?? 'NaN');
                const unit = opt?.dataset.extent ?? '—';
                const add = parseFloat(amt.value || '0');

                unitV.textContent = unit || '—';
                stockV.textContent = Number.isFinite(stock) ? stock : '—';

                if (Number.isFinite(stock) && !isNaN(add)) {
                    const total = stock + add;
                    willV.textContent = isNaN(total) ? '—' : total;
                } else {
                    willV.textContent = '—';
                }
            }

            sel?.addEventListener('change', updateViews);
            amt?.addEventListener('input', updateViews);

            // Inicial
            updateViews();
        })();

        (() => {
            const sel = document.getElementById('productSelect');
            const amt = document.getElementById('amountInput');
            const unitV = document.getElementById('unitView');
            const stockV = document.getElementById('stockView');
            const willV = document.getElementById('willBeView');

            function updateViews() {
                const opt = sel?.options[sel.selectedIndex];
                const stock = parseFloat(opt?.dataset.stock ?? 'NaN');
                const unit = opt?.dataset.extent ?? '—';
                const add = parseFloat(amt?.value || '0');

                unitV.textContent = unit || '—';
                stockV.textContent = Number.isFinite(stock) ? stock : '—';

                willV.classList.remove('will-ok', 'will-warn');
                if (Number.isFinite(stock) && !isNaN(add)) {
                    const total = stock + add;
                    willV.textContent = isNaN(total) ? '—' : total;
                    if (!isNaN(total)) {
                        willV.classList.add(total >= stock ? 'will-ok' : 'will-warn');
                    }
                } else {
                    willV.textContent = '—';
                }
            }

            sel?.addEventListener('change', updateViews);
            amt?.addEventListener('input', updateViews);
            updateViews();

            // Auto-scroll al primer error si lo hay
            const firstInvalid = document.querySelector('.entry-form .is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstInvalid.focus?.();
            }
        })();

        //--------//
        (() => {
            // === Ripple ======================================================
            function attachRipple(el) {
                if (!el) return;
                el.classList.add('ripple-host');
                el.addEventListener('click', (e) => {
                    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    if (reduce) return;
                    const rect = el.getBoundingClientRect();
                    const r = document.createElement('span');
                    r.className = 'ripple';
                    r.style.left = (e.clientX - rect.left) + 'px';
                    r.style.top = (e.clientY - rect.top) + 'px';
                    el.appendChild(r);
                    r.addEventListener('animationend', () => r.remove(), {
                        once: true
                    });
                });
            }

            document.querySelectorAll('.entry-form .btn, .entry-form .input-group-text').forEach(attachRipple);

            // === Ping en "Quedará" cada actualización ========================
            const willV = document.getElementById('willBeView');
            const sel = document.getElementById('productSelect');
            const amt = document.getElementById('amountInput');

            function ping() {
                if (!willV) return;
                willV.classList.remove('ping');
                // forza reflow para reiniciar animación
                void willV.offsetWidth;
                willV.classList.add('ping');
            }

            function onUpdate() {
                ping();
            }
            sel?.addEventListener('change', onUpdate);
            amt?.addEventListener('input', onUpdate);
        })();

        (() => {
            const mqLg = window.matchMedia('(min-width: 992px)');
            const formCol = document.getElementById('formCol');
            const tipsCol = document.getElementById('tipsCol');
            const openBtn = document.getElementById('openTipsBtn');
            const closeBtn = document.getElementById('closeTipsBtn');

            function setCollapsed(collapsed) {
                // Solo colapsamos en ≥lg; en móviles siempre se muestra en stack
                if (!mqLg.matches) {
                    tipsCol.classList.remove('d-none');
                    formCol.classList.remove('col-lg-12');
                    formCol.classList.add('col-lg-8');
                    openBtn.classList.add('d-none');
                    return;
                }

                if (collapsed) {
                    // Oculta tips y expande form
                    tipsCol.classList.add('d-none');
                    formCol.classList.remove('col-lg-8');
                    formCol.classList.add('col-lg-12');
                    openBtn.classList.remove('d-none');
                    openBtn.setAttribute('aria-expanded', 'false');
                } else {
                    // Muestra tips y reduce form
                    tipsCol.classList.remove('d-none');
                    formCol.classList.remove('col-lg-12');
                    formCol.classList.add('col-lg-8');
                    openBtn.classList.add('d-none');
                    openBtn.setAttribute('aria-expanded', 'true');
                }
            }

            // Eventos
            openBtn?.addEventListener('click', () => setCollapsed(false));
            closeBtn?.addEventListener('click', () => setCollapsed(true));
            mqLg.addEventListener?.('change', () => setCollapsed(true)); // al cambiar de tamaño, re-colapsa en ≥lg

            // Estado inicial: colapsado en escritorio, normal en móvil
            setCollapsed(true);
        })();
    </script>



@endsection

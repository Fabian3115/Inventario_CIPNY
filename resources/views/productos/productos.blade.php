@extends('layouts.master')

@section('content')

    <div class="glassify-page">
        <div class="container-xxl p-3 glassify-shell">

            {{-- Encabezado contextual / migas --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam fs-5 text-primary"></i>
                    <h2 class="h5 mb-0">Nuevo producto</h2>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear</li>
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
                        {{-- Header con la misma barra degradada animada que “Entrada” --}}
                        <div class="card-header brand">
                            <div class="d-flex align-items-center gap-2 text-white">
                                <i class="bi bi-clipboard-plus"></i>
                                <span class="fw-semibold">Formulario de registro</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('products.store') }}" novalidate>
                                @csrf
                                <div class="row g-3">
                                    {{-- Código --}}
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Código</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                            <input id="codeInput" type="number" min="1" name="code"
                                                class="form-control @error('code') is-invalid @enderror"
                                                value="{{ old('code', $nextCode ?? '') }}" placeholder="Ej: 1001" required
                                                data-check-url="{{ route('products.checkCode') }}"
                                                data-next-url="{{ route('products.nextCode') }}">
                                            <button class="btn btn-outline-primary" type="button" id="suggestCodeBtn"
                                                title="Sugerir siguiente código">
                                                <i class="bi bi-magic"></i>
                                            </button>
                                        </div>


                                        <div id="codeMsg" class="form-text"></div>
                                        @error('code')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Stock --}}
                                    <div class="col-6 col-md-4">
                                        <label class="form-label fw-semibold">Stock</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-box"></i></span>
                                            <input type="number" min="0" name="stock"
                                                class="form-control @error('stock') is-invalid @enderror"
                                                value="{{ old('stock', 0) }}" placeholder="0" required>
                                            @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Cantidad actual en inventario.</div>
                                    </div>

                                    {{-- Fecha de alta --}}
                                    <div class="col-6 col-md-4">
                                        <label class="form-label fw-semibold">Fecha de acta</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                            <input type="date" name="date_products"
                                                class="form-control @error('date_products') is-invalid @enderror"
                                                value="{{ old('date_products', now()->toDateString()) }}" required>
                                            @error('date_products')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Fecha en que entra al stock.</div>
                                    </div>

                                    {{-- Descripción --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Descripción</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                            <input id="descriptionInput" type="text" name="description"
                                                class="form-control @error('description') is-invalid @enderror"
                                                value="{{ old('description') }}"
                                                placeholder="Nombre o detalle del producto" required>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Vista previa: así se guardará en BD --}}
                                        <div id="descriptionPreview" class="form-text" aria-live="polite">
                                            Así se guardará: <strong id="descriptionPreviewText">—</strong>
                                        </div>
                                    </div>

                                    {{-- Categoría --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Categoría</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-tags"></i></span>
                                            <input list="categories_suggestions" type="text" name="categories"
                                                class="form-control @error('categories') is-invalid @enderror"
                                                value="{{ old('categories') }}" placeholder="Ej: Redes, Cómputo..."
                                                required>
                                            @error('categories')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <datalist id="categories_suggestions">
                                            <option value="Redes"></option>
                                            <option value="Cómputo"></option>
                                            <option value="Periféricos"></option>
                                            <option value="Eléctrico"></option>
                                            <option value="Herramientas"></option>
                                        </datalist>
                                    </div>

                                    {{-- Unidad de Medida (extent) --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Unidad de Medida</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                            <input list="units_suggestions" type="text" name="extent"
                                                class="form-control @error('extent') is-invalid @enderror"
                                                value="{{ old('extent') }}" placeholder="Ej: Unidad, Caja, Metro, Kg, Lt"
                                                required>
                                            @error('extent')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <datalist id="units_suggestions">
                                            <option value="Unidad"></option>
                                            <option value="Caja"></option>
                                            <option value="Paquete"></option>
                                            <option value="Metro"></option>
                                            <option value="Kilogramo"></option>
                                            <option value="Litro"></option>
                                        </datalist>
                                    </div>

                                    {{-- Almacén --}}
                                    <label for="warehouse" class="form-label fw-semibold">Almacén</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <select id="warehouse" name="warehouse"
                                            class="form-select @error('warehouse') is-invalid @enderror" required>
                                            <option value="" disabled {{ old('warehouse') ? '' : 'selected' }}>
                                                Seleccione…</option>
                                            <option value="Servidor"
                                                {{ old('warehouse') == 'Servidor' ? 'selected' : '' }}>Servidor</option>
                                            <option value="Piso 3" {{ old('warehouse') == 'Piso 3' ? 'selected' : '' }}>
                                                Piso 3</option>
                                        </select>
                                        @error('warehouse')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button class="btn btn-success">
                                        <i class="bi bi-save2 me-1"></i> Guardar
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
                                <span class="fw-semibold">Consejos</span>
                            </div>
                            <button type="button" class="btn btn-sm tips-close" data-tips-close
                                aria-label="Contraer tips">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li>Usa códigos consecutivos o el código de barras del producto.</li>
                                <li>La <strong>unidad de medida</strong> debe ser consistente con entradas/salidas.</li>
                                <li>La <strong>fecha de alta</strong> será la base de tu kardex.</li>
                                <li>Define bien el <strong>Almacén</strong> para facilitar conteos físicos.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botón flotante “i” para abrir Tips cuando está colapsado (solo escritorio) --}}
            <button type="button" class="tips-fab d-none" data-tips-open aria-label="Mostrar tips"
                title="Mostrar tips">
                <i class="bi bi-info-circle"></i>
            </button>

        </div>
    </div>

    {{-- Script para verificación de código --}}
    <script>
        (() => {
            const input = document.getElementById('codeInput');
            const msg = document.getElementById('codeMsg');
            if (!input || !msg) return;

            const url = input.dataset.checkUrl;
            let timer = null;
            let currentAbort = null;

            const setState = (state, text = '') => {
                input.classList.remove('is-valid', 'is-invalid');
                msg.className = 'form-text';
                msg.textContent = '';

                if (state === 'invalid') {
                    input.classList.add('is-invalid');
                    msg.classList.add('text-danger');
                    msg.textContent = text || 'Código inválido: use números positivos.';
                    return;
                }
                if (state === 'exists') {
                    input.classList.add('is-invalid');
                    msg.classList.add('text-danger');
                    msg.textContent = text || 'Código ocupado — cámbielo.';
                    return;
                }
                if (state === 'available') {
                    input.classList.add('is-valid');
                    msg.classList.add('text-success');
                    msg.textContent = text || 'Código disponible.';
                    return;
                }
                if (state === 'checking') {
                    msg.classList.add('text-muted');
                    msg.textContent = text || 'Verificando...';
                    return;
                }
                if (state === 'error') {
                    msg.classList.add('text-warning');
                    msg.textContent = text || 'No se pudo verificar. Intente de nuevo.';
                }
            };

            const checkCode = async (val) => {
                if (currentAbort) currentAbort.abort();
                currentAbort = new AbortController();

                if (!/^\d+$/.test(val) || parseInt(val, 10) < 1) {
                    setState('invalid');
                    return;
                }
                setState('checking');

                try {
                    const res = await fetch(`${url}?code=${encodeURIComponent(val)}`, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        cache: 'no-store',
                        signal: currentAbort.signal
                    });
                    if (!res.ok) {
                        setState('error');
                        return;
                    }
                    const data = await res.json();
                    if (data.valid === false) {
                        setState('invalid');
                        return;
                    }
                    data.exists ? setState('exists') : setState('available');
                } catch (err) {
                    if (err.name === 'AbortError') return;
                    setState('error');
                }
            };

            const onChange = () => {
                const raw = (input.value || '').trim();
                clearTimeout(timer);
                timer = setTimeout(() => checkCode(raw), 250);
            };

            input.addEventListener('input', onChange);
            input.addEventListener('change', onChange);

            if ((input.value || '').trim().length > 0) {
                checkCode(input.value.trim());
            }
        })();
    </script>

    {{-- Tips colapsables (local por si aún no pegaste el JS global en el layout) --}}
    <script>
        (() => {
            const mqLg = window.matchMedia('(min-width: 992px)');
            const tipsCard = document.querySelector('[data-tips]');
            const openBtn = document.querySelector('[data-tips-open]');
            const closeBtn = document.querySelector('[data-tips-close]');
            const formCol = document.querySelector('.col-lg-8'); // la columna del formulario

            if (!tipsCard || !openBtn || !closeBtn || !formCol) return;

            function setCollapsed(collapsed) {
                if (!mqLg.matches) {
                    tipsCard.classList.remove('d-none');
                    openBtn.classList.add('d-none');
                    formCol.classList.remove('col-lg-12');
                    formCol.classList.add('col-lg-8');
                    return;
                }
                if (collapsed) {
                    tipsCard.classList.add('d-none');
                    openBtn.classList.remove('d-none');
                    formCol.classList.remove('col-lg-8');
                    formCol.classList.add('col-lg-12');
                    openBtn.setAttribute('aria-expanded', 'false');
                } else {
                    tipsCard.classList.remove('d-none');
                    openBtn.classList.add('d-none');
                    formCol.classList.remove('col-lg-12');
                    formCol.classList.add('col-lg-8');
                    openBtn.setAttribute('aria-expanded', 'true');
                }
            }

            openBtn.addEventListener('click', () => setCollapsed(false));
            closeBtn.addEventListener('click', () => setCollapsed(true));
            mqLg.addEventListener?.('change', () => setCollapsed(true)); // re-colapsa en escritorio al cambiar tamaño

            // Estado inicial: colapsado en escritorio
            setCollapsed(true);
        })();
    </script>

    {{-- Codigo Sugerido e inyectado --}}
    <script>
        (() => {
            const input = document.getElementById('codeInput');
            const msg = document.getElementById('codeMsg');
            if (!input || !msg) return;

            const checkUrl = input.dataset.checkUrl;
            const nextUrl = input.dataset.nextUrl;
            let timer = null;
            let currentAbort = null;

            const setState = (state, text = '') => {
                input.classList.remove('is-valid', 'is-invalid');
                msg.className = 'form-text';
                msg.textContent = '';

                if (state === 'invalid') {
                    input.classList.add('is-invalid');
                    msg.classList.add('text-danger');
                    msg.textContent = text || 'Código inválido: use números positivos.';
                    return;
                }
                if (state === 'exists') {
                    input.classList.add('is-invalid');
                    msg.classList.add('text-danger');
                    msg.textContent = text || 'Código ocupado — cámbielo.';
                    return;
                }
                if (state === 'available') {
                    input.classList.add('is-valid');
                    msg.classList.add('text-success');
                    msg.textContent = text || 'Código disponible.';
                    return;
                }
                if (state === 'checking') {
                    msg.classList.add('text-muted');
                    msg.textContent = text || 'Verificando...';
                    return;
                }
                if (state === 'suggest') {
                    msg.classList.add('text-muted');
                    msg.textContent = text || 'Sugerido automáticamente.';
                    return;
                }
                if (state === 'error') {
                    msg.classList.add('text-warning');
                    msg.textContent = text || 'No se pudo verificar. Intente de nuevo.';
                }
            };

            const getNextCode = async () => {
                if (!nextUrl) return;
                try {
                    const res = await fetch(nextUrl, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const next = parseInt(data.next, 10);
                    if (Number.isFinite(next) && next > 0) {
                        input.value = String(next);
                        setState('suggest', 'Sugerido: ' + next);
                        // Valida inmediatamente el sugerido
                        checkCode(String(next));
                    }
                } catch (_) {
                    /* silencio */
                }
            };

            const checkCode = async (val) => {
                if (currentAbort) currentAbort.abort();
                currentAbort = new AbortController();

                if (!/^\d+$/.test(val) || parseInt(val, 10) < 1) {
                    setState('invalid');
                    return;
                }
                setState('checking');

                try {
                    const res = await fetch(`${checkUrl}?code=${encodeURIComponent(val)}`, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        cache: 'no-store',
                        signal: currentAbort.signal
                    });
                    if (!res.ok) {
                        setState('error');
                        return;
                    }
                    const data = await res.json();
                    if (data.valid === false) {
                        setState('invalid');
                        return;
                    }
                    data.exists ? setState('exists') : setState('available');
                } catch (err) {
                    if (err.name === 'AbortError') return;
                    setState('error');
                }
            };

            const onChange = () => {
                const raw = (input.value || '').trim();
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if (raw === '') {
                        // Si quedó vacío, vuelve a sugerir el siguiente código actual en DB
                        getNextCode();
                    } else {
                        checkCode(raw);
                    }
                }, 250);
            };

            input.addEventListener('input', onChange);
            input.addEventListener('change', onChange);

            // Al cargar:
            const initial = (input.value || '').trim();
            if (initial === '') {
                // Si el servidor no precargó (o se perdió el old), sugiere en vivo
                getNextCode();
            } else {
                // Si ya hay valor (por old() o $nextCode), validarlo
                checkCode(initial);
            }
        })();

        const btnSuggest = document.getElementById('suggestCodeBtn');
        btnSuggest?.addEventListener('click', () => {
            input.value = '';
            input.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        });
    </script>

    {{-- Manejo de descripción; EJ: Limpiador De Contacto --}}
    <script>
        (() => {
            const el = document.getElementById('descriptionInput');
            const pv = document.getElementById('descriptionPreviewText');
            if (!el || !pv) return;

            // Misma lógica que en el mutator del modelo (título por palabra, robusto en UTF-8)
            const toTitleEs = (s) => {
                let out = (s || '').toLocaleLowerCase('es-CO'); // 1) minúsculas con locale
                out = out.replace(/\b\p{L}/gu, (m) => m.toLocaleUpperCase(
                'es-CO')); // 2) inicial de cada palabra en mayúscula
                out = out.replace(/\s+/g, ' ').trim(); // 3) colapsar espacios
                return out;
            };

            const render = () => {
                const normalized = toTitleEs(el.value);
                pv.textContent = normalized || '—';
            };

            // Formatear en vivo y al salir del campo
            el.addEventListener('input', render);
            el.addEventListener('blur', () => {
                el.value = toTitleEs(el.value); // también reescribe el input para que se envíe así
                render();
            });

            // Asegurar que se envíe normalizado
            el.form?.addEventListener('submit', () => {
                el.value = toTitleEs(el.value);
                render();
            });

            // Estado inicial
            render();
        })();
    </script>


@endsection

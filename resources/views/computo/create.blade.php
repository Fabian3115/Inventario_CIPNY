@extends('layouts.master')

@section('title', 'Registrar equipo')

{{-- Tu CSS general --}}
<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">

@section('content')

<div class="glassify-page">
    <div class="container-xxl p-3 glassify-shell">

        {{-- Encabezado contextual / migas --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-pc-display-horizontal fs-5 text-primary"></i>
                <h2 class="h5 mb-0">Nuevo equipo</h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('computations.index') }}">Equipos</a></li>
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
                    {{-- Header degradado (igual estilo que productos) --}}
                    <div class="card-header brand">
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-clipboard-plus"></i>
                            <span class="fw-semibold">Formulario de registro</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('computations.store') }}" novalidate>
                            @csrf
                            <div class="row g-3">

                                {{-- Requisición (requerido) --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Requisición</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                        <input type="text" name="requisition" id="requisitionInput"
                                               class="form-control @error('requisition') is-invalid @enderror"
                                               value="{{ old('requisition') }}"
                                               placeholder="N° requisición / soporte" required>
                                        @error('requisition')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Identificador o soporte asociado al equipo.</div>
                                </div>

                                {{-- Marca (requerido) --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Marca</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="brand" id="brandInput"
                                               class="form-control @error('brand') is-invalid @enderror"
                                               value="{{ old('brand') }}" placeholder="Ej: Lenovo, HP, Dell..." required>
                                        @error('brand')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Se normaliza visualmente: <strong id="brandPreview">—</strong>
                                    </div>
                                </div>

                                {{-- Serial S/N (requerido) --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Serial (S/N)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        {{-- Nota: el name incluye la barra tal como en la migración --}}
                                        <input type="text" name="serial_s/n" id="serialInput"
                                               class="form-control @error('serial_s/n') is-invalid @enderror"
                                               value="{{ old('serial_s/n') }}" placeholder="Ej: PF2ABCD1234" required>
                                        @error('serial_s/n')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Se mostrará en mayúsculas: <strong id="serialPreview">—</strong>
                                    </div>

                                </div>
                                {{-- Tipo (opcional) --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Tipo de Computo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-laptop"></i></span>
                                        <select name="type" id="typeInput"
                                               class="form-control @error('type') is-invalid @enderror"
                                               value="{{ old('type') }}" placeholder="Ej: Portátil, Escritorio...">
                                            <option value="">Seleccione un tipo de computo ...</option>
                                            <option value="portátil" {{ old('type') == 'portátil' ? 'selected' : '' }}>Portátil</option>
                                            <option value="Pc Escritorio" {{ old('type') == 'Pc Escritorio' ? 'selected' : '' }}>PC Escritorio</option>
                                            <option value="Impresora" {{ old('type') == 'Impresora' ? 'selected' : '' }}>Impresora</option>
                                            <option value="Celular" {{ old('type') == 'Celular' ? 'selected' : '' }}>Celular</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                            <div class="mt-4 d-flex gap-2">
                                <button class="btn btn-success">
                                    <i class="bi bi-save2 me-1"></i> Guardar
                                </button>
                                <a href="{{ route('computations.index') }}" class="btn btn-outline-secondary">
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
                        <button type="button" class="btn btn-sm tips-close" data-tips-close aria-label="Contraer tips">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 small">
                            <li>La <strong>requisición</strong> debe corresponder al documento interno.</li>
                            <li>Normaliza la <strong>marca</strong> (Lenovo, HP, Dell...).</li>
                            <li>Captura el <strong>serial</strong> exactamente como aparece en la etiqueta.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón flotante para reabrir tips en escritorio --}}
        <button type="button" class="tips-fab d-none" data-tips-open aria-label="Mostrar tips" title="Mostrar tips">
            <i class="bi bi-info-circle"></i>
        </button>

    </div>
</div>

{{-- Normalización y vista previa de campos + tips colapsables --}}
<script>
(() => {
    // --- Normalizar MARCA a "Title Case" y previsualizar ---
    const brand = document.getElementById('brandInput');
    const brandPreview = document.getElementById('brandPreview');
    const toTitleEs = (s) => {
        let out = (s || '').toLocaleLowerCase('es-CO');
        out = out.replace(/\b\p{L}/gu, m => m.toLocaleUpperCase('es-CO'));
        return out.replace(/\s+/g, ' ').trim();
    };
    const renderBrand = () => {
        const norm = toTitleEs(brand.value);
        brandPreview.textContent = norm || '—';
    };
    brand?.addEventListener('input', renderBrand);
    brand?.addEventListener('blur', () => { brand.value = toTitleEs(brand.value); renderBrand(); });
    brand?.form?.addEventListener('submit', () => { brand.value = toTitleEs(brand.value); });

    // --- Normalizar SERIAL a MAYÚSCULAS y previsualizar ---
    const serial = document.getElementById('serialInput');
    const serialPreview = document.getElementById('serialPreview');
    const renderSerial = () => {
        const norm = (serial.value || '').toUpperCase().replace(/\s+/g, '').trim();
        serialPreview.textContent = norm || '—';
    };
    serial?.addEventListener('input', renderSerial);
    serial?.addEventListener('blur', () => { serial.value = (serial.value || '').toUpperCase().trim(); renderSerial(); });
    serial?.form?.addEventListener('submit', () => { serial.value = (serial.value || '').toUpperCase().trim(); });

    // Estado inicial
    renderBrand();
    renderSerial();
})();

(() => {
    // Tips colapsables (igual que en productos)
    const mqLg = window.matchMedia('(min-width: 992px)');
    const tipsCard = document.querySelector('[data-tips]');
    const openBtn = document.querySelector('[data-tips-open]');
    const closeBtn = document.querySelector('[data-tips-close]');
    const formCol = document.querySelector('.col-lg-8');

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
    mqLg.addEventListener?.('change', () => setCollapsed(true));
    setCollapsed(true); // estado inicial en escritorio
})();
</script>

@endsection

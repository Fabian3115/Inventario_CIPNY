@extends('layouts.master')

@section('title', 'Registrar movimiento de equipo')

{{-- CSS base que ya usas --}}
<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">

{{-- Tom Select para buscar en el select --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

{{-- SweetAlert2 (si no está en tu layout) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')

<div class="glassify-page">
    <div class="container-xxl p-3 glassify-shell">

        {{-- Encabezado / migas --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-pc-display-horizontal fs-5 text-primary"></i>
                <h2 class="h5 mb-0">Nuevo movimiento de equipo</h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('computed_movements.index') }}">Movimientos</a></li>
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
                    <div class="card-header brand">
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-clipboard-plus"></i>
                            <span class="fw-semibold">Formulario de registro</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <form id="cm-form" method="POST" action="{{ route('computed_movements.store') }}" novalidate>
                            @csrf
                            <div class="row g-3">

                                {{-- Equipo (computation) --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Equipo</label>
                                    <select name="computation_id" id="computation_id" class="form-select @error('computation_id') is-invalid @enderror" required>
                                        <option value="" hidden>Seleccione un equipo…</option>
                                        @foreach ($computations as $c)
                                            <option
                                                value="{{ $c->id }}"
                                                data-req="{{ $c->requisition }}"
                                                data-brand="{{ $c->brand }}"
                                                data-serial="{{ $c->{'serial_s/n'} ?? '' }}"
                                            >
                                                {{ $c->requisition }} — {{ $c->brand }} — {{ $c->{'serial_s/n'} ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('computation_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Busca por requisición, marca o serial.</div>
                                </div>

                                {{-- Fecha --}}
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Fecha del movimiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" name="movement_date" id="movement_date"
                                               class="form-control @error('movement_date') is-invalid @enderror"
                                               value="{{ old('movement_date', now()->toDateString()) }}" required>
                                        @error('movement_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Tipo (solo salida) --}}
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold d-block">Tipo</label>
                                    <div class="mv-type">
                                        <label class="pill active">
                                            <input type="radio" name="type" value="salida" checked>
                                            <i class="bi bi-box-arrow-up-right"></i> Salida
                                        </label>
                                    </div>
                                    <div class="form-text">Para equipos, el movimiento es de <b>Salida</b>.</div>
                                </div>

                                {{-- Cantidad --}}
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Cantidad</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        <input type="number" min="1" step="1" name="amount" id="amount"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               value="{{ old('amount', 1) }}" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Para laptops/PC suele ser 1.</div>
                                </div>

                                {{-- Entregado a --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Entregado a</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                        <input type="text" name="delivered_to" id="delivered_to"
                                               class="form-control @error('delivered_to') is-invalid @enderror"
                                               value="{{ old('delivered_to') }}" placeholder="Nombre de quien recibe" required>
                                        @error('delivered_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Área --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Área</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                        <input list="areas_suggestions" type="text" name="area" id="area"
                                               class="form-control @error('area') is-invalid @enderror"
                                               value="{{ old('area') }}" placeholder="Ej: Sistemas, Producción…" required>
                                        @error('area')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <datalist id="areas_suggestions">
                                        <option value="Sistemas"></option>
                                        <option value="Producción"></option>
                                        <option value="Mantenimiento"></option>
                                        <option value="Calidad"></option>
                                        <option value="Gerencia"></option>
                                        <option value="Comercial"></option>
                                        <option value="Financiera"></option>
                                        <option value="Despacho"></option>
                                        <option value="Gestion Humana"></option>
                                    </datalist>
                                </div>

                                {{-- Tomado por --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Sacado por</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-gear"></i></span>
                                        <input list="takenby_suggestions" type="text" name="taken_by" id="taken_by"
                                               class="form-control @error('taken_by') is-invalid @enderror"
                                               value="{{ old('taken_by') }}" placeholder="Responsable de la entrega" required>
                                        @error('taken_by')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <datalist id="takenby_suggestions">
                                        <option value="Yuderly Chabarro"></option>
                                        <option value="Yuli Hernandez"></option>
                                        <option value="Diego Salazar"></option>
                                        <option value="Kevin Murcia"></option>
                                        <option value="Fabian Torres"></option>
                                        <option value="Kevin Arranque"></option>
                                    </datalist>
                                </div>

                                {{-- Sede --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Sede</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <select name="seat" id="seat"
                                               class="form-control @error('seat') is-invalid @enderror"
                                               required>
                                            <option value="">Seleccione una sede</option>
                                            <option value="Rivera" {{ old('seat') == 'Rivera' ? 'selected' : '' }}>Rivera</option>
                                            <option value="Porvenir" {{ old('seat') == 'Porvenir' ? 'selected' : '' }}>Porvenir</option>
                                            <option value="Caimos" {{ old('seat') == 'Caimos' ? 'selected' : '' }}>Caimos</option>
                                            <option value="Garzon" {{ old('seat') == 'Garzon' ? 'selected' : '' }}>Garzon</option>
                                            <option value="Berlin" {{ old('seat') == 'Berlin' ? 'selected' : '' }}>Berlin</option>
                                            <option value="Betania" {{ old('seat') == 'Betania' ? 'selected' : '' }}>Betania</option>
                                        </select>
                                        @error('seat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <datalist id="seats_suggestions">
                                        <option value="Servidor"></option>
                                        <option value="Piso 3"></option>
                                        <option value="Piso 2"></option>
                                        <option value="Bodega"></option>
                                    </datalist>

                                </div>
                                {{-- Observación --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Observación (opcional)</label>
                                    <textarea name="observation" id="observation" rows="2"
                                              class="form-control @error('observation') is-invalid @enderror"
                                              placeholder="Detalles adicionales del movimiento…">{{ old('observation') }}</textarea>
                                    @error('observation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="button" id="previewBtn" class="btn btn-info">
                                    <i class="bi bi-eye"></i> Previsualizar
                                </button>
                                <button class="btn btn-success">
                                    <i class="bi bi-save2 me-1"></i> Guardar
                                </button>
                                <a href="{{ route('computed_movements.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Panel lateral (info del equipo seleccionado) --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm h-100 sticky-top" style="top: 88px;">
                    <div class="card-header">
                        <i class="bi bi-info-square me-2"></i>Equipo seleccionado
                    </div>
                    <div class="card-body">
                        <div id="eq-empty" class="text-muted small">Selecciona un equipo para ver detalles.</div>
                        <div id="eq-info" style="display:none;">
                            <div class="mb-1"><span class="text-muted small">Requisición:</span> <b id="eq-req">—</b></div>
                            <div class="mb-1"><span class="text-muted small">Marca:</span> <b id="eq-brand">—</b></div>
                            <div class="mb-2"><span class="text-muted small">Serial:</span> <b id="eq-serial">—</b></div>
                            <hr>
                            <div class="small text-muted">Se registrará como <b>Salida</b> el {{ now()->toDateString() }} salvo que cambies la fecha.</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        Tip: usa el buscador del combo para filtrar por <b>requisición</b>, <b>marca</b> o <b>serial</b>.
                    </div>
                </div>
            </div>
        </div>

        {{-- FAB para tips si lo usas en esta vista (opcional) --}}
        <button type="button" class="tips-fab d-none" data-tips-open aria-label="Mostrar tips" title="Mostrar tips">
            <i class="bi bi-info-circle"></i>
        </button>

    </div>
</div>

{{-- Estilos mínimos para las píldoras --}}
<style>
.mv-type { display:flex; gap:.6rem; }
.mv-type .pill{ user-select:none; cursor:default; border:1px solid #dee2e6; padding:.55rem .9rem; border-radius:999px; display:inline-flex; align-items:center; gap:.4rem; background:#0d6efd; color:#fff; border-color:#0d6efd; }
.mv-type .pill input{ display:none; }
</style>

{{-- Interactividad: Tom Select + previsualización --}}
<script>
(() => {
    const sel = document.getElementById('computation_id');
    const amount = document.getElementById('amount');
    const date = document.getElementById('movement_date');
    const delivered = document.getElementById('delivered_to');
    const area = document.getElementById('area');
    const taken = document.getElementById('taken_by');
    const seat = document.getElementById('seat');
    const form = document.getElementById('cm-form');
    const previewBtn = document.getElementById('previewBtn');

    // Panel lateral
    const eqEmpty  = document.getElementById('eq-empty');
    const eqInfo   = document.getElementById('eq-info');
    const eqReq    = document.getElementById('eq-req');
    const eqBrand  = document.getElementById('eq-brand');
    const eqSerial = document.getElementById('eq-serial');

    function getSelectedData(){
        const opt = sel.options[sel.selectedIndex];
        if(!opt) return null;
        return {
            id: sel.value,
            req: opt.getAttribute('data-req') || '',
            brand: opt.getAttribute('data-brand') || '',
            serial: opt.getAttribute('data-serial') || '',
        };
    }

    function updateSide(){
        const data = getSelectedData();
        if(!data){
            eqEmpty.style.display = '';
            eqInfo.style.display = 'none';
            return;
        }
        eqEmpty.style.display = 'none';
        eqInfo.style.display  = '';
        eqReq.textContent     = data.req || '—';
        eqBrand.textContent   = data.brand || '—';
        eqSerial.textContent  = data.serial || '—';
    }

    // Validación suave al enviar directo
    form.addEventListener('submit', (e) => {
        if(!form.checkValidity()){
            e.preventDefault();
            form.closest('.card').animate(
                [{transform:'translateX(0)'},{transform:'translateX(-6px)'},{transform:'translateX(6px)'},{transform:'translateX(0)'}],
                {duration:220}
            );
            Swal.fire({ icon:'warning', title:'Campos incompletos', text:'Revisa los campos requeridos.' });
        }
    });

    // Previsualización con SweetAlert2
    function buildPreview() {
        const data = getSelectedData() || {};
        return `
            <div class="text-start">
                <table class="table table-sm">
                    <tr><td style="width:38%;">Equipo</td><td>${data.req || '—'} — ${data.brand || '—'} — ${data.serial || '—'}</td></tr>
                    <tr><td>Tipo</td><td><b>Salida</b></td></tr>
                    <tr><td>Cantidad</td><td>${amount.value || '—'}</td></tr>
                    <tr><td>Fecha</td><td>${date.value || '—'}</td></tr>
                    <tr><td>Entregado a</td><td>${delivered.value || '—'}</td></tr>
                    <tr><td>Área</td><td>${area.value || '—'}</td></tr>
                    <tr><td>Sacado por</td><td>${taken.value || '—'}</td></tr>
                    <tr><td>Sede</td><td>${seat.value || '—'}</td></tr>
                </table>
            </div>
        `;
    }

    previewBtn.addEventListener('click', () => {
        if(!form.checkValidity()){
            form.reportValidity?.();
            return Swal.fire({ icon:'warning', title:'Completa los campos', text:'Verifica equipo, fecha y datos de entrega.' });
        }
        Swal.fire({
            title: 'Previsualización',
            html: buildPreview(),
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Confirmar y guardar',
            cancelButtonText: 'Volver',
            width: 680,
            preConfirm: () => form.submit()
        });
    });

    // Eventos
    sel.addEventListener('change', updateSide);
    updateSide();

    // Tom Select (buscable)
    const ts = new TomSelect('#computation_id', {
        plugins: { clear_button: { title: 'Limpiar' } },
        placeholder: 'Escribe requisición, marca o serial…',
        searchField: ['text','req','brand','serial'],
        maxOptions: 5000,
        preload: true,
        render: {
            option: (d, e) => `
                <div class="py-1">
                    <div><strong>${e(d.req || '')}</strong> — ${e(d.brand || d.text || '')}</div>
                    <div class="text-muted small">Serial: ${e(d.serial || '')}</div>
                </div>
            `,
            item:   (d, e) => `<div>${e(d.req || '')} — ${e(d.brand || d.text || '')} — ${e(d.serial || '')}</div>`
        },
        onChange: function(){
            const real = document.getElementById('computation_id');
            real && real.dispatchEvent(new Event('change', {bubbles:true}));
        }
    });
})();
</script>

@endsection

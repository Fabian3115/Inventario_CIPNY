@extends('layouts.master')

@section('title', 'Editar movimiento de equipo')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
<div class="glassify-page">
    <div class="container-xxl p-3 glassify-shell">

        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-pc-display-horizontal fs-5 text-primary"></i>
                <h2 class="h5 mb-0">Editar movimiento #{{ $movement->id }}</h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('computed_movements.index') }}">Movimientos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>

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
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header brand">
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-pencil-square"></i>
                            <span class="fw-semibold">Formulario de edición</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <form id="cm-form" method="POST" action="{{ route('computed_movements.update', $movement) }}" novalidate>
                            @csrf @method('PUT')

                            <div class="row g-3">
                                {{-- Equipo --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Equipo</label>
                                    <select name="computation_id" id="computation_id" class="form-select @error('computation_id') is-invalid @enderror" required>
                                        <option value="" hidden>Seleccione un equipo…</option>
                                        @foreach ($computations as $c)
                                            <option
                                                value="{{ $c->id }}"
                                                data-req="{{ $c->requisition }}"
                                                data-brand="{{ $c->brand }}"
                                                @selected($movement->computation_id == $c->id)
                                            >
                                                {{ $c->requisition }} — {{ $c->brand }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('computation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Fecha --}}
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Fecha</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" name="movement_date" id="movement_date"
                                               class="form-control @error('movement_date') is-invalid @enderror"
                                               value="{{ old('movement_date', optional($movement->movement_date)->format('Y-m-d')) }}" required>
                                        @error('movement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                    <div class="form-text">Fijo como <b>Salida</b>.</div>
                                </div>

                                {{-- Cantidad --}}
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Cantidad</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        <input type="number" min="1" step="1" name="amount" id="amount"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               value="{{ old('amount', $movement->amount) }}" required>
                                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Entregado a --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Entregado a</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                        <input type="text" name="delivered_to" id="delivered_to"
                                               class="form-control @error('delivered_to') is-invalid @enderror"
                                               value="{{ old('delivered_to', $movement->delivered_to) }}" required>
                                        @error('delivered_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Área --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Área</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                        <input type="text" name="area" id="area"
                                               class="form-control @error('area') is-invalid @enderror"
                                               value="{{ old('area', $movement->area) }}" required>
                                        @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Tomado por --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Sacado por</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-gear"></i></span>
                                        <input type="text" name="taken_by" id="taken_by"
                                               class="form-control @error('taken_by') is-invalid @enderror"
                                               value="{{ old('taken_by', $movement->taken_by) }}" required>
                                        @error('taken_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Sede --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Sede</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="seat" id="seat"
                                               class="form-control @error('seat') is-invalid @enderror"
                                               value="{{ old('seat', $movement->seat) }}" required>
                                        @error('seat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="button" id="previewBtn" class="btn btn-info">
                                    <i class="bi bi-eye"></i> Previsualizar
                                </button>
                                <button class="btn btn-primary">
                                    <i class="bi bi-check2-circle me-1"></i> Actualizar
                                </button>
                                <a href="{{ route('computed_movements.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Volver
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Panel lateral --}}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.mv-type { display:flex; gap:.6rem; }
.mv-type .pill{ user-select:none; cursor:default; border:1px solid #dee2e6; padding:.55rem .9rem; border-radius:999px; display:inline-flex; align-items:center; gap:.4rem; background:#0d6efd; color:#fff; border-color:#0d6efd; }
.mv-type .pill input{ display:none; }
</style>

<script>
(() => {
    const sel = document.getElementById('computation_id');
    const amount = document.getElementById('amount');
    const date = document.getElementById('movement_date');
    const delivered = document.getElementById('delivered_to');
    const area = document.getElementById('area');
    const taken = document.getElementById('taken_by');
    const seat = document.getElementById('seat');
    const previewBtn = document.getElementById('previewBtn');
    const form = document.getElementById('cm-form');

    // Panel lateral
    const eqEmpty = document.getElementById('eq-empty');
    const eqInfo  = document.getElementById('eq-info');
    const eqReq   = document.getElementById('eq-req');
    const eqBrand = document.getElementById('eq-brand');

    function getSelectedData(){
        const opt = sel.options[sel.selectedIndex];
        if(!opt) return null;
        return {
            id: sel.value,
            req: opt.getAttribute('data-req') || opt.textContent || '',
            brand: opt.getAttribute('data-brand') || '',
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
        eqReq.textContent   = data.req || '—';
        eqBrand.textContent = data.brand || '—';
    }

    // Validación amable
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

    // Previsualizar
    function buildPreview(){
        const data = getSelectedData() || {};
        return `
            <div class="text-start">
                <table class="table table-sm">
                    <tr><td style="width:38%;">Equipo</td><td>${data.req || '—'} — ${data.brand || '—'}</td></tr>
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
            confirmButtonText: 'Confirmar y actualizar',
            cancelButtonText: 'Volver',
            width: 680,
            preConfirm: () => form.submit()
        });
    });

    // Init
    sel.addEventListener('change', updateSide);
    updateSide();

    // Tom Select
    new TomSelect('#computation_id', {
        plugins: { clear_button: { title: 'Limpiar' } },
        placeholder: 'Escribe requisición o marca…',
        searchField: ['text','req','brand'],
        maxOptions: 5000,
        preload: true,
        render: {
            option: (d, e) => `<div class="py-1"><div><strong>${e(d.req || '')}</strong> — ${e(d.brand || d.text || '')}</div></div>`,
            item:   (d, e) => `<div>${e(d.req || '')} — ${e(d.brand || d.text || '')}</div>`
        },
        onChange: function(){
            const real = document.getElementById('computation_id');
            real && real.dispatchEvent(new Event('change', {bubbles:true}));
        }
    });
})();
</script>
@endsection

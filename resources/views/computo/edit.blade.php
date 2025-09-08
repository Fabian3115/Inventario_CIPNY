@extends('layouts.master')

@section('title', 'Editar equipo')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">

@section('content')
<div class="glassify-page">
    <div class="container-xxl p-3 glassify-shell">

        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-pc-display-horizontal fs-5 text-primary"></i>
                <h2 class="h5 mb-0">Editar equipo #{{ $computation->id }}</h2>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('computations.index') }}">Equipos</a></li>
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
                        <form method="POST" action="{{ route('computations.update', $computation) }}" novalidate>
                            @csrf @method('PUT')

                            <div class="row g-3">
                                {{-- Requisición --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Requisición</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                        <input type="text" name="requisition" id="requisitionInput"
                                               class="form-control @error('requisition') is-invalid @enderror"
                                               value="{{ old('requisition', $computation->requisition) }}"
                                               placeholder="N° requisición / soporte" required>
                                        @error('requisition') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Marca --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Marca</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="brand" id="brandInput"
                                               class="form-control @error('brand') is-invalid @enderror"
                                               value="{{ old('brand', $computation->brand) }}" required>
                                        @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-text">
                                        Se guardará como: <strong id="brandPreview">—</strong>
                                    </div>
                                </div>

                                {{-- Serial (S/N) --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Serial (S/N)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        <input type="text" name="serial_s/n" id="serialInput"
                                               class="form-control @error('serial_s/n') is-invalid @enderror"
                                               value="{{ old('serial_s/n', $computation->{'serial_s/n'}) }}" required>
                                        @error('serial_s/n') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-text">
                                        Se guardará como: <strong id="serialPreview">—</strong>
                                    </div>

                                {{-- Tipo (opcional) --}}
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-semibold">Tipo (opcional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-laptop"></i></span>
                                        <select name="type" id="typeInput"
                                                class="form-control @error('type') is-invalid @enderror"
                                                placeholder="Ej: Portátil, Sobremesa, All-in-One">
                                            <option value="">Selecciona un tipo</option>
                                            <option value="Portatil" {{ old('type', $computation->type) === 'Portatil' ? 'selected' : '' }}>Portátil</option>
                                            <option value="Pc Escritorio" {{ old('type', $computation->type) === 'Pc Escritorio' ? 'selected' : '' }}>Pc Escritorio</option>
                                            <option value="Todo-en-uno" {{ old('type', $computation->type) === 'Todo-en-uno' ? 'selected' : '' }}>Todo-en-uno</option>
                                            <option value="Impresora" {{ old('type', $computation->type) === 'Impresora' ? 'selected' : '' }}>Impresora</option>
                                        </select>
                                        {{-- <input type="text" name="type" id="typeInput"
                                               class="form-control @error('type') is-invalid @enderror"
                                               value="{{ old('type', $computation->type) }}"
                                               placeholder="Ej: Portátil, Sobremesa, All-in-One"> --}}
                                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-text">
                                        Describe el tipo de equipo, si lo consideras necesario.
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button class="btn btn-primary">
                                    <i class="bi bi-check2-circle me-1"></i> Actualizar
                                </button>
                                <a href="{{ route('computations.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Volver
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header tips d-flex align-items-center gap-2 text-white">
                        <i class="bi bi-info-circle"></i><span class="fw-semibold">Consejos</span>
                    </div>
                    <div class="card-body small">
                        <ul class="mb-0">
                            <li>La marca se normaliza a Título.</li>
                            <li>El serial se almacena en MAYÚSCULAS.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Normalización/Preview --}}
<script>
(() => {
    const brand = document.getElementById('brandInput');
    const brandPreview = document.getElementById('brandPreview');
    const serial = document.getElementById('serialInput');
    const serialPreview = document.getElementById('serialPreview');

    const toTitle = s => {
        let out = (s || '').toLocaleLowerCase('es-CO');
        out = out.replace(/\b\p{L}/gu, m => m.toLocaleUpperCase('es-CO'));
        return out.replace(/\s+/g, ' ').trim();
    };

    const renderBrand = () => { brandPreview.textContent = toTitle(brand.value) || '—'; };
    const renderSerial = () => { serialPreview.textContent = (serial.value || '').toUpperCase().trim() || '—'; };

    brand?.addEventListener('input', renderBrand);
    brand?.addEventListener('blur', () => { brand.value = toTitle(brand.value); renderBrand(); });
    brand?.form?.addEventListener('submit', () => { brand.value = toTitle(brand.value); });

    serial?.addEventListener('input', renderSerial);
    serial?.addEventListener('blur', () => { serial.value = (serial.value || '').toUpperCase().trim(); renderSerial(); });
    serial?.form?.addEventListener('submit', () => { serial.value = (serial.value || '').toUpperCase().trim(); });

    renderBrand(); renderSerial();
})();
</script>
@endsection

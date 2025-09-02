@extends('layouts.master')

@section('title', 'Importar Movimientos')

<link rel="stylesheet" href="{{ asset('css/movimientos.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Importar movimientos desde Excel</h5>
            <a href="{{ route('movements.template') }}" class="btn btn-outline-primary">
                <i class="bi bi-filetype-xlsx"></i> Descargar plantilla
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('movements.import') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Archivo Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="form-control" required>
                    <div class="form-text">
                        Encabezados requeridos: <code>product_code, date_products, type, amount, delivered_to, area, taken_by</code>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dry_run" id="dry_run" value="1">
                        <label class="form-check-label" for="dry_run">
                            Simular (validar y mostrar resultados sin guardar cambios)
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <button class="btn btn-success">
                        <i class="bi bi-cloud-arrow-up"></i> Procesar archivo
                    </button>
                </div>
            </form>

            @if(session('import_summary'))
                @php($s = session('import_summary'))
                <hr class="my-4">
                <h6 class="mb-3">Resultado de la importación</h6>

                <ul class="list-unstyled small">
                    <li><b>Total filas leídas:</b> {{ $s['total'] }}</li>
                    <li><b>Insertadas:</b> {{ $s['inserted'] }}</li>
                    <li><b>Fallidas:</b> {{ $s['failed'] }}</li>
                    <li><b>Simulación:</b> {{ $s['dry_run'] ? 'Sí' : 'No' }}</li>
                </ul>

                @if(!empty($s['errors']))
                    <div class="alert alert-warning">
                        <b>Errores:</b>
                        <ul class="mb-0">
                            @foreach($s['errors'] as $err)
                                <li>Fila {{ $err['row'] }} — {{ $err['message'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: '{{ $s["failed"] ? "warning" : "success" }}',
                            title: 'Importación {{ $s["dry_run"] ? "simulada" : "completada" }}',
                            html: `<div class="text-start">
                                    <p><b>Total:</b> {{ $s['total'] }}</p>
                                    <p><b>Insertadas:</b> {{ $s['inserted'] }}</p>
                                    <p><b>Fallidas:</b> {{ $s['failed'] }}</p>
                                   </div>`,
                            confirmButtonText: 'Ok'
                        });
                    });
                </script>
            @endif

        </div>
    </div>
</div>
@endsection

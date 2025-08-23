@extends('layouts.master')

@section('content')
    {{-- =========================
     Hero
========================== --}}
    <section class="welcome-hero position-relative overflow-hidden rounded-4 p-0 p-md-5 mb-4 shadow-sm">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-teal fw-semibold">Inventario</span>
                    <span class="text-white-50 small">C.I. Piscícola New York</span>
                </div>
                <h2 class="display-6 text-white fw-bold mb-2">Bienvenido al Sistema de Inventario</h2>
                <p class="lead text-white-50 mb-4">
                    Registra entradas y salidas, administra productos y controla existencias en tiempo real.
                </p>

                {{-- Atajos principales (animados) --}}
                <div class="d-flex flex-wrap gap-2 gap-md-3">
                    <a href="{{ route('products.create') }}" class="quick-action has-anim" data-anim-delay="0">
                        <i class="bi bi-plus-circle"></i>
                        <span>Nuevo producto</span>
                    </a>
                    <a href="{{ route('movements.in.create') }}" class="quick-action has-anim" data-anim-delay="80">
                        <i class="bi bi-box-arrow-in-down"></i>
                        <span>Registrar entrada</span>
                    </a>
                    <a href="{{ route('movements.out.create') }}" class="quick-action has-anim" data-anim-delay="160">
                        <i class="bi bi-box-arrow-up"></i>
                        <span>Registrar salida</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="quick-action has-anim" data-anim-delay="240">
                        <i class="bi bi-list-ul"></i>
                        <span>Ver productos</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                {{-- Tarjeta de estado del sistema --}}
                <div class="card border-0 shadow-lg rounded-4 glassy">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0">Estado del sistema</h5>
                            <span class="badge rounded-pill bg-teal badge-pulse">
                                <i class="fas fa-water me-1"></i> Operativo
                            </span>
                        </div>
                        <ul class="list-unstyled mb-0 small">
                            <li class="d-flex align-items-center mb-2">
                                <i class="bi bi-check2-circle me-2"></i> Integridad de datos OK
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="bi bi-check2-circle me-2"></i> Conexión a base de datos estable
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="bi bi-check2-circle me-2"></i> Último backup: <strong class="ms-1">hoy</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Onda decorativa --}}
        <svg class="hero-wave" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,0 C300,100 900,-40 1200,60 L1200,120 L0,120 Z" />
        </svg>
    </section>

    {{-- =========================
     KPIs / Tarjetas rápidas
========================== --}}
    <section class="mb-4">
        <div class="row g-3 g-md-4">
            <div class="col-6 col-lg-3">
                <div class="kpi-card has-anim" data-anim-delay="0">
                    <div class="kpi-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="kpi-body">
                        <span class="kpi-label">Productos</span>
                        <span class="kpi-value">{{ number_format($stats['products_total'] ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="kpi-card has-anim" data-anim-delay="80">
                    <div class="kpi-icon">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                    <div class="kpi-body">
                        <span class="kpi-label">Entradas (30d)</span>
                        <span class="kpi-value">{{ number_format($stats['ins_30d'] ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="kpi-card has-anim" data-anim-delay="160">
                    <div class="kpi-icon">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                    <div class="kpi-body">
                        <span class="kpi-label">Salidas (30d)</span>
                        <span class="kpi-value">{{ number_format($stats['outs_30d'] ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="kpi-card has-anim" data-anim-delay="240">
                    <div class="kpi-icon">
                        <i class="bi bi-thermometer-half"></i>
                    </div>
                    <div class="kpi-body">
                        <span class="kpi-label">Stock bajo</span>
                        <span class="kpi-value">{{ number_format($stats['low_stock'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
{{-- =========================
     Modal “Stock Bajo / Agotados” fullscreen
========================== --}}
@if(isset($out_of_stock) && $out_of_stock->count() > 0)
<div class="modal fade" id="stockAlertModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered">
    <div class="modal-content border-0">
      {{-- Header con gradiente --}}
      <div class="modal-header border-0" style="background: linear-gradient(120deg, var(--ny-blue-700), var(--ny-blue-500));">
        <div class="w-100 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                  style="width:54px;height:54px;background:rgba(255,255,255,.15);color:#fff;">
              <i class="bi bi-exclamation-triangle-fill fs-3"></i>
            </span>
            <div class="text-white">
              <h3 class="mb-0 fw-bold">¡Atención! Solicitar implementos/productos</h3>
              <small class="text-white-50">Se detectaron referencias con stock bajo o agotado</small>
            </div>
          </div>

          <div class="mt-3 mt-md-0">
            <span class="badge bg-teal fs-6">
              {{ $out_of_stock->count() }} {{ Str::plural('producto', $out_of_stock->count()) }}
            </span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body p-3 p-md-4">
        <div class="row g-3">
          <div class="col-12">
            <div class="alert border-0" style="background: var(--ny-teal-200); color:#0b3b4f;">
              <div class="d-flex flex-wrap align-items-center gap-2">
                <i class="bi bi-megaphone-fill"></i>
                <strong>Recomendación:</strong>
                Genera una solicitud de compra para las referencias listadas a continuación.
              </div>
            </div>
          </div>

          {{-- Chips con CÓDIGOS en badges --}}
          <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
              @foreach($out_of_stock as $p)
                <span class="badge rounded-pill" style="background:#eaf7fb;color:#116e91;border:1px solid #cfe2f3;">
                  {{ $p->code }}
                </span>
              @endforeach
            </div>
          </div>

          {{-- Tabla compacta --}}
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th style="min-width:120px">Código</th>
                        <th>Descripción</th>
                        <th class="text-center" style="width:120px">Stock</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($out_of_stock as $p)
                        <tr>
                          <td class="fw-semibold">{{ $p->code }}</td>
                          <td class="text-muted">{{ $p->description }}</td>
                          <td class="text-center">
                            <span class="badge {{ (int)$p->stock <= 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                              {{ (int)$p->stock }}
                            </span>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                  * Stock en tiempo real. Verifica existencias físicas antes de solicitar.
                </div>
                <div class="d-flex gap-2">
                  <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> Ver productos
                  </a>
                  {{-- Reemplaza esta ruta por tu flujo de compras/solicitudes si lo tienes --}}
                  <a href="#" class="btn btn-secondary-sena btn-sm">
                    <i class="bi bi-cart-plus me-1"></i> Generar solicitud de compra
                  </a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer bg-white border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
@endif


{{-- =========================
     Scripts locales (usa las mismas micro-animaciones)
========================== --}}
<script>
    // Stagger reveal de atajos y KPIs
    (function revealWelcome() {
        const cards = document.querySelectorAll('.has-anim');
        cards.forEach(el => {
            const d = parseInt(el.getAttribute('data-anim-delay') || '0', 10);
            setTimeout(() => el.classList.add('is-revealed'), d);
        });
    })();
</script>


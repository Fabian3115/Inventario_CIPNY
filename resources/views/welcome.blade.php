@extends('layouts.master')

@section('title', 'Inventario • Inicio')

@push('styles')
    <style>
        .welcome-hero {
            background: linear-gradient(120deg, var(--ny-blue-700, #004aad), var(--ny-red, #d72638));
            position: relative;
            padding: clamp(1rem, 3vw, 2.5rem);
        }

        .welcome-hero .badge.bg-teal {
            background: var(--ny-teal, #16a085) !important;
        }

        .welcome-hero .hero-wave {
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            width: 100%;
            height: 70px;
            fill: rgba(255, 255, 255, 0.15);
        }

        .glassy {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .quick-action {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .6rem .9rem;
            border-radius: 14px;
            text-decoration: none;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .18);
            transform: translateY(8px);
            opacity: 0;
            transition: all .5s ease;
        }

        .quick-action:hover {
            background: rgba(255, 255, 255, .18)
        }

        .kpi-card {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1rem;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .06);
            transform: translateY(10px);
            opacity: 0;
            transition: all .5s ease;
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(120deg, var(--ny-blue-700, #004aad), var(--ny-blue-500, #3b82f6));
            color: #fff;
        }

        .kpi-label {
            display: block;
            font-size: .8rem;
            color: #6b7280;
        }

        .kpi-value {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .has-anim.is-revealed {
            transform: none;
            opacity: 1
        }

        /* asegurar que el modal quede encima */
        .modal {
            z-index: 1065;
        }

        .modal-backdrop {
            z-index: 1060;
        }
    </style>
@endpush

@section('content')
    {{-- === Hero === --}}
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
                    <a href="" class="quick-action has-anim" data-anim-delay="80">
                        <i class="bi bi-box-arrow-in-down"></i>
                        <span>Registrar Movimiento</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="quick-action has-anim" data-anim-delay="240">
                        <i class="bi bi-list-ul"></i>
                        <span>Ver productos</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                {{-- Tarjeta de estado del sistema (glass) --}}
                <div class="card border-0 shadow-lg rounded-4 glassy">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0">Estado del sistema</h5>
                            <span class="badge rounded-pill bg-teal badge-pulse">
                                <i class="bi bi-activity me-1"></i> Operativo
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

    {{-- === KPIs === --}}
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

@push('scripts')
    <script>
        (function() {
            // Animación de entrada de “atajos” y KPIs
            document.querySelectorAll('.has-anim').forEach(el => {
                const d = parseInt(el.getAttribute('data-anim-delay') || '0', 10);
                setTimeout(() => el.classList.add('is-revealed'), d);
            });

            // Datos que VIENEN del controlador actual
            const AGOTADOS = @json($agotados ?? []);
            const BAJO = @json($bajo_stock_2 ?? []);

            if ((!Array.isArray(AGOTADOS) || !AGOTADOS.length) &&
                (!Array.isArray(BAJO) || !BAJO.length)) {
                return; // nada que mostrar
            }

            // ---------- Helpers ----------
            const badge = (label, cls) => `<span class="badge ${cls} rounded-pill">${label}</span>`;

            // Obtiene el nombre del almacén, sin importar cómo venga del backend
            function getWarehouseName(p) {
                if (p?.warehouse_name) return p.warehouse_name; // columna directa
                if (p?.warehouse?.name) return p.warehouse.name; // relación -> name
                if (p?.warehouse) return p.warehouse; // texto en 'warehouse'
                if (p?.store_name) return p.store_name; // alias comunes
                if (p?.store) return p.store;
                if (p?.location_name) return p.location_name;
                if (p?.location) return p.location;
                return '-';
            }

            // Chips con tooltip del almacén
            const renderChips = (rows, bg, text, border) => !rows?.length ? '' : `
    <div class="mb-3 d-flex flex-wrap gap-2">
      ${rows.map(p=>{
        const wh = getWarehouseName(p);
        return `
                  <span class="badge rounded-pill"
                        title="Almacén: ${wh}"
                        style="background:${bg};color:${text};border:1px solid ${border};">
                    ${p.code ?? ''}
                  </span>
                `;
      }).join('')}
    </div>
  `;

            // Tabla con columna "Almacén"
            const renderTable = rows => {
                if (!rows?.length) return `<div class="text-muted small">Sin registros.</div>`;
                const trs = rows.map(p => {
                    const wh = getWarehouseName(p);
                    const stock = (+p.stock) || 0;
                    const cls = stock <= 0 ? 'bg-danger' : 'bg-warning text-dark';
                    return `
        <tr>
          <td class="fw-semibold">${p.code ?? ''}</td>
          <td class="text-muted">${p.description ?? ''}</td>
          <td class="text-muted">${wh}</td>
          <td class="text-center"><span class="badge ${cls}">${stock}</span></td>
        </tr>
      `;
                }).join('');
                return `
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="min-width:120px">Código</th>
              <th>Descripción</th>
              <th style="min-width:120px">Almacén</th>
              <th class="text-center" style="width:120px">Stock</th>
            </tr>
          </thead>
          <tbody>${trs}</tbody>
        </table>
      </div>`;
            };

            // ---------- Modal ----------
            const html = `
  <div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
      <div class="modal-content border-0">
        <div class="modal-header border-0" style="background: linear-gradient(120deg, var(--ny-blue-700,#004aad), var(--ny-blue-500,#3b82f6));">
          <div class="w-100 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
              <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                    style="width:54px;height:54px;background:rgba(255,255,255,.15);color:#fff;">
                <i class="bi bi-cart-x fs-3"></i>
              </span>
              <div class="text-white">
                <h3 class="mb-0 fw-bold">Productos para reponer</h3>
                <small class="text-white-50">Separados por estado de inventario</small>
              </div>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
              ${badge(`${AGOTADOS.length} ${AGOTADOS.length===1?'agotado':'agotados'}`, 'bg-danger fs-6')}
              ${badge(`${BAJO.length} con stock bajo`, 'bg-warning text-dark fs-6')}
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-3 p-md-4">
          <div class="row g-4">
            <div class="col-12 col-lg-6">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="bi bi-exclamation-octagon-fill text-danger me-2"></i> Agotados (0)</h5>
                  ${badge(AGOTADOS.length, 'bg-danger')}
                </div>
                <div class="card-body">
                  ${renderChips(AGOTADOS, '#ffe5e7', '#8b1b28', '#ffcdd2')}
                  ${renderTable(AGOTADOS)}
                </div>
                <div class="card-footer bg-white border-0 d-flex justify-content-end">
                  <a href="#" class="btn btn-danger btn-sm"><i class="bi bi-bag-plus me-1"></i> Solicitar compra (agotados)</a>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="bi bi-thermometer-half text-warning me-2"></i> Bajo stock (&lt; 2)</h5>
                  ${badge(BAJO.length, 'bg-warning text-dark')}
                </div>
                <div class="card-body">
                  ${renderChips(BAJO, '#fff6db', '#7a5a00', '#ffe59a')}
                  ${renderTable(BAJO)}
                </div>
                <div class="card-footer bg-white border-0 d-flex justify-content-end">
                  <a href="#" class="btn btn-warning btn-sm"><i class="bi bi-bag-plus me-1"></i> Solicitar compra (bajo stock)</a>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="alert border-0 mt-1" style="background:#eaf7fb;color:#0b3b4f;">
                <i class="bi bi-megaphone-fill me-2"></i>
                <strong>Nota:</strong> Verifica existencias físicas antes de generar la solicitud de compra.
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-white border-0">
          <a href="{{ route('products.index') }}" class="btn btn-outline-primary"><i class="bi bi-list-ul me-1"></i> Ver todos los productos</a>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>`;

            const wrap = document.createElement('div');
            wrap.innerHTML = html;
            document.body.appendChild(wrap.firstElementChild);

            // Espera a que Bootstrap esté disponible (por si hay latencia de CDN)
            const start = performance.now();
            (function waitForBootstrap() {
                if (window.bootstrap && bootstrap.Modal && typeof bootstrap.Modal.getOrCreateInstance ===
                    'function') {
                    const el = document.getElementById('restockModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(el, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    setTimeout(() => modal.show(), 150);
                } else {
                    if (performance.now() - start < 3000) { // intenta durante 3s
                        return setTimeout(waitForBootstrap, 50);
                    }
                    console.warn('Bootstrap Modal no disponible a tiempo.');
                }
            })();
        })();
    </script>
@endpush

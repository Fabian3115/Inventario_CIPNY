<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>C.I. Piscícola New York – Sistema de Inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Tom Select + tema Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>


    <link rel="shortcut icon" href="{{ asset('images/PNY.png') }}" type="image/x-icon">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/master.css') }}">
</head>

<body>

    <div>
        <!-- Topbar -->
        <header class="topbar nav-animate d-flex align-items-center px-3 shadow-sm sticky-top">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/pny.png') }}" class="logo me-2" alt="C.I. Piscícola New York">
                <div class="d-flex flex-column">
                    <h1 class="h6 mb-0 text-white fw-semibold">C.I. Piscícola New York</h1>
                    <small class="text-white-50">Sistema de Inventario</small>
                </div>
            </div>

            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="d-none d-md-inline text-white-50">
                    <i class="fas fa-fish me-1"></i> Calidad garantizada
                </span>
            </div>
        </header>

        <!-- Menú de íconos horizontal -->
        <nav class="nav-icons py-2 bg-white shadow-sm">
            <ul
                class="menu-scroll d-flex align-items-center justify-content-start gap-2 gap-md-3 flex-nowrap flex-md-wrap mb-0 list-unstyled px-3">

                <!-- Productos -->
                <li class="nav-item1 dropdown position-static">
                    <a id="ddProductsToggle" href="javascript:void(0)"
                        class="nav-item-link dropdown-toggle text-decoration-none has-anim" data-anim-delay="0"
                        role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static"
                        data-bs-boundary="viewport" data-bs-offset="0,6" aria-expanded="false"
                        aria-controls="ddProductsMenu">
                        <span class="icon-bubble">
                            <i class="bi bi-box-seam fs-5"></i>
                        </span>
                    </a>

                    <ul id="ddProductsMenu" class="dropdown-menu shadow-sm border-0" aria-labelledby="ddProductsToggle">
                        <li><a class="dropdown-item" href="{{ route('products.create') }}"><i
                                    class="bi bi-plus-circle me-2"></i> Crear producto</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index') }}"><i
                                    class="bi bi-list-ul me-2"></i>
                                Listar productos</a></li>
                    </ul>
                </li>

                <!-- Entrada -->
                <li class="nav-item2 dropdown position-static">
                    <a id="ddInToggle" href="javascript:void(0)"
                        class="nav-item-link dropdown-toggle text-decoration-none has-anim" data-anim-delay="80"
                        role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static"
                        data-bs-boundary="viewport" data-bs-offset="0,6" aria-expanded="false" aria-controls="ddInMenu">
                        <span class="icon-bubble">
                            <i class="bi bi-box-arrow-in-down fs-5"></i>
                        </span>
                    </a>

                    <ul id="ddInMenu" class="dropdown-menu shadow-sm border-0" aria-labelledby="ddInToggle">
                        <li><a class="dropdown-item" href="{{ route('movements.in.create') }}"><i
                                    class="bi bi-plus-lg me-2"></i> Registrar entrada</a></li>
                        <li><a class="dropdown-item" href="{{ route('movements.in.index') }}"><i
                                    class="bi bi-clock-history me-2"></i> Historial de entradas</a></li>
                    </ul>
                </li>

                <!-- Salida -->
                <li class="nav-item3 dropdown position-static">
                    <a id="ddOutToggle" href="javascript:void(0)"
                        class="nav-item-link dropdown-toggle text-decoration-none has-anim" data-anim-delay="160"
                        role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static"
                        data-bs-boundary="viewport" data-bs-offset="0,6" aria-expanded="false"
                        aria-controls="ddOutMenu">
                        <span class="icon-bubble">
                            <i class="bi bi-box-arrow-up fs-5"></i>
                        </span>
                    </a>

                    <ul id="ddOutMenu" class="dropdown-menu shadow-sm border-0" aria-labelledby="ddOutToggle">
                        <li><a class="dropdown-item" href="{{ route('movements.out.create') }}"><i
                                    class="bi bi-dash-lg me-2"></i> Registrar salida</a></li>
                        <li><a class="dropdown-item" href="{{ route('movements.out.index') }}"><i
                                    class="bi bi-clock-history me-2"></i> Historial de salidas</a></li>
                    </ul>
                </li>

                <!-- Estado sistema -->
                <li class="d-none d-md-flex align-items-center ms-2">
                    <span class="badge rounded-pill bg-teal badge-pulse">
                        <i class="fas fa-water me-1"></i> Operativo
                    </span>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Contenido principal -->
    <main class="py-4">
        <div class="container-xxl">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-ny mt-2 pt-2 pb-1">
        <div class="container-xxl">
            <div class="row g-4 align-items-center">
                {{-- Logo + descripción --}}
                <div class="col-md-4 text-center text-md-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <img src="{{ asset('images/pny.png') }}" alt="C.I. Piscícola New York" class="footer-logo">
                        <h5 class="mb-0 text-white fw-bold">C.I. Piscícola New York</h5>
                    </div>
                    <p class="text-white-50 small mb-0">
                        Sistema de Inventario para el control de productos, entradas y salidas.<br>
                        Transparencia y calidad garantizada.
                    </p>
                </div>

                {{-- Navegación rápida --}}
                <div class="col-md-4 text-center">
                    <h6 class="text-uppercase text-white fw-semibold mb-3">Navegación</h6>
                    <ul class="list-unstyled d-flex flex-wrap justify-content-center gap-3 mb-0">
                        <li><a href="{{ route('products.index') }}" class="footer-link"><i
                                    class="bi bi-box-seam me-1"></i> Productos</a></li>
                        <li><a href="{{ route('movements.in.index') }}" class="footer-link"><i
                                    class="bi bi-box-arrow-in-down me-1"></i> Entradas</a></li>
                        <li><a href="{{ route('movements.out.index') }}" class="footer-link"><i
                                    class="bi bi-box-arrow-up me-1"></i> Salidas</a></li>
                    </ul>
                </div>

                {{-- Contacto / redes --}}
                <div class="col-md-4 text-center text-md-end">
                    <h6 class="text-uppercase text-white fw-semibold mb-3">Contacto</h6>
                    <p class="text-white-50 small mb-2">
                        <i class="bi bi-envelope me-1"></i> contacto@piscicolany.com<br>
                        <i class="bi bi-telephone me-1"></i> +57 310 123 4567
                    </p>
                    <div class="d-flex justify-content-center justify-content-md-end gap-2">
                        <a href="#" class="footer-social"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="footer-social"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="footer-social"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

            </div>

            <hr class="footer-divider my-4">

            <div class="text-center small text-white-50">
                © {{ date('Y') }} C.I. Piscícola New York .
                <br>
                Fabian Esteban Torres A — Todos los derechos reservados.
            </div>
        </div>
    </footer>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectors = [
            '#ddProductsToggle',
            '#ddInToggle',
            '#ddOutToggle'
        ];

        const toggles = selectors
            .map(sel => document.querySelector(sel))
            .filter(Boolean);

        // Cerrar los demás al abrir uno
        toggles.forEach(tg => {
            tg.addEventListener('show.bs.dropdown', (ev) => {
                toggles.forEach(other => {
                    if (other !== tg) {
                        const inst = bootstrap.Dropdown.getOrCreateInstance(other);
                        inst.hide();
                    }
                });
            });
        });

        // (Opcional) cerrar al click dentro del menú si es un enlace
        ['#ddProductsMenu', '#ddInMenu', '#ddOutMenu'].forEach(sel => {
            const menu = document.querySelector(sel);
            if (!menu) return;
            menu.addEventListener('click', (e) => {
                const a = e.target.closest('a.dropdown-item');
                if (a) {
                    // deja que navegue y cierra visualmente
                    const toggle = document.querySelector(menu.getAttribute('aria-labelledby') ?
                        `#${menu.getAttribute('aria-labelledby')}` : null);
                    if (toggle) bootstrap.Dropdown.getOrCreateInstance(toggle).hide();
                }
            });
        });
    });
</script>

<script>
    // === Aparición escalonada de tarjetas =========================
    (function revealCards() {
        const cards = document.querySelectorAll('.nav-item-link.has-anim');
        cards.forEach(card => {
            const d = parseInt(card.getAttribute('data-anim-delay') || '0', 10);
            setTimeout(() => card.classList.add('is-revealed'), d);
        });
    })();

    // === Stagger por item cuando se abre un dropdown ===============
    document.addEventListener('show.bs.dropdown', function(ev) {
        const menu = ev.target.querySelector('.dropdown-menu');
        if (!menu) return;
        const items = menu.querySelectorAll('.dropdown-item');
        items.forEach((it, idx) => it.style.setProperty('--i', idx)); // para CSS calc(var(--i))
    });

    // === Ripple al click ==========================================
    function attachRipple(el) {
        el.addEventListener('click', (e) => {
            const preferReduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (preferReduce) return;

            const rect = el.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            el.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove(), {
                once: true
            });
        });
    }
    document.querySelectorAll('.nav-item-link').forEach(attachRipple);

    // === Mantener tu modo exclusivo de dropdowns ===================
    // (dejamos tu listener existente tal cual)
</script>
<script>
    (function showStockAlertOnLoad() {
        const modalEl = document.getElementById('stockAlertModal');
        if (!modalEl) return; // no hay productos bajos, no mostramos nada
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Opcional: delay suave para que cargue el resto de la UI
        const open = () => {
            const m = new bootstrap.Modal(modalEl, {
                backdrop: 'static',
                keyboard: true
            });
            m.show();
        };

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(open, reduce ? 0 : 250);
        } else {
            document.addEventListener('DOMContentLoaded', () => setTimeout(open, reduce ? 0 : 250));
        }
    })();
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Cerrar otros menús cuando uno se abre ---
        const toggleSelectors = ['#ddProductsToggle', '#ddInToggle', '#ddOutToggle'];
        const toggles = toggleSelectors.map(q => document.querySelector(q)).filter(Boolean);
        toggles.forEach(tg => {
            tg.addEventListener('show.bs.dropdown', () => {
                toggles.forEach(other => {
                    if (other !== tg) bootstrap.Dropdown.getOrCreateInstance(other)
                        .hide();
                });
            });
        });

        // --- Portalizar menús para que queden por encima de todo ---
        const portals = new Map(); // menuEl -> { parent, next, onMove }

        function positionMenu(toggleEl, menuEl) {
            const rect = toggleEl.getBoundingClientRect();
            const offsetY = 6; // tu data-bs-offset vertical
            // centramos el menú justo bajo el icono
            menuEl.style.left = (rect.left + rect.width / 2) + 'px';
            menuEl.style.top = (rect.bottom + offsetY) + 'px';
        }

        function portalize(toggleSel, menuSel) {
            const toggleEl = document.querySelector(toggleSel);
            const menuEl = document.querySelector(menuSel);
            if (!toggleEl || !menuEl) return;

            // Asegura instancia Bootstrap
            bootstrap.Dropdown.getOrCreateInstance(toggleEl);

            const onShow = () => {
                const info = {
                    parent: menuEl.parentElement,
                    next: menuEl.nextSibling,
                    onMove: () => positionMenu(toggleEl, menuEl),
                };
                portals.set(menuEl, info);

                // Mover al body y elevar z-index
                menuEl.classList.add('dropdown-portal');
                document.body.appendChild(menuEl);
                info.onMove();

                // Reposicionar en scroll/resize (captura profunda)
                window.addEventListener('scroll', info.onMove, true);
                window.addEventListener('resize', info.onMove, true);
            };

            const onHide = () => {
                const info = portals.get(menuEl);
                if (!info) return;

                window.removeEventListener('scroll', info.onMove, true);
                window.removeEventListener('resize', info.onMove, true);

                menuEl.classList.remove('dropdown-portal');
                // Limpia estilos inline para volver al flujo normal
                menuEl.style.left = '';
                menuEl.style.top = '';
                menuEl.style.transform = '';

                // Regresar al lugar original
                if (info.next && info.next.parentNode) {
                    info.parent.insertBefore(menuEl, info.next);
                } else {
                    info.parent.appendChild(menuEl);
                }
                portals.delete(menuEl);
            };

            toggleEl.addEventListener('show.bs.dropdown', onShow);
            toggleEl.addEventListener('hide.bs.dropdown', onHide);
        }

        portalize('#ddProductsToggle', '#ddProductsMenu');
        portalize('#ddInToggle', '#ddInMenu');
        portalize('#ddOutToggle', '#ddOutMenu');
    });
</script>

</html>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>C.I. Piscícola New York – Sistema de Inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- (Opcional) Tailwind utilidades -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tom Select (si lo usas en otras vistas) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <link rel="shortcut icon" href="{{ asset('images/PNY.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/master.css') }}">
</head>

<body class="ny-bg">

    <!-- ===== HEADER ===== -->
    <header class="ny-header shadow-sm">
        <div class="container-xxl d-flex align-items-center gap-3 py-2">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/pny.png') }}" class="ny-logo" alt="C.I. Piscícola New York">
                <div class="d-flex flex-column">
                    <h1 class="h6 mb-0 text-white fw-semibold">C.I. Piscícola New York</h1>
                    <small class="text-white-50">Sistema de Inventario</small>
                </div>
            </div>

            <div class="ms-auto d-none d-md-flex align-items-center gap-3">
                <span class="text-white-75 small"><i class="bi bi-water me-1"></i> Calidad garantizada</span>
                <span class="badge rounded-pill bg-light-subtle text-black border border-white/25">
                    <i class="bi bi-activity me-1"></i> Operativo
                </span>
            </div>
        </div>
    </header>

    <!-- ===== NAV (botón + opciones al lado) ===== -->
    <nav class="ny-inline-nav" aria-label="Navegación principal">
        <div class="container-xxl ny-inlinebar">
            <!-- Botón hamburguesa SIEMPRE visible; al abrir aparecen opciones a la derecha -->
            <button id="nyMenuBtn" class="ny-hamb btn btn-sm border-0 px-2 py-1" aria-expanded="false"
                aria-controls="nyInline">
                <span class="ny-hamb-burger"></span>
            </button>

            <!-- Contenedor horizontal que se expande junto al botón -->
            <div id="nyInline" class="ny-inline">

                <!-- Inicio -->
                <div class="ny-item">
                    <a href="{{ route('welcome') }}" class="ny-mainbtn">
                        <i class="bi bi-house-door"></i><span>Inicio</span>
                    </a>
                </div>

                <!-- Productos -->
                <div class="ny-item" data-sub="subProducts">
                    <button class="ny-mainbtn" type="button">
                        <i class="bi bi-box-seam"></i><span>Productos</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>
                    <div class="ny-submenu" id="subProducts" role="menu" aria-label="Productos">
                        <a class="ny-subpill" href="{{ route('products.create') }}"><i class="bi bi-plus-circle"></i>
                            Crear</a>
                        <a class="ny-subpill" href="{{ route('products.index') }}"><i class="bi bi-list-ul"></i>
                            Lista</a>
                    </div>
                </div>

                <!-- Entradas -->
                <div class="ny-item" data-sub="subIn">
                    <button class="ny-mainbtn" type="button">
                        <i class="bi bi-box-arrow-in-down"></i><span>Entradas</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>
                    <div class="ny-submenu" id="subIn" role="menu" aria-label="Entradas">
                        <a class="ny-subpill" href="{{ route('movements.in.create') }}"><i class="bi bi-plus-lg"></i>
                            Registrar</a>
                        <a class="ny-subpill" href="{{ route('movements.in.index') }}"><i
                                class="bi bi-clock-history"></i> Historial</a>
                    </div>
                </div>

                <!-- Salidas -->
                <div class="ny-item" data-sub="subOut">
                    <button class="ny-mainbtn" type="button">
                        <i class="bi bi-box-arrow-up"></i><span>Salidas</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>
                    <div class="ny-submenu" id="subOut" role="menu" aria-label="Salidas">
                        <a class="ny-subpill" href="{{ route('movements.out.create') }}"><i class="bi bi-dash-lg"></i>
                            Registrar</a>
                        <a class="ny-subpill" href="{{ route('movements.out.index') }}"><i
                                class="bi bi-clock-history"></i> Historial</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== CONTENIDO ===== -->
    <main class="ny-main">
        <div class="container-xxl py-4">
            @yield('content')
        </div>
    </main>

    <!-- ===== FOOTER (compacto) ===== -->
    <footer class="ny-footer">
        <div class="container-xxl py-2">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4 text-center text-md-start" id="footer-iz">
                    <div class="d-inline-flex align-items-center gap-2">
                        <img src="{{ asset('images/pny.png') }}" class="ny-footer-logo"
                            alt="C.I. Piscícola New York">
                        <span class="fw-semibold">C.I. Piscícola New York</span>
                    </div>
                </div>

                <div class="col-12 col-md-4 text-center small text-white-50">
                    © {{ date('Y') }} C.I. Piscícola New York — Fabian Esteban Torres A
                </div>

                <div class="col-12 col-md-4 text-center text-md-end" id="footer-de">
                    <span class="text-white-50 small me-2"><i
                            class="bi bi-envelope me-1"></i>contacto@piscicolany.com</span>
                    <span class="d-inline-flex gap-2 ms-md-2">
                        <a href="#" class="ny-footer-social"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="ny-footer-social"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="ny-footer-social"><i class="bi bi-linkedin"></i></a>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // === NAV INLINE (opciones aparecen al lado del botón) ===
        (function() {
            const btn = document.getElementById('nyMenuBtn');
            const inline = document.getElementById('nyInline');
            const items = [...inline.querySelectorAll('.ny-item')];

            const closeAllSubs = () => items.forEach(i => i.classList.remove('open'));

            // Abrir/cerrar bloque de opciones (al lado del botón)
            btn.addEventListener('click', () => {
                const isOpen = inline.classList.toggle('is-open');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (!isOpen) closeAllSubs();
            });

            // Submenús inline (empujan horizontalmente)
            items.forEach(item => {
                const trigger = item.querySelector('.ny-mainbtn');
                trigger.addEventListener('click', () => {
                    if (item.classList.contains('open')) {
                        item.classList.remove('open');
                    } else {
                        items.forEach(i => i.classList.remove('open'));
                        item.classList.add('open');
                    }
                });

                // Accesible: Enter/Espacio
                trigger.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        trigger.click();
                    }
                });
            });

            // Cerrar todo si se hace click fuera
            document.addEventListener('click', (e) => {
                const nav = document.querySelector('.ny-inline-nav');
                if (!nav.contains(e.target)) {
                    inline.classList.remove('is-open');
                    btn.setAttribute('aria-expanded', 'false');
                    closeAllSubs();
                }
            });
        })();
    </script>
</body>

</html>

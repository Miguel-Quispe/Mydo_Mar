<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mydo-mar — Diseña, Cotiza y Construye Muebles a Medida</title>
    <meta name="description" content="Plataforma de diseño y carpintería a medida. Conecta con maestros carpinteros expertos, explora el catálogo y usa el diseñador técnico 2D para crear muebles con precisión milimétrica.">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS via Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* ====== VARIABLES GLOBALES ====== */
        :root {
            --color-bg:       #F9FAFB;
            --color-surface:  #FFFFFF;
            --color-dark:     #111827;
            --color-mid:      #374151;
            --color-muted:    #6B7280;
            --color-border:   #E5E7EB;
            --color-accent:   #D97706;   /* amarillo mostaza */
            --color-madera:   #92400E;   /* marrón nogal */
            --color-madera-light: #FEF3C7;
            --font-main:      'Inter', sans-serif;
            --radius-card:    12px;
            --shadow-card:    0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
            --shadow-hover:   0 4px 24px rgba(0,0,0,.12);
        }

        * { box-sizing: border-box; }
        body {
            font-family: var(--font-main);
            background-color: var(--color-bg);
            color: var(--color-dark);
            margin: 0;
        }

        /* ====== NAVBAR ====== */
        .nav-landing {
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0 0;
        }
        .nav-landing .container {
            display: flex;
            align-items: center;
            height: 64px;
            gap: 2rem;
        }
        .nav-logo {
            font-size: 1.35rem;
            font-weight: 900;
            color: var(--color-madera);
            text-decoration: none;
            letter-spacing: -0.5px;
            flex-shrink: 0;
        }
        .nav-logo span { color: var(--color-dark); }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
        }
        .nav-links a {
            font-size: .9rem;
            font-weight: 500;
            color: var(--color-mid);
            text-decoration: none;
            transition: color .2s;
        }
        .nav-links a:hover { color: var(--color-dark); }
        .nav-actions { display: flex; gap: .75rem; flex-shrink: 0; }
        .btn-ghost {
            font-size: .875rem;
            font-weight: 600;
            color: var(--color-mid);
            background: none;
            border: 1.5px solid var(--color-border);
            border-radius: 8px;
            padding: .45rem 1.1rem;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-ghost:hover { border-color: var(--color-dark); color: var(--color-dark); }
        .btn-cta {
            font-size: .875rem;
            font-weight: 700;
            color: var(--color-dark);
            background: var(--color-accent);
            border: none;
            border-radius: 8px;
            padding: .45rem 1.25rem;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-cta:hover { background: #b45309; color: #fff; }
        /* Mobile nav toggle */
        .nav-toggle { display: none; background: none; border: none; cursor: pointer; padding: .5rem; }
        .nav-toggle span { display: block; width: 22px; height: 2px; background: var(--color-dark); margin: 5px 0; border-radius: 2px; }

        /* ====== HERO ====== */
        .hero {
            padding: 80px 0 64px;
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            overflow: hidden;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: var(--color-madera-light);
            color: var(--color-madera);
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .35rem .9rem;
            border-radius: 100px;
            margin-bottom: 1.25rem;
        }
        .hero-badge::before {
            content: '';
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--color-madera);
        }
        .hero h1 {
            font-size: clamp(2rem, 4vw, 3.25rem);
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -1.5px;
            color: var(--color-dark);
            margin: 0 0 1.25rem;
        }
        .hero h1 em {
            font-style: normal;
            color: var(--color-accent);
        }
        .hero-sub {
            font-size: 1.05rem;
            color: var(--color-muted);
            line-height: 1.65;
            margin-bottom: 2rem;
            max-width: 440px;
        }
        .hero-bullets {
            list-style: none;
            padding: 0;
            margin: 0 0 2.25rem;
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }
        .hero-bullets li {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .9rem;
            font-weight: 500;
            color: var(--color-mid);
        }
        .hero-bullets li::before {
            content: '';
            width: 18px; height: 18px;
            border-radius: 50%;
            background: var(--color-accent);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23111827' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E");
            background-size: 11px;
            background-position: center;
            background-repeat: no-repeat;
            flex-shrink: 0;
        }
        .hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-hero-primary {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--color-dark);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 10px;
            padding: .8rem 1.75rem;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-hero-primary:hover { background: #1f2937; color: #fff; transform: translateY(-1px); }
        .btn-hero-secondary {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--color-accent);
            color: var(--color-dark);
            font-size: 1rem;
            font-weight: 700;
            border-radius: 10px;
            padding: .8rem 1.75rem;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-hero-secondary:hover { background: #b45309; color: #fff; transform: translateY(-1px); }

        /* Hero: mockup SVG del diseñador */
        .hero-mockup {
            background: #1c1917;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 24px 64px rgba(0,0,0,.25);
            position: relative;
        }
        .mockup-topbar {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1rem;
        }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-dot.red   { background: #ef4444; }
        .mockup-dot.yellow { background: #f59e0b; }
        .mockup-dot.green { background: #22c55e; }
        .mockup-label { color: #6b7280; font-size: .75rem; margin-left: auto; font-family: monospace; }

        /* Mini lienzo SVG */
        .mini-lienzo {
            width: 100%;
            aspect-ratio: 5/6;
            background: #f8f9fa;
            border: 10px solid var(--color-madera);
            border-radius: 4px;
            display: flex;
            overflow: hidden;
        }
        .mz { flex: 1; display: flex; flex-direction: column; }
        .mz-row { display: flex; flex: 1; }
        .mz-cell { flex: 1; background: #f0fdf4; border: 1px solid #d1fae5; }
        .mz-cell.selected { background: #cfe2ff; border-color: #0d6efd; }
        .mz-estante { height: 10px; background: var(--color-madera); width: 100%; flex-shrink: 0; }
        .mz-parante { width: 10px; background: var(--color-madera); height: 100%; flex-shrink: 0; }
        .mockup-label-bottom {
            margin-top: .75rem;
            text-align: center;
            color: #6b7280;
            font-size: .72rem;
            font-family: monospace;
        }

        /* ====== SECCIÓN: COMO FUNCIONA ====== */
        .section-how {
            padding: 80px 0;
            background: var(--color-bg);
        }
        .section-label {
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--color-accent);
            margin-bottom: .5rem;
        }
        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.25rem);
            font-weight: 800;
            letter-spacing: -.5px;
            color: var(--color-dark);
            margin-bottom: .75rem;
        }
        .section-sub {
            font-size: 1rem;
            color: var(--color-muted);
            max-width: 520px;
            margin: 0 auto 3rem;
        }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .step-card {
            background: var(--color-surface);
            border: 1.5px solid var(--color-border);
            border-radius: var(--radius-card);
            padding: 2rem 1.5rem;
            position: relative;
            transition: all .25s;
        }
        .step-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
            border-color: var(--color-accent);
        }
        .step-num {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: var(--color-dark);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
        }
        .step-card h5 { font-size: 1rem; font-weight: 700; margin-bottom: .5rem; }
        .step-card p { font-size: .875rem; color: var(--color-muted); line-height: 1.6; margin: 0; }
        .step-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
        }

        /* ====== SECCIÓN: CATÁLOGO DESTACADO ====== */
        .section-catalog {
            padding: 80px 0;
            background: var(--color-surface);
            border-top: 1px solid var(--color-border);
        }
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .mueble-card {
            background: var(--color-bg);
            border: 1.5px solid var(--color-border);
            border-radius: var(--radius-card);
            overflow: hidden;
            transition: all .25s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }
        .mueble-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
            border-color: var(--color-madera);
        }
        .mueble-img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            background: linear-gradient(135deg, #fef3c7, #fed7aa);
            display: flex; align-items: center; justify-content: center;
        }
        .mueble-img img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .mueble-img-placeholder {
            width: 100%;
            aspect-ratio: 4/3;
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 50%, #fde68a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--color-madera);
        }
        .mueble-img-placeholder svg { opacity: .4; }
        .mueble-img-placeholder span { font-size: .75rem; color: var(--color-madera); opacity: .6; margin-top: .5rem; }
        .mueble-info { padding: 1rem 1.25rem 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .mueble-material {
            font-size: .72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .07em;
            color: var(--color-madera);
            margin-bottom: .35rem;
        }
        .mueble-nombre { font-size: .95rem; font-weight: 700; margin: 0 0 .35rem; }
        .mueble-precio {
            font-size: 1.05rem; font-weight: 800;
            color: var(--color-dark); margin-top: auto; padding-top: .5rem;
        }
        .mueble-precio small { font-size: .72rem; font-weight: 400; color: var(--color-muted); }
        .catalog-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-muted);
        }
        .catalog-cta { margin-top: 2.5rem; text-align: center; }
        .btn-outline-dark-custom {
            display: inline-flex; align-items: center; gap: .5rem;
            border: 2px solid var(--color-dark);
            color: var(--color-dark);
            font-size: .95rem;
            font-weight: 700;
            border-radius: 10px;
            padding: .75rem 1.75rem;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-outline-dark-custom:hover { background: var(--color-dark); color: #fff; }

        /* ====== SECCIÓN: PARA EL MAESTRO ====== */
        .section-maestro {
            padding: 80px 0;
            background: var(--color-dark);
        }
        .maestro-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .maestro-tag {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(217,119,6,.2);
            color: #fbbf24;
            font-size: .78rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .07em;
            padding: .35rem .9rem;
            border-radius: 100px;
            margin-bottom: 1.25rem;
        }
        .maestro-inner h2 {
            font-size: clamp(1.6rem, 3vw, 2.25rem);
            font-weight: 800;
            letter-spacing: -.5px;
            color: #fff;
            margin-bottom: 1rem;
        }
        .maestro-inner p { color: #9ca3af; font-size: 1rem; line-height: 1.7; margin-bottom: 1.5rem; }
        .maestro-features { list-style: none; padding: 0; margin: 0 0 2rem; }
        .maestro-features li {
            display: flex; align-items: flex-start; gap: .75rem;
            font-size: .9rem; color: #d1d5db; margin-bottom: .75rem;
            line-height: 1.5;
        }
        .maestro-features li::before {
            content: '✓';
            width: 20px; height: 20px;
            border-radius: 50%;
            background: #d97706;
            color: #fff;
            font-size: .7rem;
            font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .btn-maestro {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--color-accent);
            color: var(--color-dark);
            font-size: 1rem; font-weight: 700;
            border-radius: 10px;
            padding: .85rem 2rem;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-maestro:hover { background: #b45309; color: #fff; transform: translateY(-1px); }
        /* Mini preview del diseñador en fondo oscuro */
        .maestro-preview {
            background: #1c1917;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #374151;
        }
        .preview-header {
            display: flex; gap: .4rem; margin-bottom: 1rem;
        }
        .preview-dot { width: 9px; height: 9px; border-radius: 50%; }

        /* ====== FOOTER ====== */
        .footer {
            background: #0a0a0a;
            border-top: 1px solid #1f2937;
            padding: 56px 0 24px;
            color: #9ca3af;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        .footer-brand { }
        .footer-logo { font-size: 1.2rem; font-weight: 900; color: var(--color-madera); margin-bottom: .75rem; }
        .footer-brand p { font-size: .85rem; line-height: 1.7; color: #6b7280; }
        .footer h6 { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #d1d5db; margin-bottom: 1rem; }
        .footer ul { list-style: none; padding: 0; margin: 0; }
        .footer ul li { margin-bottom: .6rem; }
        .footer ul a { font-size: .85rem; color: #6b7280; text-decoration: none; transition: color .2s; }
        .footer ul a:hover { color: #d1d5db; }
        .footer-bottom { border-top: 1px solid #1f2937; padding-top: 1.5rem; text-align: center; font-size: .8rem; color: #4b5563; }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 992px) {
            .hero-grid, .maestro-inner { grid-template-columns: 1fr; gap: 2.5rem; }
            .catalog-grid, .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; }
        }
        @media (max-width: 640px) {
            .catalog-grid, .steps-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
            .hero { padding: 48px 0 40px; }
            .hero-actions { flex-direction: column; }
            .btn-hero-primary, .btn-hero-secondary { justify-content: center; }
        }
    </style>
</head>
<body>

<!-- ======== NAVBAR ======== -->
<nav class="nav-landing">
    <div class="container">
        <a href="{{ url('/') }}" class="nav-logo">mydo<span>-</span>mar</a>

        <ul class="nav-links">
            <li><a href="{{ url('/') }}">Inicio</a></li>
            <li><a href="{{ route('login') }}">Catálogo</a></li>
            <li><a href="#como-funciona">Cómo Funciona</a></li>
            <li><a href="#para-maestros">Para Maestros</a></li>
        </ul>

        <div class="nav-actions">
            @if(Route::has('login'))
                <a href="{{ route('login') }}" class="btn-ghost">Iniciar Sesión</a>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-cta">Diseñar Mueble</a>
                @endif
            @endif
        </div>
    </div>
</nav>

<!-- ======== HERO ======== -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <!-- Columna de texto -->
            <div>
                <div class="hero-badge">Carpintería Digital de Precisión</div>
                <h1>Diseña, Cotiza y <em>Construye</em> tus Muebles a Medida</h1>
                <p class="hero-sub">
                    La plataforma que conecta a maestros carpinteros con clientes que buscan calidad. 
                    Diseña con precisión milimétrica y accede a guías de armado interactivas.
                </p>
                <ul class="hero-bullets">
                    <li>Despiece Automático con cálculo de piezas en tiempo real</li>
                    <li>Guías de Armado paso a paso al estilo IKEA interactivo</li>
                    <li>Conexión directa con el maestro carpintero por WhatsApp</li>
                </ul>
                <div class="hero-actions">
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        Explorar Catálogo
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-hero-secondary">
                        Registrarme Gratis
                    </a>
                    @endif
                </div>
            </div>

            <!-- Columna: Mockup del diseñador 2D -->
            <div>
                <div class="hero-mockup">
                    <div class="mockup-topbar">
                        <div class="mockup-dot red"></div>
                        <div class="mockup-dot yellow"></div>
                        <div class="mockup-dot green"></div>
                        <span class="mockup-label">Diseñador 2D — Mydo-mar</span>
                    </div>
                    <!-- Mini lienzo CSS que imita el diseñador Polyboard -->
                    <div class="mini-lienzo">
                        <div class="mz">
                            <div class="mz-row">
                                <div class="mz-cell selected"></div>
                                <div class="mz-parante"></div>
                                <div class="mz-cell" style="flex:1.5"></div>
                            </div>
                            <div class="mz-estante"></div>
                            <div class="mz-row" style="flex:1.8">
                                <div class="mz-cell"></div>
                                <div class="mz-parante"></div>
                                <div class="mz-cell" style="flex:1.5; display:flex; flex-direction:column;">
                                    <div class="mz-cell" style="flex:1"></div>
                                    <div class="mz-estante"></div>
                                    <div class="mz-cell" style="flex:1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mockup-label-bottom">Haz clic en una zona para dividirla</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======== COMO FUNCIONA ======== -->
<section class="section-how" id="como-funciona">
    <div class="container">
        <div class="text-center">
            <div class="section-label">Proceso</div>
            <h2 class="section-title">Así funciona Mydo-mar</h2>
            <p class="section-sub">
                Desde la idea hasta el mueble armado en tu hogar, en tres pasos simples.
            </p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">1</div>
                <h5>Elige tu Diseño</h5>
                <p>Explora el catálogo de muebles creados por maestros carpinteros con especificaciones técnicas reales: materiales, dimensiones y precio base.</p>
            </div>
            <div class="step-card">
                <div class="step-num" style="background: var(--color-accent); color: var(--color-dark)">2</div>
                <h5>Recibe tu Lista de Materiales</h5>
                <p>El sistema genera automáticamente la lista de despiece: cantidad de piezas, largos y anchos exactos listos para llevar a la maderería.</p>
            </div>
            <div class="step-card">
                <div class="step-num" style="background: #065f46; color: #fff">3</div>
                <h5>Arma con la Guía Interactiva</h5>
                <p>Una guía paso a paso al estilo IKEA te muestra cómo ensamblar el mueble, qué herrajes necesitas en cada etapa y las herramientas requeridas.</p>
            </div>
        </div>
    </div>
</section>

<!-- ======== CATÁLOGO DESTACADO ======== -->
<section class="section-catalog" id="catalogo">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">Catálogo</div>
            <h2 class="section-title">Muebles Destacados</h2>
            <p class="section-sub">Diseños creados por nuestros maestros carpinteros con especificaciones listas para fabricar.</p>
        </div>

        <div class="catalog-grid">
            @forelse($mueblesDestacados as $mueble)
                <a href="{{ route('login') }}" class="mueble-card">
                    @if($mueble->imagen_url)
                        <img src="{{ asset('storage/' . $mueble->imagen_url) }}" alt="{{ $mueble->nombre }}" class="mueble-img" style="width:100%;aspect-ratio:4/3;object-fit:cover;">
                    @else
                        <div class="mueble-img-placeholder">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M3 9h18M9 3v18"/>
                            </svg>
                            <span>Sin imagen</span>
                        </div>
                    @endif
                    <div class="mueble-info">
                        @if($mueble->material)
                            <div class="mueble-material">{{ $mueble->material }}</div>
                        @endif
                        <div class="mueble-nombre">{{ $mueble->nombre }}</div>
                        @if($mueble->dimensiones)
                            <div style="font-size:.78rem; color: var(--color-muted); margin-bottom:.25rem;">{{ $mueble->dimensiones }}</div>
                        @endif
                        <div class="mueble-precio">
                            @if($mueble->precio)
                                Bs. {{ number_format($mueble->precio, 2) }}
                                <small>/ desde</small>
                            @else
                                <small>Consultar precio</small>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="catalog-empty">
                    <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:1rem;display:block;margin-left:auto;margin-right:auto">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/>
                    </svg>
                    <p style="margin:0; font-weight:600; color:#6b7280">El catálogo estará disponible pronto.</p>
                    <p style="margin:.5rem 0 0; font-size:.875rem; color:#9ca3af">Los maestros están preparando sus primeros diseños.</p>
                </div>
            @endforelse
        </div>

        <div class="catalog-cta">
            <a href="{{ route('login') }}" class="btn-outline-dark-custom">
                Ver todo el catálogo
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- ======== SECCIÓN MAESTRO ======== -->
<section class="section-maestro" id="para-maestros">
    <div class="container">
        <div class="maestro-inner">
            <!-- Texto -->
            <div>
                <div class="maestro-tag">Para Maestros Carpinteros</div>
                <h2>Tu taller, ahora con herramientas digitales de precisión</h2>
                <p>
                    Usa el Diseñador Técnico 2D para crear el despiece exacto de cada mueble, 
                    publica tu catálogo y conecta directamente con clientes que valoran la calidad artesanal.
                </p>
                <ul class="maestro-features">
                    <li>Diseñador 2D con cálculo automático de zonas y tableros de Melamina / MDF / Madera</li>
                    <li>Lista de despiece generada al instante: piezas, largos, anchos y espesor</li>
                    <li>Publicación directa al catálogo de la plataforma con precio referencial</li>
                    <li>Los clientes te contactan directamente por WhatsApp para cotizar</li>
                </ul>
                @if(Route::has('register'))
                <a href="{{ route('register') }}" class="btn-maestro">
                    Registrarme como Maestro
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>

            <!-- Preview del diseñador (decorativo) -->
            <div class="maestro-preview">
                <div class="preview-header">
                    <div class="preview-dot" style="background:#ef4444"></div>
                    <div class="preview-dot" style="background:#f59e0b"></div>
                    <div class="preview-dot" style="background:#22c55e"></div>
                </div>
                <!-- Representación del panel de control + lienzo -->
                <div style="display:grid; grid-template-columns:1fr 1.8fr; gap:.75rem;">
                    <!-- Panel lateral -->
                    <div style="background:#292524; border-radius:8px; padding:.75rem; font-size:.65rem; color:#a8a29e;">
                        <div style="color:#fbbf24; font-weight:700; font-size:.7rem; margin-bottom:.75rem;">Control de Zonas</div>
                        <div style="background:#1c1917; border-radius:4px; padding:.4rem .6rem; margin-bottom:.4rem; color:#6b7280;">Alto: 1800mm</div>
                        <div style="background:#1c1917; border-radius:4px; padding:.4rem .6rem; margin-bottom:.75rem; color:#6b7280;">Ancho: 900mm</div>
                        <div style="background:#1d4ed8; border-radius:4px; padding:.4rem .6rem; text-align:center; color:#fff; font-weight:600; margin-bottom:.4rem;">+ Estante</div>
                        <div style="background:#166534; border-radius:4px; padding:.4rem .6rem; text-align:center; color:#fff; font-weight:600;">+ Parante</div>
                    </div>
                    <!-- Mini lienzo oscuro -->
                    <div style="background:#1c1917; border-radius:8px; padding:.5rem; display:flex; align-items:center; justify-content:center;">
                        <div style="width:100%; aspect-ratio:4/5; border:8px solid #92400e; border-radius:2px; display:flex; overflow:hidden;">
                            <div style="flex:1; display:flex; flex-direction:column;">
                                <div style="display:flex; flex:1;">
                                    <div style="flex:1; background:#1a2e1a; border:1px solid #14532d;"></div>
                                    <div style="width:8px; background:#92400e;"></div>
                                    <div style="flex:1.5; background:#2e1a1a; border:1px solid #7f1d1d;"></div>
                                </div>
                                <div style="height:8px; background:#92400e; width:100%;"></div>
                                <div style="display:flex; flex:1.5;">
                                    <div style="flex:1; background:#1a2e1a; border:1px solid #14532d;"></div>
                                    <div style="width:8px; background:#92400e;"></div>
                                    <div style="flex:1.5; background:#1a1a2e; border:1px solid #1e1b4b; display:flex; flex-direction:column;">
                                        <div style="flex:1; border-bottom:4px solid #92400e;"></div>
                                        <div style="flex:1;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:.75rem; background:#292524; border-radius:6px; padding:.6rem .75rem;">
                    <div style="font-size:.65rem; color:#6b7280; margin-bottom:.35rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Despiece Calculado</div>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:.25rem; font-size:.6rem; color:#a8a29e; text-align:center;">
                        <span>Pieza</span><span>Cant.</span><span>Largo</span><span>Ancho</span>
                        <span style="color:#d1d5db">Lateral</span><span>2</span><span>1800</span><span>450</span>
                        <span style="color:#d1d5db">Estante</span><span>1</span><span>864</span><span>430</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======== FOOTER ======== -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo">mydo-mar</div>
                <p>Transformando ideas en muebles únicos. Conectamos a los mejores maestros carpinteros con clientes que buscan calidad, durabilidad y diseño a medida.</p>
            </div>
            <div>
                <h6>Navegación</h6>
                <ul>
                    <li><a href="{{ url('/') }}">Inicio</a></li>
                    <li><a href="{{ route('login') }}">Catálogo</a></li>
                    @if(Route::has('register'))
                        <li><a href="{{ route('register') }}">Registrarse</a></li>
                    @endif
                    <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                </ul>
            </div>
            <div>
                <h6>Plataforma</h6>
                <ul>
                    <li><a href="#como-funciona">Cómo Funciona</a></li>
                    <li><a href="#para-maestros">Para Maestros</a></li>
                    <li><a href="#">Preguntas Frecuentes</a></li>
                    <li><a href="#">Política de Privacidad</a></li>
                </ul>
            </div>
            <div>
                <h6>Contacto</h6>
                <ul>
                    <li><a href="mailto:soporte@mydomar.com">soporte@mydomar.com</a></li>
                    <li><a href="#">+591 123 45678</a></li>
                    <li><a href="#">Lun–Vie, 08:00–18:00</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} Mydo-mar. Todos los derechos reservados.
        </div>
    </div>
</footer>

</body>
</html>
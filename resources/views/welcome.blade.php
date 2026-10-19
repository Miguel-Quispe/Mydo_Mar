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
            padding: 96px 0;
            background: radial-gradient(circle at 80% 20%, rgba(217,119,6,0.14) 0%, rgba(15,23,42,1) 65%), #0f172a;
            position: relative;
            overflow: hidden;
        }
        .section-maestro::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            opacity: 0.7;
        }
        .maestro-inner {
            display: grid;
            grid-template-columns: 1fr 1.25fr;
            gap: 3.5rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .maestro-tag {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(217,119,6,.18);
            border: 1px solid rgba(251,191,36,.3);
            color: #fbbf24;
            font-size: .8rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em;
            padding: .4rem 1rem;
            border-radius: 100px;
            margin-bottom: 1.25rem;
        }
        .maestro-inner h2 {
            font-size: clamp(1.8rem, 3.2vw, 2.5rem);
            font-weight: 800;
            letter-spacing: -.5px;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        .maestro-inner h2 span {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .maestro-inner p { color: #94a3b8; font-size: 1.05rem; line-height: 1.7; margin-bottom: 1.75rem; }
        .maestro-features { list-style: none; padding: 0; margin: 0 0 2.25rem; }
        .maestro-features li {
            display: flex; align-items: flex-start; gap: .85rem;
            font-size: .95rem; color: #e2e8f0; margin-bottom: 1rem;
            line-height: 1.5;
        }
        .maestro-features li .feat-icon {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d97706, #b45309);
            color: #fff;
            font-size: .75rem;
            font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(217,119,6,0.4);
            margin-top: 1px;
        }
        .btn-maestro {
            display: inline-flex; align-items: center; gap: .6rem;
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            color: #0f172a;
            font-size: 1.05rem; font-weight: 800;
            border-radius: 12px;
            padding: .95rem 2.2rem;
            text-decoration: none;
            box-shadow: 0 4px 18px rgba(217,119,6,0.35);
            transition: all .25s ease;
        }
        .btn-maestro:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(217,119,6,0.5);
            color: #000;
        }

        /* Mockup ventana Diseñador 3D Real */
        .maestro-preview-3d {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,0.18);
            box-shadow: 0 25px 60px -15px rgba(0,0,0,0.85), 0 0 0 1px rgba(255,255,255,0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: #1e293b;
        }
        .preview-topbar-3d {
            background: #111827;
            padding: .75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #1f2937;
        }
        .preview-topbar-left {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .preview-dots-3d { display: flex; gap: .4rem; }
        .preview-dot-3d { width: 10px; height: 10px; border-radius: 50%; }
        .preview-title-3d {
            color: #f8fafc;
            font-size: .8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .preview-title-3d .badge-proj {
            background: #374151;
            color: #d1d5db;
            font-size: .65rem;
            padding: .15rem .5rem;
            border-radius: 4px;
        }
        .preview-topbar-actions {
            display: flex;
            gap: .45rem;
            align-items: center;
        }
        .btn-preview-publish {
            background: #1f2937;
            color: #f3f4f6;
            border: 1px solid #374151;
            border-radius: 6px;
            font-size: .68rem;
            font-weight: 700;
            padding: .35rem .75rem;
        }
        .btn-preview-cut {
            background: #f59e0b;
            color: #111827;
            border: none;
            border-radius: 6px;
            font-size: .68rem;
            font-weight: 800;
            padding: .35rem .75rem;
        }

        /* Área de trabajo 3D */
        .preview-workbench-3d {
            display: grid;
            grid-template-columns: 165px 1fr;
            background: #f8fafc;
            height: 380px;
        }

        /* Sidebar izquierda */
        .preview-sidebar-3d {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .preview-tabs-top {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #e2e8f0;
            background: #f1f5f9;
        }
        .preview-tab-btn {
            font-size: .68rem;
            font-weight: 700;
            padding: .5rem;
            text-align: center;
            color: #64748b;
            border-bottom: 2px solid transparent;
        }
        .preview-tab-btn.active {
            color: #d97706;
            background: #ffffff;
            border-bottom-color: #d97706;
        }
        .preview-elements-list {
            padding: .6rem;
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }
        .preview-elem-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: .45rem .6rem;
            font-size: .7rem;
            font-weight: 600;
            color: #334155;
            display: flex;
            align-items: center;
            gap: .5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .preview-elem-item svg { color: #d97706; }
        .preview-bottom-params {
            margin-top: auto;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: .6rem;
        }
        .preview-btn-group-lock {
            background: #475569;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: .35rem .5rem;
            font-size: .65rem;
            font-weight: 700;
            width: 100%;
            text-align: center;
            margin-bottom: .45rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .3rem;
        }
        .preview-dim-inputs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .25rem;
            text-align: center;
        }
        .preview-dim-cell {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: .2rem .1rem;
            font-size: .6rem;
        }
        .preview-dim-cell span { display: block; font-size: .55rem; color: #64748b; }
        .preview-dim-cell strong { color: #0f172a; font-size: .65rem; }

        /* Lienzo 3D Three.js */
        .preview-viewport-3d {
            position: relative;
            background: radial-gradient(circle at 50% 40%, #f1f5f9 0%, #cbd5e1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        /* Cuadrícula isométrica 3D */
        .viewport-3d-grid {
            position: absolute;
            width: 320px;
            height: 220px;
            bottom: 30px;
            background-image: 
                linear-gradient(rgba(148, 163, 184, 0.35) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.35) 1px, transparent 1px);
            background-size: 20px 20px;
            transform: perspective(600px) rotateX(60deg) rotateZ(-25deg);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border-radius: 4px;
        }
        /* Ejes RGB Three.js */
        .axes-indicator {
            position: absolute;
            bottom: 95px;
            left: calc(50% - 10px);
            width: 30px;
            height: 30px;
        }
        .axis-y { position: absolute; left: 14px; bottom: 14px; width: 2px; height: 35px; background: #22c55e; }
        .axis-x { position: absolute; left: 14px; bottom: 14px; width: 35px; height: 2px; background: #ef4444; transform: rotate(-25deg); transform-origin: left; }
        .axis-z { position: absolute; left: 14px; bottom: 14px; width: 35px; height: 2px; background: #3b82f6; transform: rotate(145deg); transform-origin: left; }

        /* Mueble 3D renderizado */
        .rendered-furniture-3d {
            position: relative;
            z-index: 2;
            width: 155px;
            height: 190px;
            transform: perspective(700px) rotateX(15deg) rotateY(-25deg);
            transform-style: preserve-3d;
            filter: drop-shadow(15px 25px 20px rgba(0,0,0,0.3));
        }
        .mueble-3d-box {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #b45309 0%, #78350f 100%);
            border: 5px solid #92400e;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.4);
            position: relative;
        }
        .mueble-3d-inner {
            display: flex;
            flex: 1;
        }
        .m-col-izq {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-right: 4px solid #92400e;
        }
        .m-col-der {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .m-repisa {
            border-bottom: 4px solid #92400e;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .52rem;
            color: #fed7aa;
            font-weight: 700;
        }
        .m-repisa.highlight {
            background: rgba(254, 243, 199, 0.25);
            outline: 1.5px dashed #38bdf8;
        }
        /* Herrajes visuales (bisagras y jalador) */
        .m-jalador {
            position: absolute;
            right: 8px;
            top: 50%;
            width: 3px;
            height: 25px;
            background: #e2e8f0;
            border-radius: 2px;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }

        /* Gizmo / Controles flotantes a la derecha */
        .preview-gizmo-bar {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: .3rem;
            display: flex;
            flex-direction: column;
            gap: .3rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            z-index: 5;
        }
        .gizmo-tool-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .62rem;
            font-weight: 800;
            color: #475569;
        }
        .gizmo-tool-btn.active {
            background: #d97706;
            color: #ffffff;
            border-color: #d97706;
        }

        /* Barra inferior: Resumen Técnico de Corte */
        .preview-footer-3d {
            background: #0f172a;
            color: #94a3b8;
            padding: .5rem .9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: .65rem;
            border-top: 1px solid #1e293b;
        }
        .preview-footer-3d span.accent {
            color: #fbbf24;
            font-weight: 700;
        }
        .preview-footer-3d .badge-ready {
            background: rgba(34,197,94,0.15);
            color: #4ade80;
            font-weight: 700;
            padding: .15rem .5rem;
            border-radius: 4px;
            border: 1px solid rgba(74,222,128,0.3);
        }

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
            .preview-workbench-3d { grid-template-columns: 1fr; height: auto; }
            .preview-viewport-3d { height: 260px; }
            .preview-footer-3d { flex-direction: column; gap: .4rem; text-align: center; }
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
            <!-- Texto y Beneficios del Maestro -->
            <div>
                <div class="maestro-tag">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Herramientas de Precisión 3D
                </div>
                <h2>Tu taller ahora con <span>Diseñador 3D & Optimización</span></h2>
                <p>
                    Lleva tu carpintería al siguiente nivel. Modela muebles en Melamina de 18mm en un entorno 3D interactivo, aplica texturas reales, calcula el despiece automático y genera hojas de corte optimizadas con planos PDF listos para fabricar.
                </p>
                <ul class="maestro-features">
                    <li>
                        <span class="feat-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <div>
                            <strong style="color:#f8fafc; font-size:.95rem; margin-bottom:2px;">Diseñador 3D Interactivo (Three.js):</strong>
                            Crea tableros verticales y horizontales, agrupa ensambles completos y visualiza en perspectivas 3D, Frontal, Superior y Lateral.
                        </div>
                    </li>
                    <li>
                        <span class="feat-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <div>
                            <strong style="color:#f8fafc; font-size:.95rem; margin-bottom:2px;">Texturas con Veta Real & Herrajes:</strong>
                            Aplica acabados de Roble, Nogal, Grafito o Mármol e integra bisagras de cazoleta de 35mm y rieles telescópicos automáticamente.
                        </div>
                    </li>
                    <li>
                        <span class="feat-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <div>
                            <strong style="color:#f8fafc; font-size:.95rem; margin-bottom:2px;">Optimizador de Corte & Planos PDF:</strong>
                            Algoritmo bin-packing para láminas de melamina (2440×1220 mm) con cálculo de kerf y descarga de planos de taller.
                        </div>
                    </li>
                    <li>
                        <span class="feat-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <div>
                            <strong style="color:#f8fafc; font-size:.95rem; margin-bottom:2px;">Catálogo Digital & WhatsApp Directo:</strong>
                            Publica tus modelos con captura 3D automática para que los clientes coticen al instante por WhatsApp.
                        </div>
                    </li>
                </ul>

                @auth
                    @if(auth()->user()->rol_id == 1 || str_starts_with(auth()->user()->clave_rol ?? '', 'MAE'))
                        <a href="{{ route('maestro.dashboard') }}" class="btn-maestro">
                            Ir a mi Panel de Maestro
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <a href="{{ route('muebles.create') }}" class="btn-maestro">
                            Abrir Diseñador 3D
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                @else
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-maestro">
                            Registrarme como Maestro
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Preview fiel del Diseñador 3D del Maestro -->
            <div class="maestro-preview-3d">
                <!-- Barra superior de la App -->
                <div class="preview-topbar-3d">
                    <div class="preview-topbar-left">
                        <div class="preview-dots-3d">
                            <div class="preview-dot-3d" style="background:#ef4444"></div>
                            <div class="preview-dot-3d" style="background:#f59e0b"></div>
                            <div class="preview-dot-3d" style="background:#22c55e"></div>
                        </div>
                        <div class="preview-title-3d">
                            <span>Diseñador 3D</span>
                            <span class="badge-proj">Proyecto: Mydo Mar</span>
                        </div>
                    </div>
                    <div class="preview-topbar-actions">
                        <div class="btn-preview-cut">Configurar Corte</div>
                        <div class="btn-preview-publish">Guardar y Publicar</div>
                    </div>
                </div>

                <!-- Workbench 3D dividido (Sidebar + Viewport Three.js) -->
                <div class="preview-workbench-3d">
                    <!-- Sidebar de Herramientas -->
                    <div class="preview-sidebar-3d">
                        <div class="preview-tabs-top">
                            <div class="preview-tab-btn active">Elementos</div>
                            <div class="preview-tab-btn">Biblioteca</div>
                        </div>
                        <div class="preview-elements-list">
                            <div class="preview-elem-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                Habitación
                            </div>
                            <div class="preview-elem-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="3" width="12" height="18" rx="1"/></svg>
                                Vertical (18mm)
                            </div>
                            <div class="preview-elem-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="8" width="18" height="8" rx="1"/></svg>
                                Horizontal (18mm)
                            </div>
                        </div>

                        <!-- Parámetros Inferiores -->
                        <div class="preview-bottom-params">
                            <div class="preview-btn-group-lock">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Bloquear / Agrupar
                            </div>
                            <div class="preview-dim-inputs">
                                <div class="preview-dim-cell">
                                    <span>Grosor</span>
                                    <strong>18 mm</strong>
                                </div>
                                <div class="preview-dim-cell">
                                    <span>Alto</span>
                                    <strong>1800</strong>
                                </div>
                                <div class="preview-dim-cell">
                                    <span>Ancho</span>
                                    <strong>900</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Viewport 3D Three.js -->
                    <div class="preview-viewport-3d">
                        <!-- Cuadrícula isométrica 3D -->
                        <div class="viewport-3d-grid"></div>

                        <!-- Ejes dimensionales RGB -->
                        <div class="axes-indicator">
                            <div class="axis-y"></div>
                            <div class="axis-x"></div>
                            <div class="axis-z"></div>
                        </div>

                        <!-- Mueble renderizado en 3D -->
                        <div class="rendered-furniture-3d">
                            <div class="mueble-3d-box">
                                <div class="mueble-3d-inner">
                                    <!-- Columna izquierda -->
                                    <div class="m-col-izq">
                                        <div class="m-repisa">430x423</div>
                                        <div class="m-repisa highlight">Melamina 18mm</div>
                                        <div class="m-repisa" style="border-bottom:none">430x423</div>
                                    </div>
                                    <!-- Columna derecha -->
                                    <div class="m-col-der">
                                        <div class="m-repisa" style="flex:1.8">Colgador</div>
                                        <div class="m-repisa" style="flex:1; border-bottom:none">Cajonera</div>
                                    </div>
                                </div>
                                <div class="m-jalador"></div>
                            </div>
                        </div>

                        <!-- Gizmo de vistas a la derecha -->
                        <div class="preview-gizmo-bar">
                            <div class="gizmo-tool-btn">⤢</div>
                            <div class="gizmo-tool-btn">↻</div>
                            <div class="gizmo-tool-btn active">3D</div>
                            <div class="gizmo-tool-btn">X</div>
                            <div class="gizmo-tool-btn">Y</div>
                            <div class="gizmo-tool-btn">Z</div>
                        </div>
                    </div>
                </div>

                <!-- Footer técnico: Optimización de Corte -->
                <div class="preview-footer-3d">
                    <div>
                        <span>Optimización Bin-Packing: <span class="accent">2 Láminas (2440×1220 mm)</span> • Melamina Nogal 18mm</span>
                    </div>
                    <div>
                        <span class="badge-ready">Planos PDF Listos</span>
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
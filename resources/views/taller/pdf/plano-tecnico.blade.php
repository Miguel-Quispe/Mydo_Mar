<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Plano Técnico – {{ $diseno->nombre_diseno_muebles }}</title>
    <style>
        /* ─── Reset y base ──────────────────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #ffffff;
            line-height: 1.45;
        }

        /* ─── Página y márgenes ─────────────────────────────────────────────── */
        @page {
            margin: 18mm 15mm 22mm 15mm;
            size: letter portrait;
        }

        /* ─── Membrete ──────────────────────────────────────────────────────── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1.5px solid #1e3a5f;
            border-radius: 4px;
        }
        .header-logo-cell {
            width: 28%;
            background: #1e3a5f;
            color: #fff;
            padding: 10px 12px;
            vertical-align: middle;
        }
        .header-logo-cell .company-name {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-logo-cell .company-sub {
            font-size: 8.5px;
            color: #a8c4e0;
            margin-top: 2px;
        }
        .header-logo-cell .company-nit {
            font-size: 9px;
            color: #ffd166;
            margin-top: 4px;
            font-weight: bold;
        }
        .header-doc-cell {
            width: 44%;
            padding: 8px 12px;
            vertical-align: top;
            border-left: 1px solid #c8d8e8;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            font-size: 9px;
            color: #5a7a9a;
            margin-top: 3px;
        }
        .doc-ref {
            margin-top: 6px;
            font-size: 9px;
            color: #333;
        }
        .header-meta-cell {
            width: 28%;
            padding: 8px 12px;
            vertical-align: top;
            border-left: 1px solid #c8d8e8;
            background: #f5f8fc;
        }
        .meta-row {
            font-size: 9px;
            margin-bottom: 3px;
        }
        .meta-label {
            color: #6b7280;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }
        .meta-value {
            color: #1a1a2e;
            font-weight: bold;
        }

        /* ─── Badges de estado ──────────────────────────────────────────────── */
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
            border-radius: 3px;
            padding: 1px 5px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
            border-radius: 3px;
            padding: 1px 5px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-dark {
            background: #1e3a5f;
            color: #ffffff;
            border-radius: 3px;
            padding: 1px 5px;
            font-size: 8px;
            font-weight: bold;
        }

        /* ─── Resumen General ───────────────────────────────────────────────── */
        .info-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border: 1px solid #c8d8e8;
            border-radius: 4px;
            background: #f5f8fc;
        }
        .info-box td {
            padding: 7px 12px;
            font-size: 9.5px;
            border-right: 1px solid #dde8f0;
            vertical-align: top;
        }
        .info-box td:last-child { border-right: none; }
        .info-label {
            display: block;
            font-size: 8px;
            color: #6b7280;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 10px;
            font-weight: bold;
            color: #1e3a5f;
        }

        /* ─── Sección ───────────────────────────────────────────────────────── */
        .section-header {
            width: 100%;
            background: #1e3a5f;
            color: #fff;
            padding: 5px 10px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
            margin-top: 16px;
        }
        .section-header-first { margin-top: 8px; }
        .section-subheader {
            background: #e8f0f8;
            border-bottom: 1.5px solid #c8d8e8;
            padding: 4px 10px;
            font-size: 8.5px;
            color: #4a6a8a;
            margin-bottom: 0;
        }

        /* ─── Tablas de Datos ───────────────────────────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8d8e8;
            margin-bottom: 4px;
            font-size: 9px;
        }
        .data-table thead tr {
            background: #2d5a8e;
            color: #fff;
        }
        .data-table thead th {
            padding: 6px 9px;
            text-align: left;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-right: 1px solid #3a6ea8;
            white-space: nowrap;
        }
        .data-table thead th:last-child { border-right: none; }
        .data-table tbody tr:nth-child(even) { background: #f0f6ff; }
        .data-table tbody tr:nth-child(odd) { background: #ffffff; }
        .data-table tbody td {
            padding: 5px 9px;
            border-right: 1px solid #dde8f0;
            border-bottom: 1px solid #e8f0fa;
            font-size: 9px;
            vertical-align: middle;
        }
        .data-table tbody td:last-child { border-right: none; }
        .data-table tfoot tr { background: #e8f4fd; border-top: 2px solid #2d5a8e; }
        .data-table tfoot td {
            padding: 6px 9px;
            font-weight: bold;
            font-size: 9.5px;
            color: #1e3a5f;
            border-right: 1px solid #c8d8e8;
        }
        .data-table tfoot td:last-child { border-right: none; }

        /* ─── Utilidades ────────────────────────────────────────────────────── */
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold     { font-weight: bold; }
        .mono        { font-family: Courier, 'Courier New', monospace; font-size: 8.5px; }
        .text-blue   { color: #1e5fa8; }
        .text-green  { color: #065f46; }
        .text-orange { color: #92400e; }
        .text-gray   { color: #6b7280; }
        .m-neto-highlight { background: #fffbeb !important; }

        /* ─── KPI row ───────────────────────────────────────────────────────── */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border: 1px solid #c8d8e8;
        }
        .kpi-table td {
            width: 25%;
            padding: 8px 10px;
            text-align: center;
            border-right: 1px solid #c8d8e8;
            vertical-align: middle;
        }
        .kpi-table td:last-child { border-right: none; }
        .kpi-value {
            font-size: 17px;
            font-weight: bold;
            color: #1e3a5f;
            display: block;
        }
        .kpi-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 2px;
            display: block;
        }
        .kpi-sub {
            font-size: 7.5px;
            color: #9ca3af;
            margin-top: 1px;
            display: block;
        }

        /* ─── Pasos de ensamblaje ───────────────────────────────────────────── */
        .step-row {
            border: 1px solid #c8d8e8;
            border-left: 4px solid #1e3a5f;
            padding: 6px 10px;
            margin-bottom: 5px;
            background: #f9fbff;
        }
        .step-number {
            font-size: 8px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
        }
        .step-text {
            font-size: 9.5px;
            color: #1a1a2e;
        }

        /* ─── Footer ────────────────────────────────────────────────────────── */
        .footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            height: 18mm;
            border-top: 2px solid #1e3a5f;
            padding-top: 4px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            font-size: 8px;
            color: #6b7280;
            vertical-align: top;
            padding: 2px 0;
        }
        .footer-left { text-align: left; }
        .footer-center { text-align: center; }
        .footer-right { text-align: right; }
        .footer-company { font-weight: bold; color: #1e3a5f; }
        .footer-disclaimer {
            font-size: 7px;
            color: #9ca3af;
            margin-top: 3px;
            text-align: center;
        }

        /* ─── Separador ─────────────────────────────────────────────────────── */
        .divider { border-top: 1px dashed #c8d8e8; margin: 10px 0; }

        /* ─── Firma ─────────────────────────────────────────────────────────── */
        .firma-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .firma-table td {
            width: 33%;
            text-align: center;
            padding: 0 10px;
            vertical-align: bottom;
        }
        .firma-line {
            border-top: 1px solid #1e3a5f;
            padding-top: 4px;
            margin-top: 28px;
            font-size: 8.5px;
            color: #1a1a2e;
        }
        .firma-label {
            font-size: 8px;
            color: #6b7280;
        }

        /* ─── Sello de confidencialidad ─────────────────────────────────────── */
        .stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 40px;
            font-weight: bold;
            color: rgba(30, 58, 95, 0.06);
            white-space: nowrap;
            letter-spacing: 4px;
            text-transform: uppercase;
            pointer-events: none;
        }

        /* ─── Tabla de Dimensiones 3D ───────────────────────────────────────── */
        .coord-cell { font-size: 8px; color: #5a7a9a; font-family: Courier, monospace; }
    </style>
</head>
<body>

    <!-- ══════════════════════════════════════════════════════════
         PIE DE PÁGINA FIJO (se repite en todas las hojas)
    ══════════════════════════════════════════════════════════════ -->
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left" style="width: 38%;">
                    <span class="footer-company">MADEART S.R.L.</span><br>
                    Sistema PolloMuebles v2.0 — HTAP B2B<br>
                    NIT: <strong>1023849021</strong>
                </td>
                <td class="footer-center" style="width: 24%;">
                    Plano Técnico Oficial de Taller<br>
                    <strong>Documento de uso interno exclusivo</strong>
                </td>
                <td class="footer-right" style="width: 38%;">
                    Orden: <strong>ORD-{{ str_pad($diseno->id_diseno_muebles, 4, '0', STR_PAD_LEFT) }}</strong><br>
                    Smart Key: <strong class="mono">{{ $diseno->id_dim_tiempo_publicacion }}</strong><br>
                    Generado: {{ $fechaGeneracion }}
                </td>
            </tr>
        </table>
        <div class="footer-disclaimer">
            © {{ date('Y') }} Madeart S.R.L. — Este documento es de circulación restringida. Prohibida su reproducción sin autorización expresa de Gerencia.
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         MEMBRETE OFICIAL
    ══════════════════════════════════════════════════════════════ -->
    <table class="header-table">
        <tr>
            <!-- Identidad Corporativa -->
            <td class="header-logo-cell">
                <div class="company-name">Madeart</div>
                <div class="company-sub">Fábrica de Muebles a Medida · Sistema B2B</div>
                <div class="company-nit">NIT: 1023849021</div>
            </td>

            <!-- Título del Documento -->
            <td class="header-doc-cell">
                <div class="doc-title">Plano Técnico de Taller</div>
                <div class="doc-subtitle">Despiece milimétrico y lista de materiales BOM para producción</div>
                <div class="doc-ref">
                    <strong>Proyecto:</strong> {{ $diseno->nombre_diseno_muebles }}<br>
                    <strong>Categoría:</strong> {{ $diseno->categoria->nombre_categoria_muebles ?? 'General' }} &nbsp;·&nbsp;
                    <strong>Nivel:</strong> {{ $diseno->nivel->nombre_nivel_muebles ?? 'Intermedio' }}
                </div>
            </td>

            <!-- Metadatos del Documento -->
            <td class="header-meta-cell">
                <div class="meta-row">
                    <span class="meta-label">N° Orden</span>
                    <span class="meta-value">ORD-{{ str_pad($diseno->id_diseno_muebles, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Smart Key HTAP</span>
                    <span class="meta-value mono">{{ $diseno->id_dim_tiempo_publicacion }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Estado</span>
                    @if($diseno->publicado_diseno_muebles)
                        <span class="badge-success">✓ Publicado</span>
                    @else
                        <span class="badge-warning">Borrador</span>
                    @endif
                </div>
                <div class="meta-row">
                    <span class="meta-label">Fecha de emisión</span>
                    <span class="meta-value">{{ $fechaGeneracion }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Empresa</span>
                    <span class="meta-value">{{ $diseno->empresa->nombre_empresa_users ?? 'Madeart S.R.L.' }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- ══════════════════════════════════════════════════════════
         KPI's DE RESUMEN (Superficie, Planchas, Costo, Piezas)
    ══════════════════════════════════════════════════════════════ -->
    @php
        $m2Total = $diseno->piezas->sum(fn($p) => ($p->ancho_mm_muebles * $p->alto_mm_muebles) / 1000000);
        $m2Desp  = $m2Total * 1.15;
        $planchas = max(1, ceil($m2Desp / 5.0325));
        $costoMelamina = $planchas * 280;   // Precio de referencia de la semilla
        $costoHerrajes = $diseno->herrajesProyecto->sum(fn($h) => $h->subtotalCosto);
        $costoTotal = $costoMelamina + $costoHerrajes;
    @endphp

    <table class="kpi-table">
        <tr>
            <td style="border-left: 4px solid #1e5fa8;">
                <span class="kpi-value text-blue">{{ number_format($m2Total, 2) }}</span>
                <span class="kpi-label">m² Superficie Neta</span>
                <span class="kpi-sub">+15% kerf → {{ number_format($m2Desp, 2) }} m²</span>
            </td>
            <td style="border-left: 4px solid #d97706;">
                <span class="kpi-value text-orange">{{ $planchas }}</span>
                <span class="kpi-label">Planchas 2750×1830 mm</span>
                <span class="kpi-sub">Melamina 18mm estándar</span>
            </td>
            <td style="border-left: 4px solid #065f46;">
                <span class="kpi-value text-green">Bs {{ number_format($costoTotal, 2) }}</span>
                <span class="kpi-label">Costo Total Estimado</span>
                <span class="kpi-sub">Melamina + Herrajes</span>
            </td>
            <td style="border-left: 4px solid #6b7280;">
                <span class="kpi-value" style="color: #374151;">{{ $diseno->piezas->count() }}</span>
                <span class="kpi-label">Piezas en Despiece</span>
                <span class="kpi-sub">{{ $diseno->herrajesProyecto->count() }} tipo(s) herraje</span>
            </td>
        </tr>
    </table>

    @if($diseno->descripcion)
    <table class="info-box">
        <tr>
            <td style="width: 100%; border-right: none;">
                <span class="info-label">Descripción Técnica del Proyecto</span>
                <span class="info-value" style="font-weight: normal; font-size: 9px;">{{ $diseno->descripcion }}</span>
            </td>
        </tr>
    </table>
    @endif

    <!-- ══════════════════════════════════════════════════════════
         TABLA 1: DESPIECE MILIMÉTRICO (muebles_piezas)
    ══════════════════════════════════════════════════════════════ -->
    <div class="section-header section-header-first">
        📐 Tabla 1 — Despiece Milimétrico para Sierra de Corte (muebles_piezas)
    </div>
    <div class="section-subheader">
        Dimensiones en milímetros (mm) · Coordenadas 3D del diseño paramétrico Three.js · Área = Ancho × Alto / 1,000,000
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">#</th>
                <th style="width: 26%;">Nombre de Pieza</th>
                <th class="text-right" style="width: 10%;">Ancho (mm)</th>
                <th class="text-right" style="width: 10%;">Alto (mm)</th>
                <th class="text-right" style="width: 10%;">Espesor (mm)</th>
                <th class="text-right" style="width: 10%;">Área (m²)</th>
                <th style="width: 16%;">Material</th>
                <th class="text-center" style="width: 14%;">Coords 3D (X,Y,Z)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($diseno->piezas as $idx => $pieza)
            <tr class="{{ $idx % 2 == 0 ? '' : '' }}">
                <td class="text-center text-gray fw-bold">{{ $idx + 1 }}</td>
                <td class="fw-bold text-blue">{{ $pieza->nombre_pieza_muebles }}</td>
                <td class="text-right fw-bold mono">{{ number_format($pieza->ancho_mm_muebles, 1) }}</td>
                <td class="text-right fw-bold mono">{{ number_format($pieza->alto_mm_muebles, 1) }}</td>
                <td class="text-right mono">{{ number_format($pieza->espesor_mm_muebles, 1) }}</td>
                <td class="text-right text-green fw-bold mono">{{ number_format($pieza->metrosCuadrados, 4) }}</td>
                <td class="text-gray">{{ $pieza->material->nombre_material_muebles ?? 'Melamina 18mm' }}</td>
                <td class="text-center coord-cell">
                    [{{ (int)$pieza->posicion_x }}, {{ (int)$pieza->posicion_y }}, {{ (int)$pieza->posicion_z }}]
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-gray" style="padding: 14px;">
                    No se registraron piezas de corte para este diseño.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($diseno->piezas->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">
                    TOTALES DEL DESPIECE:
                </td>
                <td class="text-right text-green">
                    {{ number_format($m2Total, 4) }} m²
                </td>
                <td colspan="2" class="text-gray" style="font-size: 8px; font-weight: normal;">
                    +15% desperdicio = {{ number_format($m2Desp, 4) }} m² → <strong>{{ $planchas }} plancha(s)</strong>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- ══════════════════════════════════════════════════════════
         TABLA 2: LISTA DE HERRAJES BOM (muebles_herrajes_por_proyecto)
    ══════════════════════════════════════════════════════════════ -->
    <div class="section-header">
        🔩 Tabla 2 — Lista de Herrajes e Insumos BOM (muebles_herrajes_por_proyecto)
    </div>
    <div class="section-subheader">
        Insumos requeridos para el ensamblaje · Precios referenciales según catálogo Madeart S.R.L.
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">#</th>
                <th style="width: 38%;">Herraje / Insumo de Ensamble</th>
                <th class="text-center" style="width: 14%;">Cantidad Requerida</th>
                <th class="text-right" style="width: 18%;">Precio Unitario (Bs)</th>
                <th class="text-right" style="width: 18%;">Subtotal Estimado (Bs)</th>
                <th class="text-center" style="width: 8%;">Tipo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($diseno->herrajesProyecto as $idx => $hp)
            @php
                $herraje = $hp->catalogoHerraje;
                $subtotal = $hp->subtotalCosto;
            @endphp
            <tr>
                <td class="text-center text-gray fw-bold">{{ $idx + 1 }}</td>
                <td class="fw-bold">{{ $herraje->nombre_herraje_global ?? 'Herraje sin nombre' }}</td>
                <td class="text-center">
                    <span class="badge-dark">{{ $hp->cantidad_necesaria_proyecto }} uds.</span>
                </td>
                <td class="text-right mono">
                    {{ $herraje ? number_format($herraje->precio_referencial, 2) : '—' }}
                </td>
                <td class="text-right fw-bold mono text-green">
                    {{ number_format($subtotal, 2) }}
                </td>
                <td class="text-center">
                    <span class="badge-warning">
                        {{ $herraje->tipo_visibilidad_herraje ?? 'Est.' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-gray" style="padding: 14px;">
                    No se vincularon herrajes a este proyecto. El ensamble usa fijaciones integradas.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($diseno->herrajesProyecto->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">SUBTOTAL HERRAJES:</td>
                <td class="text-right">
                    Bs {{ number_format($costoMelamina, 2) }} (Melamina)
                </td>
                <td class="text-right text-green fw-bold">
                    Bs {{ number_format($costoHerrajes, 2) }}
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right" style="color: #1e3a5f;">
                    COSTO TOTAL ESTIMADO (Melamina + Herrajes):
                </td>
                <td class="text-right" style="color: #065f46; font-size: 12px;">
                    Bs {{ number_format($costoTotal, 2) }}
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- ══════════════════════════════════════════════════════════
         TABLA 3: SECUENCIA DE ENSAMBLAJE (muebles_pasos_ensamblaje)
    ══════════════════════════════════════════════════════════════ -->
    @if($diseno->pasosEnsamblaje->count() > 0)
    <div class="section-header">
        🔨 Tabla 3 — Secuencia de Ensamblaje (muebles_pasos_ensamblaje)
    </div>
    <div class="section-subheader">
        Pasos ordenados para el armador de taller · Siga la secuencia rigurosamente para garantizar la rigidez estructural.
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 8%;">Paso</th>
                <th>Instrucción Técnica de Ensamblaje</th>
                <th class="text-center" style="width: 12%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($diseno->pasosEnsamblaje as $paso)
            <tr>
                <td class="text-center fw-bold" style="color: #1e3a5f;">
                    {{ str_pad($paso->numero_paso_muebles, 2, '0', STR_PAD_LEFT) }}
                </td>
                <td>{{ $paso->titulo_paso_muebles }}</td>
                <td class="text-center">
                    <span class="badge-warning">Pendiente</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- ══════════════════════════════════════════════════════════
         FIRMAS Y AUTORIZACIÓN
    ══════════════════════════════════════════════════════════════ -->
    <div class="divider"></div>

    <table class="firma-table">
        <tr>
            <td>
                <div class="firma-line">
                    Armador de Taller
                    <div class="firma-label">Nombre y C.I. / Firma</div>
                </div>
            </td>
            <td>
                <div class="firma-line">
                    Supervisor de Calidad
                    <div class="firma-label">Nombre y C.I. / Firma</div>
                </div>
            </td>
            <td>
                <div class="firma-line">
                    Gerente de Planta — Madeart S.R.L.
                    <div class="firma-label">Sello y Firma de Aprobación</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

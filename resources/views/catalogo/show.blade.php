@extends('layouts.app')

@section('content')
@php
    $estructura = null;
    if (!empty($mueble->estructura_json)) {
        $estructura = json_decode($mueble->estructura_json, true);
    }
    $piezas = $estructura['piezas'] ?? $estructura['pieces'] ?? [];
    $objetos = $estructura['objetos'] ?? [];

    // Si no hay piezas explícitas pero hay objetos 3D Three.js, generamos el despiece a partir de los objetos
    if (empty($piezas) && !empty($objetos)) {
        foreach ($objetos as $idx => $obj) {
            $w = round($obj['w'] ?? 500);
            $h = round($obj['h'] ?? 500);
            $d = round($obj['d'] ?? 18);
            $dims = [$w, $h, $d];
            rsort($dims);
            $piezas[] = [
                'name' => $obj['name'] ?? ('Pieza ' . ($idx + 1)),
                'pw' => $dims[0],
                'ph' => $dims[1],
                'thickness' => $dims[2],
                'pieceType' => $obj['pieceType'] ?? ($obj['h'] > $obj['w'] ? 'vertical' : 'horizontal'),
                'textureKey' => $obj['textureKey'] ?? 'roble'
            ];
        }
    }

    // Si aún está vacío, deducir un despiece base estándar según el tipo y dimensiones del mueble
    if (empty($piezas)) {
        $dimStr = $mueble->dimensiones ?? '1000 × 500 × 750 mm';
        preg_match_all('/\d+/', $dimStr, $matches);
        $wTotal = isset($matches[0][0]) ? (int)$matches[0][0] : 1000;
        $dTotal = isset($matches[0][1]) ? (int)$matches[0][1] : 500;
        $hTotal = isset($matches[0][2]) ? (int)$matches[0][2] : 750;
        $esp = 18;

        $piezas = [
            ['name' => 'Cubierta Superior', 'pw' => $wTotal, 'ph' => $dTotal, 'thickness' => $esp, 'pieceType' => 'horizontal'],
            ['name' => 'Base Inferior', 'pw' => max(100, $wTotal - ($esp * 2)), 'ph' => $dTotal, 'thickness' => $esp, 'pieceType' => 'horizontal'],
            ['name' => 'Lateral Izquierdo', 'pw' => max(100, $hTotal - $esp), 'ph' => $dTotal, 'thickness' => $esp, 'pieceType' => 'vertical'],
            ['name' => 'Lateral Derecho', 'pw' => max(100, $hTotal - $esp), 'ph' => $dTotal, 'thickness' => $esp, 'pieceType' => 'vertical'],
            ['name' => 'Fondo Trasero', 'pw' => max(100, $wTotal - ($esp * 2)), 'ph' => max(100, $hTotal - ($esp * 2)), 'thickness' => 3, 'pieceType' => 'vertical'],
            ['name' => 'Repisa Interior', 'pw' => max(100, $wTotal - ($esp * 2)), 'ph' => max(100, $dTotal - 20), 'thickness' => $esp, 'pieceType' => 'horizontal'],
        ];
    }

    // Cálculos matemáticos del BOM y Hoja de Melamina
    $totalPiezas = count($piezas);
    $m2TotalNeto = 0.0;
    $metrosLinealesCanto = 0.0;

    foreach ($piezas as $p) {
        $pw = (float)($p['pw'] ?? 500);
        $ph = (float)($p['ph'] ?? 500);
        $m2TotalNeto += ($pw * $ph) / 1000000.0;
        // Estimación de cantos visibles (60% de los bordes llevan tapacanto)
        $metrosLinealesCanto += (($pw * 2) + ($ph * 2)) / 1000.0 * 0.6;
    }
    $m2TotalNeto = round($m2TotalNeto, 3);
    $metrosLinealesCanto = round($metrosLinealesCanto, 1);

    // Formato de plancha estándar Masisa/Pelikano en Sudamérica: 2750 x 1830 mm = 5.0325 m2
    $areaPlanchaStd = 2.750 * 1.830;
    $m2ConMerma = round($m2TotalNeto * 1.15, 3); // 15% merma por cortes de sierra
    $planchasEstimadas = max(1, (int)ceil($m2ConMerma / $areaPlanchaStd));
    $porcentajeAprovechamiento = min(98.5, round(($m2TotalNeto / ($planchasEstimadas * $areaPlanchaStd)) * 100, 1));
    $porcentajeMerma = round(100 - $porcentajeAprovechamiento, 1);
    $retazoM2 = round(($planchasEstimadas * $areaPlanchaStd) - $m2TotalNeto, 2);

    // Estimación técnica de tornillos e insumos de unión
    $soberbiosCount = max(12, $totalPiezas * 4);
    $tornillosChicosCount = max(16, ($totalPiezas * 3) + 8);
    $minifixCount = max(8, (int)round($totalPiezas * 2));
    $tarugosCount = max(12, $totalPiezas * 3);
    $tapitasCount = $soberbiosCount;
@endphp

<div class="container py-4 py-md-5">
    <!-- Top Navigation Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary fw-bold px-3 py-2 shadow-sm rounded-3">
            &larr; Volver al Catálogo
        </a>
        <button type="button" class="btn btn-dark fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2 rounded-3" onclick="window.imprimirPlanoMueble()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Descargar Planos / Ficha Técnica (PDF)
        </button>
    </div>

    <!-- Mensaje de éxito tras acciones -->
    @if(session('exito_favorito'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm rounded-3" role="alert">
            {{ session('exito_favorito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tarjeta Principal de Detalle -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-3 p-md-4 mb-5">
        <div class="row g-4 align-items-center">
            
            <!-- Columna Izquierda: Visor Multimedia (Foto o 3D) -->
            <div class="col-lg-6">
                <!-- Selector de Modo de Vista si hay datos 3D -->
                @if(!empty($objetos))
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-secondary active fw-bold px-3 py-1 d-inline-flex align-items-center gap-1 rounded-3" id="btn-view-photo">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            Fotografía / Render
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold px-3 py-1 d-inline-flex align-items-center gap-1 rounded-3" id="btn-view-3d">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            Visor 3D Interactivo
                        </button>
                    </div>
                @endif

                <div class="position-relative overflow-hidden rounded-4 border bg-light d-flex align-items-center justify-content-center shadow-sm" style="min-height: 400px; height: 440px;">
                    <!-- Contenedor Foto Principal -->
                    <div id="container-photo" class="w-100 h-100 d-flex align-items-center justify-content-center p-2">
                        @if($mueble->imagen_url)
                            <img src="{{ asset('storage/' . $mueble->imagen_url) }}" 
                                 alt="{{ $mueble->nombre }}" 
                                 id="mueble-main-img"
                                 class="img-fluid w-100 h-100 object-fit-contain d-block rounded-3">
                        @else
                            <div class="text-center p-5 text-secondary opacity-50">
                                <svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <path d="M3 9h18M9 3v18"/>
                                </svg>
                                <p class="mt-2 mb-0 small fw-bold text-uppercase">Sin imagen</p>
                            </div>
                        @endif
                    </div>

                    <!-- Contenedor Visor 3D Interactivo -->
                    @if(!empty($objetos))
                        <div id="container-3d-viewer" class="w-100 h-100 position-absolute top-0 start-0" style="display: none; background: #f8fafc;">
                            <div id="three-viewer-canvas" style="width: 100%; height: 100%;"></div>
                            <div class="position-absolute bottom-0 start-0 m-3 bg-dark text-white px-3 py-1 rounded-pill small opacity-75 d-flex align-items-center gap-2">
                                <span style="font-size: 11px;">Arrastra para girar · Rueda para zoom</span>
                            </div>
                            <button type="button" id="btn-toggle-auto-rot" class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 shadow-sm fw-bold border d-inline-flex align-items-center gap-1 rounded-3">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                Rotación
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha: Información y Acciones -->
            <div class="col-lg-6 d-flex flex-column justify-content-between">
                <div>
                    <!-- Vistas y Título -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h1 class="fw-bold text-dark text-capitalize mb-0 fs-2">
                            {{ $mueble->nombre }}
                        </h1>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small d-inline-flex align-items-center gap-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            {{ $mueble->vistas ?? 0 }} vistas
                        </span>
                    </div>

                    <!-- Precio Destacado -->
                    <div class="my-3">
                        <span class="fs-1 fw-bold text-warning">Bs {{ number_format($mueble->precio ?? 0, 2) }}</span>
                        <span class="text-muted small ms-2 fw-semibold">Precio estimado de fabricación</span>
                    </div>

                    <!-- Maestro / Diseñador -->
                    <div class="bg-light p-3 rounded-3 mb-3 border-start border-4 border-warning">
                        <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Diseñado y Creado por:</span>
                        <span class="fw-bold text-dark fs-6">Maestro {{ $mueble->user->nombre ?? 'Carpintero' }} {{ $mueble->user->apellido_paterno ?? '' }}</span>
                    </div>

                    <!-- Especificaciones Clave en Grid -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Dimensiones Bounding</div>
                                <div class="fw-bold text-dark small">{{ $mueble->dimensiones ?? '1200 × 600 × 750 mm' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Material Principal</div>
                                <div class="fw-bold text-dark small">{{ $mueble->material ?? 'Melamina 18mm' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            Descripción del Mueble
                        </h6>
                        <p class="text-secondary lh-base mb-0 small">
                            {{ $mueble->descripcion ?? 'Mueble modular fabricado con tableros de melamina de alta durabilidad y uniones mecánicas precisas.' }}
                        </p>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="pt-3 border-top d-flex flex-column gap-2">
                    <!-- Botón Guía de Armado -->
                    <a href="{{ route('catalogo.armado', $mueble->id) }}" 
                       class="btn btn-dark btn-lg fw-bold w-100 py-3 shadow-sm d-flex align-items-center justify-content-center gap-2 rounded-3"
                       style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: none;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                        </svg>
                        Ver Guía de Armado Paso a Paso
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>

                    <div class="d-flex flex-sm-row gap-2">
                        <!-- Botón de WhatsApp -->
                        @if($linkWhatsapp)
                            <a href="{{ $linkWhatsapp }}" 
                                target="_blank" 
                                class="btn btn-success btn-lg fw-bold flex-grow-1 shadow-sm py-2 d-flex align-items-center justify-content-center gap-2 rounded-3">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                Contactar por WhatsApp
                            </a>
                        @else
                            <button class="btn btn-secondary btn-lg fw-bold flex-grow-1 py-2 rounded-3" disabled>
                                Contacto no disponible
                            </button>
                        @endif

                        <!-- Botón Favoritos -->
                        @auth
                            <form action="{{ route('favoritos.toggle', $mueble->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-lg fw-bold text-dark w-100 py-2 h-100 d-flex align-items-center justify-content-center gap-2 rounded-3">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                    Favorito
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-warning btn-lg fw-bold text-dark w-100 py-2 h-100 d-flex align-items-center justify-content-center gap-2 rounded-3">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                Favorito
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN COMPLETA: Ficha Técnica, Cortes de Melamina y Lista de Despiece -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-5" id="seccion-despiece">
        <!-- Encabezado de la Ficha Técnica -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Documentación de Taller
                    </span>
                    <span class="text-muted small">Mydo-Mar Ingeniería de Muebles</span>
                </div>
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 22h20"/><path d="M4 2v20"/><path d="M8 6v16"/><path d="M12 10v12"/><path d="M16 14v8"/><path d="M20 18v4"/></svg>
                    Ficha Técnica, Cortes de Melamina y Despiece
                </h3>
                <p class="text-muted small mb-0">
                    Esquema de cortes en tableros, lista de piezas al milímetro, desglose exacto de tornillería y caja de herramientas recomendadas para taller.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-dark fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2 rounded-3" onclick="window.imprimirPlanoMueble()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Imprimir Planos / Guardar PDF
                </button>
                <a href="{{ route('catalogo.armado', $mueble->id) }}" class="btn btn-warning fw-bold text-dark px-3 py-2 shadow-sm d-flex align-items-center gap-2 rounded-3">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Guía de Armado 3D
                </a>
            </div>
        </div>

        <!-- 4 Tarjetas Métricas Rápidas de Fabricación -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                    <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.05em;">Hojas de Melamina</span>
                    <div class="d-flex align-items-baseline gap-1 mt-1">
                        <span class="fs-4 fw-bold text-dark">{{ $planchasEstimadas }}</span>
                        <span class="small text-muted fw-semibold">plancha(s)</span>
                    </div>
                    <span class="text-muted small mt-1 d-block" style="font-size: 0.75rem;">
                        Aprov.: <strong>{{ $porcentajeAprovechamiento }}%</strong> (2750×1830 mm)
                    </span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                    <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.05em;">Superficie Neta</span>
                    <div class="d-flex align-items-baseline gap-1 mt-1">
                        <span class="fs-4 fw-bold text-dark">{{ $m2TotalNeto }}</span>
                        <span class="small text-muted fw-semibold">m² útiles</span>
                    </div>
                    <span class="text-muted small mt-1 d-block" style="font-size: 0.75rem;">
                        {{ $totalPiezas }} piezas · Sierra 3.0 mm
                    </span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                    <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.05em;">Tornillos Soberbios</span>
                    <div class="d-flex align-items-baseline gap-1 mt-1">
                        <span class="fs-4 fw-bold text-primary">{{ $soberbiosCount }}</span>
                        <span class="small text-muted fw-semibold">unidades</span>
                    </div>
                    <span class="text-muted small mt-1 d-block" style="font-size: 0.75rem;">
                        Medida 4×50mm (PH2 avellanada)
                    </span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-dark">
                    <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.05em;">Herramientas Taller</span>
                    <div class="d-flex align-items-baseline gap-1 mt-1">
                        <span class="fs-4 fw-bold text-dark">8</span>
                        <span class="small text-muted fw-semibold">equipos clave</span>
                    </div>
                    <span class="text-muted small mt-1 d-block" style="font-size: 0.75rem;">
                        Broca avellanadora 4mm, escuadra, etc.
                    </span>
                </div>
            </div>
        </div>

        <!-- Pestañas de Navegación de la Ficha Técnica -->
        <ul class="nav nav-pills gap-2 mb-4 p-1 bg-light rounded-4 border" id="fichaTecnicaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2" 
                        id="tab-cortes-btn" data-bs-toggle="pill" data-bs-target="#tab-cortes" type="button" role="tab">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    Cortes de Melamina (Mapa de Hojas)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2" 
                        id="tab-despiece-btn" data-bs-toggle="pill" data-bs-target="#tab-despiece" type="button" role="tab">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Lista de Despiece Detallada ({{ $totalPiezas }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2" 
                        id="tab-tornillos-btn" data-bs-toggle="pill" data-bs-target="#tab-tornillos" type="button" role="tab">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Tornillos e Insumos de Fijación
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2" 
                        id="tab-herramientas-btn" data-bs-toggle="pill" data-bs-target="#tab-herramientas" type="button" role="tab">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Herramientas Recomendadas
                </button>
            </li>
        </ul>

        <!-- Contenido de las Pestañas -->
        <div class="tab-content" id="fichaTecnicaTabsContent">
            
            <!-- PESTAÑA 1: CORTES DE MELAMINA / ESQUEMA DE HOJAS -->
            <div class="tab-pane fade show active" id="tab-cortes" role="tabpanel">
                <div class="bg-light p-3 rounded-4 border mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <!-- Selectores de Formato de Plancha -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem;">Formato de Hoja:</span>
                            <div class="btn-group shadow-sm" role="group">
                                <button type="button" class="btn btn-sm btn-dark fw-bold px-3 active" id="btn-sheet-format-2750">
                                    2750 × 1830 mm (Sudamérica)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-dark fw-bold px-3" id="btn-sheet-format-2440">
                                    2440 × 1830 mm (8 × 6 pies)
                                </button>
                            </div>
                        </div>

                        <!-- Selector de Hoja si hay múltiples planchas -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="small fw-bold text-muted" id="sheet-pagination-label">Hoja 1 de 1</span>
                            <div class="btn-group btn-group-sm" id="sheet-nav-group" style="display: none;">
                                <button type="button" class="btn btn-outline-secondary fw-bold" id="btn-sheet-prev">&larr;</button>
                                <button type="button" class="btn btn-outline-secondary fw-bold" id="btn-sheet-next">&rarr;</button>
                            </div>
                        </div>

                        <!-- Indicadores de Sierra y Veta -->
                        <div class="d-flex align-items-center gap-3 small text-muted">
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="badge bg-secondary rounded-pill" style="width: 8px; height: 8px; padding: 0;"></span>
                                Sierra: <strong>3.0 mm (Kerf)</strong>
                            </span>
                            <span class="d-inline-flex align-items-center gap-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                Veta Longitudinal
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contenedor del Esquema Gráfico de Corte de Hoja -->
                <div class="card border rounded-4 overflow-hidden mb-3 bg-white shadow-sm">
                    <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded" id="sheet-header-badge">Hoja #1</span>
                            <span class="small fw-bold text-dark" id="sheet-dimension-label">Plancha Melamina 2750 × 1830 × 18 mm</span>
                        </div>
                        <!-- Leyenda de colores -->
                        <div class="d-flex align-items-center gap-3 small" style="font-size: 0.72rem;">
                            <span class="d-inline-flex align-items-center gap-1">
                                <span style="display:inline-block; width:12px; height:12px; background:#fef3c7; border:1px solid #d97706; border-radius:2px;"></span>
                                Piezas Cortadas
                            </span>
                            <span class="d-inline-flex align-items-center gap-1">
                                <span style="display:inline-block; width:12px; height:12px; background:#f1f5f9; border:1px dashed #94a3b8; border-radius:2px;"></span>
                                Retazo / Merma Sobrante
                            </span>
                        </div>
                    </div>

                    <!-- Canvas / SVG Interactivo de la Plancha -->
                    <div class="p-3 text-center position-relative" style="background: #f8fafc; min-height: 380px;">
                        <div id="cutting-sheet-container" class="w-100 d-flex justify-content-center align-items-center">
                            <!-- Inyectado vía JavaScript renderSheetLayout() -->
                        </div>
                    </div>

                    <!-- Resumen Numérico de la Hoja Seleccionada -->
                    <div class="card-footer bg-light p-3 border-top">
                        <div class="row g-2 text-center small">
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Superficie Plancha</span>
                                <strong class="text-dark fs-6" id="kpi-sheet-total-area">5.03 m²</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Superficie Piezas</span>
                                <strong class="text-success fs-6" id="kpi-sheet-used-area">{{ $m2TotalNeto }} m²</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Retazo Reutilizable</span>
                                <strong class="text-secondary fs-6" id="kpi-sheet-waste-area">{{ $retazoM2 }} m²</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Eficiencia de Corte</span>
                                <strong class="text-warning fs-6" id="kpi-sheet-efficiency">{{ $porcentajeAprovechamiento }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nota Técnica del Optimizador de Corte -->
                <div class="p-3 bg-white rounded-3 border-start border-4 border-warning border small text-secondary">
                    <strong class="text-dark d-block mb-1">Recomendaciones del Maestro para el Seccionado:</strong>
                    1. Realizar primer corte longitudinal (despunte perimetral de 10mm) para sanear los bordes de fábrica del tablero.<br>
                    2. Respetar el espesor del disco seccionador (3.0 mm) en cada pasada para mantener las medidas nominales exactas.<br>
                    3. Al cortar piezas con acabado amaderado, alinear el largo de la pieza paralelamente a la veta para continuidad visual estética.
                </div>
            </div>

            <!-- PESTAÑA 2: LISTA DE DESPIECE COMPLETA -->
            <div class="tab-pane fade" id="tab-despiece" role="tabpanel">
                <div class="table-responsive border rounded-4 bg-white shadow-sm mb-3">
                    <table class="table table-hover align-middle mb-0" id="tabla-despiece-piezas">
                        <thead class="table-light">
                            <tr class="small text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nombre de Pieza</th>
                                <th class="text-center">Largo / Alto</th>
                                <th class="text-center">Ancho</th>
                                <th class="text-center">Espesor</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-center">Orientación</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-end">Tapacanto Sugerido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($piezas as $idx => $p)
                                @php
                                    $isVert = ($p['pieceType'] ?? '') === 'vertical';
                                    $pw = round($p['pw'] ?? 0);
                                    $ph = round($p['ph'] ?? 0);
                                    $th = round($p['thickness'] ?? 18);
                                @endphp
                                <tr id="row-piece-{{ $idx + 1 }}">
                                    <td class="text-center fw-bold text-muted">
                                        <span class="badge bg-light text-dark border rounded-circle" style="width: 26px; height: 26px; line-height: 18px; display:inline-block;">
                                            {{ $idx + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block">{{ $p['name'] ?? 'Pieza ' . ($idx + 1) }}</strong>
                                        <span class="text-muted small" style="font-size: 0.72rem;">{{ $mueble->material ?? 'Melamina 18mm' }}</span>
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ $pw }} mm</td>
                                    <td class="text-center fw-bold text-dark">{{ $ph }} mm</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $th }} mm</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-2">1 ud.</span>
                                    </td>
                                    <td class="text-center small text-muted">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                            Longitudinal
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($isVert)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded">Vertical</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded">Horizontal</span>
                                        @endif
                                    </td>
                                    <td class="text-end small">
                                        <span class="badge bg-light text-secondary border">PVC 0.45mm (Cantos Vistos)</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold small">
                                <td colspan="2" class="text-dark">TOTALES GENERALES</td>
                                <td colspan="3" class="text-center text-muted">Superficie Total Útil: {{ $m2TotalNeto }} m²</td>
                                <td class="text-center text-dark">{{ $totalPiezas }} uds.</td>
                                <td colspan="3" class="text-end text-muted">Canto estimado: ~{{ $metrosLinealesCanto }} m lineales</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- PESTAÑA 3: TORNILLOS E INSUMOS DE FIJACIÓN -->
            <div class="tab-pane fade" id="tab-tornillos" role="tabpanel">
                <div class="row g-3 mb-4">
                    
                    <!-- 1. Tornillo Soberbio 4x50mm -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm hover-shadow transition">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="p-2 bg-warning bg-opacity-10 rounded-3 text-warning">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                </div>
                                <span class="badge bg-warning text-dark fw-bold fs-6 px-3 py-1 rounded-pill">
                                    {{ $soberbiosCount }} uds.
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Tornillo Soberbio 4×50 mm</h5>
                            <span class="text-muted small d-block mb-2">Cincado con cabeza avellanada Phillips PH2</span>
                            <p class="text-secondary small mb-3">
                                Fijación estructural directa entre tableros de 18mm (laterales con base, techo y divisiones intermedias fijas).
                            </p>
                            <div class="mt-auto pt-2 border-top small text-muted">
                                <strong>Perforación:</strong> Broca guía avellanadora de 4 mm
                            </div>
                        </div>
                    </div>

                    <!-- 2. Tornillo Spax 3.5x16mm -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm hover-shadow transition">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>
                                </div>
                                <span class="badge bg-primary text-white fw-bold fs-6 px-3 py-1 rounded-pill">
                                    {{ $tornillosChicosCount }} uds.
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Tornillo Madera 3.5×16 mm</h5>
                            <span class="text-muted small d-block mb-2">Punta aguja autorroscante zincado</span>
                            <p class="text-secondary small mb-3">
                                Instalación de bases de bisagras cazoleta, correderas telescópicas, ángulos esquineros de refuerzo y fondo de 3mm.
                            </p>
                            <div class="mt-auto pt-2 border-top small text-muted">
                                <strong>Punta:</strong> Phillips PH2 con tope de atornillador
                            </div>
                        </div>
                    </div>

                    <!-- 3. Sistema Minifix 15mm -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm hover-shadow transition">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="p-2 bg-success bg-opacity-10 rounded-3 text-success">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="4"/></svg>
                                </div>
                                <span class="badge bg-success text-white fw-bold fs-6 px-3 py-1 rounded-pill">
                                    {{ $minifixCount }} sets
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Caja Excéntrica Minifix 15 mm</h5>
                            <span class="text-muted small d-block mb-2">Perno de unión 34mm + tambor de zamak</span>
                            <p class="text-secondary small mb-3">
                                Sistema de unión desmontable oculto para ensamblar sin tornillos vistos desde el exterior del mueble.
                            </p>
                            <div class="mt-auto pt-2 border-top small text-muted">
                                <strong>Mecanizado:</strong> Broca Forstner 15mm (prof. 12mm)
                            </div>
                        </div>
                    </div>

                    <!-- 4. Tarugos de Madera 8x30mm -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm hover-shadow transition">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="p-2 bg-secondary bg-opacity-10 rounded-3 text-secondary">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="3" x2="12" y2="21"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="18" x2="16" y2="18"/></svg>
                                </div>
                                <span class="badge bg-secondary text-white fw-bold fs-6 px-3 py-1 rounded-pill">
                                    {{ $tarugosCount }} uds.
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Tarugos Estriados 8×30 mm</h5>
                            <span class="text-muted small d-block mb-2">Madera prensada de haya o eucalipto</span>
                            <p class="text-secondary small mb-3">
                                Guía de alineación precisa y resistencia al cizallamiento en cada unión de tablero. Se acoplan con martillo de goma.
                            </p>
                            <div class="mt-auto pt-2 border-top small text-muted">
                                <strong>Broca:</strong> 8 mm para madera con tope de profundidad
                            </div>
                        </div>
                    </div>

                    <!-- 5. Tapacanto Melamínico / PVC -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm hover-shadow transition">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="p-2 bg-info bg-opacity-10 rounded-3 text-info">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/></svg>
                                </div>
                                <span class="badge bg-info text-white fw-bold fs-6 px-3 py-1 rounded-pill">
                                    ~{{ $metrosLinealesCanto }} m
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Tapacanto PVC 0.45 / 2 mm</h5>
                            <span class="text-muted small d-block mb-2">Color tono {{ $mueble->material ?? 'Melamina' }}</span>
                            <p class="text-secondary small mb-3">
                                Sellado perimetral de bordes expuestos para evitar penetración de humedad ambiente y otorgar acabado suave al tacto.
                            </p>
                            <div class="mt-auto pt-2 border-top small text-muted">
                                <strong>Pegado:</strong> Termofusión en enchapadora o cola de contacto
                            </div>
                        </div>
                    </div>

                    <!-- 6. Accesorios de Terminación (Tapas y Deslizadores) -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm hover-shadow transition">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="p-2 bg-dark bg-opacity-10 rounded-3 text-dark">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <span class="badge bg-dark text-white fw-bold fs-6 px-3 py-1 rounded-pill">
                                    {{ $tapitasCount }} + 4 uds.
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Tapas y Deslizadores Base</h5>
                            <span class="text-muted small d-block mb-2">Tapitas adhesivas + regatones clavables</span>
                            <p class="text-secondary small mb-3">
                                Tapitas adhesivas circulares para disimular cabezas de tornillos, y 4 deslizadores plásticos de base para aislar del piso.
                            </p>
                            <div class="mt-auto pt-2 border-top small text-muted">
                                <strong>Aislación:</strong> Despeje de 5mm sobre el nivel de suelo
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tabla Resumen para Ferretería / Lista de Compras -->
                <div class="card border rounded-4 bg-white p-3 shadow-sm">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Lista Rápida de Compras para Ferretería
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Insumo</th>
                                    <th>Especificación</th>
                                    <th class="text-center">Cant. Requerida</th>
                                    <th>Uso en Mueble</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr>
                                    <td><strong>Tornillos Soberbios</strong></td>
                                    <td>4×50 mm cincado cabeza cruz PH2</td>
                                    <td class="text-center fw-bold text-primary">{{ $soberbiosCount }} uds.</td>
                                    <td>Estructura principal de melamina 18mm</td>
                                </tr>
                                <tr>
                                    <td><strong>Tornillos Madera Spax</strong></td>
                                    <td>3.5×16 mm punta fina zincado</td>
                                    <td class="text-center fw-bold text-dark">{{ $tornillosChicosCount }} uds.</td>
                                    <td>Bisagras, correderas, escuadras y fondo</td>
                                </tr>
                                <tr>
                                    <td><strong>Minifix (Perno + Tambor)</strong></td>
                                    <td>Caja 15 mm / Perno 34 mm</td>
                                    <td class="text-center fw-bold text-success">{{ $minifixCount }} sets</td>
                                    <td>Unión desarmable interior oculta</td>
                                </tr>
                                <tr>
                                    <td><strong>Tarugos Estriados</strong></td>
                                    <td>Madera 8×30 mm</td>
                                    <td class="text-center fw-bold text-secondary">{{ $tarugosCount }} uds.</td>
                                    <td>Guías y resistencia al esfuerzo de corte</td>
                                </tr>
                                <tr>
                                    <td><strong>Tapacanto PVC</strong></td>
                                    <td>Ancho 22 mm × Grosor 0.45 mm</td>
                                    <td class="text-center fw-bold text-info">~{{ $metrosLinealesCanto }} m</td>
                                    <td>Enchapado de bordes visibles</td>
                                </tr>
                                <tr>
                                    <td><strong>Deslizadores Plásticos</strong></td>
                                    <td>Diámetro 15 mm clavables</td>
                                    <td class="text-center fw-bold text-dark">4 uds.</td>
                                    <td>Base y protección contra humedad del piso</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA 4: HERRAMIENTAS RECOMENDADAS -->
            <div class="tab-pane fade" id="tab-herramientas" role="tabpanel">
                <div class="row g-3 mb-4">
                    
                    <!-- 1. Atornillador Inalámbrico -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-warning">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Taladro / Atornillador</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">12V o 18V Inalámbrico</span>
                            <p class="text-secondary small mb-2">
                                Imprescindible con embrague de torque regulable para no aislar ni rodar la rosca en el tablero aglomerado.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Torque:</strong> Ajustar en posición 5 a 8 para soberbio.
                            </span>
                        </div>
                    </div>

                    <!-- 2. Broca Avellanadora 4mm -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-primary">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="22"/><polygon points="12,2 8,7 16,7"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Broca Avellanadora 4mm</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">Para Madera / Melamina</span>
                            <p class="text-secondary small mb-2">
                                Perfora la guía y labra el avellanado a 45° en un solo paso, permitiendo que la cabeza quede al ras exacto sin fisurar.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Broca guía:</strong> Diámetro 4 mm con tope regulable.
                            </span>
                        </div>
                    </div>

                    <!-- 3. Puntas PH2 y PZ2 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-success">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4.5 2 7-6.5-4.5-6.5 4.5 2-7-5.5-4.5h7z"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Puntas Phillips PH2</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">Portapuntas Magnético</span>
                            <p class="text-secondary small mb-2">
                                Calce perfecto sin resbalones para no dañar la cabeza del tornillo ni rayar la melamina alrededor de la perforación.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Tipo:</strong> Punta PH2 endurecida de 50 mm.
                            </span>
                        </div>
                    </div>

                    <!-- 4. Escuadra Metálica 300mm -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-danger">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 21V3l4 4v10h10l4 4"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Escuadra Metálica 90°</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">Sombrero de 300 mm</span>
                            <p class="text-secondary small mb-2">
                                Para comprobar la perpendicularidad exacta de los laterales y la base antes de apretar definitivamente los tornillos.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Control:</strong> Medir también diagonales cruzadas.
                            </span>
                        </div>
                    </div>

                    <!-- 5. Martillo de Goma Blanco -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-secondary">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12l-8.5 8.5a2.12 2.12 0 0 1-3-3L12 9"/><path d="M17.64 4.36a3 3 0 0 1 4 4L19 11l-4-4 2.64-2.64z"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Martillo de Goma Blanco</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">Nylon o Goma No Marcante</span>
                            <p class="text-secondary small mb-2">
                                Asienta tarugos y encastres de tableros sin dejar manchas de caucho negro ni fracturar los cantos de melamina.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Seguridad:</strong> Golpear siempre con madera de descarte intermedia.
                            </span>
                        </div>
                    </div>

                    <!-- 6. Cinta Métrica de Precisión -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-warning">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Huincha Métrica 5 m</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">Clase II con Freno</span>
                            <p class="text-secondary small mb-2">
                                Para trazado de perforaciones equidistantes y verificación de cotas bounding exteriores e interiores.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Tolerancia:</strong> Medición en milímetros exactos.
                            </span>
                        </div>
                    </div>

                    <!-- 7. Prensas Esquineras 90° -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-primary">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Prensas de Ángulo 90°</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">Sargentos Esquineros</span>
                            <p class="text-secondary small mb-2">
                                Sujetan con fuerza los laterales contra la base en ángulo recto mientras se perfora y atornilla, evitando desplazamientos.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Uso:</strong> 2 prensas por esquina de módulo.
                            </span>
                        </div>
                    </div>

                    <!-- 8. Broca Forstner 35mm / 15mm -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border rounded-4 p-3 h-100 bg-white shadow-sm">
                            <div class="mb-3 text-success">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/></svg>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Broca Forstner 35 / 15mm</h6>
                            <span class="badge bg-light text-dark border mb-2 small" style="width: fit-content;">Acero Rápido HSS</span>
                            <p class="text-secondary small mb-2">
                                Para cazoletas de bisagras de 35 mm en puertas o para cajas de minifix de 15 mm con fondo plano limpio y sin rebabas.
                            </p>
                            <span class="text-muted small mt-auto d-block pt-2 border-top" style="font-size: 0.72rem;">
                                <strong>Profundidad:</strong> Máximo 11.5 a 12.0 mm en melamina 18mm.
                            </span>
                        </div>
                    </div>

                </div>

                <!-- Banner de Seguridad en Taller -->
                <div class="p-3 bg-light rounded-4 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-dark text-warning rounded-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <strong class="text-dark d-block">Equipamiento de Protección Personal (EPP):</strong>
                            <span class="text-secondary small">Gafas de seguridad contra virutas de melamina, mascarilla para polvo fino y calzado cerrado de taller.</span>
                        </div>
                    </div>
                    <span class="badge bg-dark text-white px-3 py-2 rounded-pill small">Norma de Taller Mydo-mar</span>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Three.js si existen objetos 3D -->
@if(!empty($objetos))
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
@endif

<script>
    const objetosData = @json($objetos);
    const piezasData = @json($piezas);
    const muebleNombre = @json($mueble->nombre);
    const muebleDimensiones = @json($mueble->dimensiones ?? '1200 × 600 × 750 mm');
    const muebleMaterial = @json($mueble->material ?? 'Melamina 18mm');
    const maestroNombre = @json('Maestro ' . ($mueble->user->nombre ?? 'Carpintero') . ' ' . ($mueble->user->apellido_paterno ?? ''));
    const mueblePrecio = @json(number_format($mueble->precio ?? 0, 2));
    const muebleImgUrl = @json($mueble->imagen_url ? asset('storage/' . $mueble->imagen_url) : '');
    const soberbiosQty = @json($soberbiosCount);
    const tornillosChicosQty = @json($tornillosChicosCount);
    const minifixQty = @json($minifixCount);
    const tarugosQty = @json($tarugosCount);
    const tapacantoMts = @json($metrosLinealesCanto);

    // ==========================================
    // 1. GENERADOR INTERACTIVO DE MAPA DE HOJAS
    // ==========================================
    let currentSheetFormat = { width: 2750, height: 1830, name: '2750 × 1830 mm' };
    let activeSheetIndex = 0;
    let computedSheets = [];
    const kerfSierra = 3; // mm de espesor del disco

    function computeSheetLayout(sheetWidth, sheetHeight, kerf, pieces) {
        if (!pieces || pieces.length === 0) return [];

        const sorted = pieces.map((p, idx) => {
            const w = Math.round(p.pw || p.w || 500);
            const h = Math.round(p.ph || p.h || 500);
            return {
                id: idx + 1,
                name: p.name || ('Pieza ' + (idx + 1)),
                w: Math.max(w, h),
                h: Math.min(w, h),
                thickness: p.thickness || 18,
                pieceType: p.pieceType || 'horizontal'
            };
        }).sort((a, b) => (b.w * b.h) - (a.w * a.h));

        const sheets = [];
        let currentSheet = {
            width: sheetWidth,
            height: sheetHeight,
            placed: [],
            usedArea: 0
        };

        const margin = 15; // Despunte de fábrica
        let curX = margin;
        let curY = margin;
        let shelfHeight = 0;

        for (const piece of sorted) {
            let pw = piece.w;
            let ph = piece.h;

            let fits = (curX + pw + kerf <= sheetWidth - margin) && (curY + ph + kerf <= sheetHeight - margin);
            if (!fits && (curX + ph + kerf <= sheetWidth - margin) && (curY + pw + kerf <= sheetHeight - margin)) {
                const tmp = pw; pw = ph; ph = tmp;
                fits = true;
            }

            if (fits) {
                currentSheet.placed.push({ ...piece, cutW: pw, cutH: ph, x: curX, y: curY });
                currentSheet.usedArea += (pw * ph);
                curX += pw + kerf;
                if (ph > shelfHeight) shelfHeight = ph;
            } else {
                curX = margin;
                curY += shelfHeight + kerf;
                shelfHeight = ph;

                let fitsNew = (curX + pw + kerf <= sheetWidth - margin) && (curY + ph + kerf <= sheetHeight - margin);
                if (!fitsNew && (curX + ph + kerf <= sheetWidth - margin) && (curY + pw + kerf <= sheetHeight - margin)) {
                    const tmp = pw; pw = ph; ph = tmp;
                    fitsNew = true;
                    shelfHeight = ph;
                }

                if (fitsNew) {
                    currentSheet.placed.push({ ...piece, cutW: pw, cutH: ph, x: curX, y: curY });
                    currentSheet.usedArea += (pw * ph);
                    curX += pw + kerf;
                } else {
                    sheets.push(currentSheet);
                    currentSheet = { width: sheetWidth, height: sheetHeight, placed: [], usedArea: 0 };
                    curX = margin; curY = margin; shelfHeight = ph;
                    currentSheet.placed.push({ ...piece, cutW: pw, cutH: ph, x: curX, y: curY });
                    currentSheet.usedArea += (pw * ph);
                    curX += pw + kerf;
                }
            }
        }

        if (currentSheet.placed.length > 0) {
            sheets.push(currentSheet);
        }
        return sheets;
    }

    function renderCurrentSheetDiagram() {
        const container = document.getElementById('cutting-sheet-container');
        if (!container) return;

        computedSheets = computeSheetLayout(currentSheetFormat.width, currentSheetFormat.height, kerfSierra, piezasData);
        if (computedSheets.length === 0) {
            container.innerHTML = '<div class="p-5 text-muted">No hay piezas registradas para optimizar cortes.</div>';
            return;
        }

        if (activeSheetIndex >= computedSheets.length) activeSheetIndex = 0;
        const sheet = computedSheets[activeSheetIndex];

        // Actualizar paginación
        const paginationLabel = document.getElementById('sheet-pagination-label');
        const navGroup = document.getElementById('sheet-nav-group');
        const headerBadge = document.getElementById('sheet-header-badge');
        const dimLabel = document.getElementById('sheet-dimension-label');

        if (paginationLabel) paginationLabel.textContent = 'Hoja ' + (activeSheetIndex + 1) + ' de ' + computedSheets.length;
        if (navGroup) navGroup.style.display = computedSheets.length > 1 ? 'inline-flex' : 'none';
        if (headerBadge) headerBadge.textContent = 'Hoja #' + (activeSheetIndex + 1);
        if (dimLabel) dimLabel.textContent = 'Plancha Melamina ' + currentSheetFormat.name + ' × 18 mm';

        // Actualizar KPIs de la hoja
        const totalAreaM2 = (sheet.width * sheet.height) / 1000000;
        const usedAreaM2 = sheet.usedArea / 1000000;
        const wasteAreaM2 = Math.max(0, totalAreaM2 - usedAreaM2);
        const efficiency = Math.min(99.0, Math.round((usedAreaM2 / totalAreaM2) * 1000) / 10);

        document.getElementById('kpi-sheet-total-area').textContent = totalAreaM2.toFixed(2) + ' m²';
        document.getElementById('kpi-sheet-used-area').textContent = usedAreaM2.toFixed(2) + ' m²';
        document.getElementById('kpi-sheet-waste-area').textContent = wasteAreaM2.toFixed(2) + ' m²';
        document.getElementById('kpi-sheet-efficiency').textContent = efficiency.toFixed(1) + '%';

        // Generar SVG interactivo a escala
        const W = sheet.width;
        const H = sheet.height;

        let piecesSvg = '';
        sheet.placed.forEach((p) => {
            const isSelected = false;
            const textY1 = p.y + (p.cutH / 2) - 8;
            const textY2 = p.y + (p.cutH / 2) + 14;
            const fontSize = Math.min(36, Math.max(16, p.cutH * 0.09));

            piecesSvg += `
                <g class="piece-cut-group" data-piece-id="${p.id}" style="cursor: pointer;">
                    <rect x="${p.x}" y="${p.y}" width="${p.cutW}" height="${p.cutH}" 
                          fill="#fef3c7" stroke="#d97706" stroke-width="3" rx="4"
                          class="piece-rect piece-rect-${p.id}">
                        <title>#${p.id} ${p.name}: ${p.cutW} × ${p.cutH} mm</title>
                    </rect>
                    <!-- Textos centrados si la pieza es suficientemente grande -->
                    ${p.cutW > 120 && p.cutH > 60 ? `
                        <text x="${p.x + p.cutW / 2}" y="${textY1}" text-anchor="middle" font-size="${fontSize}" font-weight="bold" fill="#78350f" font-family="sans-serif">
                            #${p.id} ${p.name}
                        </text>
                        <text x="${p.x + p.cutW / 2}" y="${textY2}" text-anchor="middle" font-size="${fontSize * 0.85}" fill="#92400e" font-family="sans-serif">
                            ${p.cutW} × ${p.cutH} mm
                        </text>
                    ` : `
                        <text x="${p.x + p.cutW / 2}" y="${p.y + p.cutH / 2 + 5}" text-anchor="middle" font-size="20" font-weight="bold" fill="#78350f" font-family="sans-serif">
                            #${p.id}
                        </text>
                    `}
                </g>
            `;
        });

        const svgHtml = `
            <svg viewBox="0 0 ${W} ${H}" width="100%" height="auto" style="max-height: 440px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); background: #f8fafc;" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <!-- Patrón rayado para área de retazo sobrante -->
                    <pattern id="hatchWaste" width="30" height="30" patternTransform="rotate(45 0 0)" patternUnits="userSpaceOnUse">
                        <line x1="0" y1="0" x2="0" y2="30" stroke="#cbd5e1" stroke-width="2" />
                    </pattern>
                </defs>
                
                <!-- Fondo Total del Tablero (Área de Melamina bruta) -->
                <rect x="0" y="0" width="${W}" height="${H}" fill="url(#hatchWaste)" stroke="#94a3b8" stroke-width="6" rx="6"/>

                <!-- Texto de Retazo Sobrante en zona libre -->
                <text x="${W - 30}" y="${H - 30}" text-anchor="end" font-size="42" font-weight="bold" fill="#94a3b8" opacity="0.6" font-family="sans-serif">
                    RETAZO REUTILIZABLE
                </text>

                <!-- Piezas ubicadas en el tablero -->
                ${piecesSvg}

                <!-- Cotas de borde del tablero -->
                <text x="25" y="45" font-size="34" font-weight="bold" fill="#64748b" font-family="sans-serif">${W} mm</text>
                <text x="25" y="85" font-size="28" fill="#94a3b8" font-family="sans-serif">× ${H} mm</text>
            </svg>
        `;

        container.innerHTML = svgHtml;

        // Eventos hover / click en piezas del SVG para resaltar tabla
        container.querySelectorAll('.piece-cut-group').forEach((group) => {
            const id = group.getAttribute('data-piece-id');
            const rect = group.querySelector('.piece-rect');

            group.addEventListener('mouseenter', () => {
                if (rect) rect.setAttribute('fill', '#fde68a');
                const row = document.getElementById('row-piece-' + id);
                if (row) {
                    row.classList.add('table-warning');
                }
            });

            group.addEventListener('mouseleave', () => {
                if (rect) rect.setAttribute('fill', '#fef3c7');
                const row = document.getElementById('row-piece-' + id);
                if (row) {
                    row.classList.remove('table-warning');
                }
            });

            group.addEventListener('click', () => {
                const tabDespieceBtn = document.getElementById('tab-despiece-btn');
                if (tabDespieceBtn) {
                    const bsTab = new bootstrap.Tab(tabDespieceBtn);
                    bsTab.show();
                    setTimeout(() => {
                        const row = document.getElementById('row-piece-' + id);
                        if (row) {
                            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            row.classList.add('table-warning');
                            setTimeout(() => row.classList.remove('table-warning'), 2500);
                        }
                    }, 200);
                }
            });
        });
    }

    // Botones de selector de formato de plancha
    const btnFormat2750 = document.getElementById('btn-sheet-format-2750');
    const btnFormat2440 = document.getElementById('btn-sheet-format-2440');

    if (btnFormat2750 && btnFormat2440) {
        btnFormat2750.addEventListener('click', () => {
            currentSheetFormat = { width: 2750, height: 1830, name: '2750 × 1830 mm' };
            btnFormat2750.classList.add('btn-dark', 'active');
            btnFormat2750.classList.remove('btn-outline-dark');
            btnFormat2440.classList.remove('btn-dark', 'active');
            btnFormat2440.classList.add('btn-outline-dark');
            activeSheetIndex = 0;
            renderCurrentSheetDiagram();
        });

        btnFormat2440.addEventListener('click', () => {
            currentSheetFormat = { width: 2440, height: 1830, name: '2440 × 1830 mm' };
            btnFormat2440.classList.add('btn-dark', 'active');
            btnFormat2440.classList.remove('btn-outline-dark');
            btnFormat2750.classList.remove('btn-dark', 'active');
            btnFormat2750.classList.add('btn-outline-dark');
            activeSheetIndex = 0;
            renderCurrentSheetDiagram();
        });
    }

    document.getElementById('btn-sheet-prev')?.addEventListener('click', () => {
        if (activeSheetIndex > 0) {
            activeSheetIndex--;
            renderCurrentSheetDiagram();
        }
    });

    document.getElementById('btn-sheet-next')?.addEventListener('click', () => {
        if (activeSheetIndex < computedSheets.length - 1) {
            activeSheetIndex++;
            renderCurrentSheetDiagram();
        }
    });

    // Render inicial del diagrama de corte
    renderCurrentSheetDiagram();

    // ==========================================
    // 2. VISOR 3D THREE.JS (FOTO / 3D TOGGLE)
    // ==========================================
    const btnViewPhoto = document.getElementById('btn-view-photo');
    const btnView3D = document.getElementById('btn-view-3d');
    const containerPhoto = document.getElementById('container-photo');
    const container3D = document.getElementById('container-3d-viewer');
    let threeInitialized = false;
    let scene, camera, renderer, controls, animId;
    let autoRotate = true;

    function initThreeViewer() {
        if (threeInitialized || !objetosData || objetosData.length === 0) return;
        threeInitialized = true;

        const canvasContainer = document.getElementById('three-viewer-canvas');
        if (!canvasContainer) return;

        const width = canvasContainer.clientWidth || 500;
        const height = canvasContainer.clientHeight || 440;

        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xf8fafc);

        camera = new THREE.PerspectiveCamera(45, width / height, 1, 10000);
        camera.position.set(1000, 1000, 1200);

        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.shadowMap.enabled = true;
        canvasContainer.appendChild(renderer.domElement);

        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.autoRotate = autoRotate;
        controls.autoRotateSpeed = 2.0;

        // Iluminación
        const ambient = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambient);

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.6);
        dirLight.position.set(500, 1200, 700);
        scene.add(dirLight);

        const dirLight2 = new THREE.DirectionalLight(0xffffff, 0.3);
        dirLight2.position.set(-500, 800, -700);
        scene.add(dirLight2);

        // Suelo sutil
        const grid = new THREE.GridHelper(2000, 20, 0xd1d5db, 0xe5e7eb);
        grid.position.y = 0;
        scene.add(grid);

        // Catálogo de texturas
        const textureCatalog = {
            'roble': { url: '{{ asset("textures/roble_natural.jpg") }}', fallbackColor: 0xd2a679, scale: 400 },
            'nogal': { url: '{{ asset("textures/nogal_oscuro.jpg") }}', fallbackColor: 0x5c4033, scale: 400 },
            'blanca': { url: '{{ asset("textures/melamina_blanca.jpg") }}', fallbackColor: 0xf5f5f5, scale: 500 },
            'grafito': { url: '{{ asset("textures/gris_grafito.jpg") }}', fallbackColor: 0x3a3d42, scale: 500 },
            'marmol': { url: '{{ asset("textures/marmol_calacatta.jpg") }}', fallbackColor: 0xeaecef, scale: 600 },
            'haya': { url: '{{ asset("textures/haya_madera.jpg") }}', fallbackColor: 0xd8a47f, scale: 400 }
        };

        const textureLoader = new THREE.TextureLoader();

        function createPieceMaterial(key, w, h, d) {
            if (!textureCatalog[key]) key = 'roble';
            const info = textureCatalog[key];
            const scale = info.scale || 400;
            const maxDim1 = Math.max(w, h);
            const maxDim2 = Math.max(Math.min(w, h), d);
            const rX = Math.max(1, +(maxDim1 / scale).toFixed(2));
            const rY = Math.max(1, +(maxDim2 / scale).toFixed(2));

            const mat = new THREE.MeshStandardMaterial({
                color: info.fallbackColor || 0xd2a679,
                roughness: 0.55,
                metalness: 0.05
            });

            textureLoader.load(
                info.url,
                (tex) => {
                    tex.wrapS = THREE.RepeatWrapping;
                    tex.wrapT = THREE.RepeatWrapping;
                    tex.repeat.set(rX, rY);
                    mat.color.setHex(0xffffff);
                    mat.map = tex;
                    mat.needsUpdate = true;
                    if (renderer) renderer.render(scene, camera);
                },
                undefined,
                () => {
                    mat.color.setHex(info.fallbackColor || 0xd2a679);
                    mat.needsUpdate = true;
                }
            );
            return mat;
        }

        const group = new THREE.Group();
        const box = new THREE.Box3();

        objetosData.forEach((obj) => {
            const w = obj.w || 500;
            const h = obj.h || 500;
            const d = obj.d || 18;

            const geom = new THREE.BoxGeometry(w, h, d);
            const mat = createPieceMaterial(obj.textureKey || 'roble', w, h, d);
            const mesh = new THREE.Mesh(geom, mat);

            const pos = obj.pos || [0, 0, 0];
            mesh.position.set(pos[0], pos[1], pos[2]);

            if (obj.rot) {
                mesh.rotation.set(obj.rot[0], obj.rot[1], obj.rot[2]);
            }

            mesh.castShadow = true;
            mesh.receiveShadow = true;

            // Bordes nítidos
            const edges = new THREE.EdgesGeometry(geom);
            const line = new THREE.LineSegments(edges, new THREE.LineBasicMaterial({ color: 0x1e293b, opacity: 0.3, transparent: true }));
            mesh.add(line);

            group.add(mesh);
            box.expandByObject(mesh);
        });

        scene.add(group);

        // Centrar cámara en el modelo
        const center = new THREE.Vector3();
        box.getCenter(center);
        controls.target.copy(center);

        const size = new THREE.Vector3();
        box.getSize(size);
        const maxDim = Math.max(size.x, size.y, size.z);
        camera.position.set(center.x + maxDim * 1.2, center.y + maxDim * 0.9, center.z + maxDim * 1.4);
        controls.update();

        function animate() {
            animId = requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', () => {
            if (!container3D || container3D.style.display === 'none') return;
            const nw = canvasContainer.clientWidth;
            const nh = canvasContainer.clientHeight;
            if (nw && nh) {
                camera.aspect = nw / nh;
                camera.updateProjectionMatrix();
                renderer.setSize(nw, nh);
            }
        });
    }

    if (btnViewPhoto && btnView3D && containerPhoto && container3D) {
        btnViewPhoto.addEventListener('click', () => {
            btnViewPhoto.classList.add('active', 'btn-secondary');
            btnViewPhoto.classList.remove('btn-outline-secondary');
            btnView3D.classList.remove('active', 'btn-warning');
            btnView3D.classList.add('btn-outline-warning');
            containerPhoto.style.display = 'flex';
            container3D.style.display = 'none';
        });

        btnView3D.addEventListener('click', () => {
            btnView3D.classList.add('active', 'btn-warning');
            btnView3D.classList.remove('btn-outline-warning');
            btnViewPhoto.classList.remove('active', 'btn-secondary');
            btnViewPhoto.classList.add('btn-outline-secondary');
            containerPhoto.style.display = 'none';
            container3D.style.display = 'block';

            if (!threeInitialized) {
                initThreeViewer();
            } else if (renderer && camera) {
                const canvasContainer = document.getElementById('three-viewer-canvas');
                renderer.setSize(canvasContainer.clientWidth, canvasContainer.clientHeight);
                camera.aspect = canvasContainer.clientWidth / canvasContainer.clientHeight;
                camera.updateProjectionMatrix();
            }
        });

        document.getElementById('btn-toggle-auto-rot')?.addEventListener('click', () => {
            if (controls) {
                controls.autoRotate = !controls.autoRotate;
            }
        });
    }

    // ==========================================
    // 3. IMPRESIÓN COMPLETA DE FICHA Y PLANOS PDF
    // ==========================================
    window.imprimirPlanoMueble = function() {
        const win = window.open('', '_blank');
        if (!win) {
            alert("Por favor habilita las ventanas emergentes en tu navegador para imprimir la ficha técnica.");
            return;
        }

        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Ficha Técnica y Despiece - ${muebleNombre}</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; padding: 25px; color: #1f2937; max-width: 880px; margin: auto; }
                    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #d97706; padding-bottom: 12px; margin-bottom: 18px; }
                    h1 { color: #111827; margin: 0 0 4px 0; font-size: 22px; }
                    .brand { color: #d97706; font-weight: 800; font-size: 18px; letter-spacing: 0.5px; }
                    .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; background: #f9fafb; padding: 12px 16px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px; font-size: 12px; }
                    .section-title { font-size: 14px; font-weight: bold; text-transform: uppercase; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin: 20px 0 10px 0; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 12px; }
                    th, td { border: 1px solid #e5e7eb; padding: 6px 10px; text-align: left; }
                    th { background: #f3f4f6; color: #374151; font-weight: 700; }
                    .footer-note { background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px; border-radius: 6px; font-size: 11px; margin-top: 25px; line-height: 1.5; }
                    .tools-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 11px; margin-bottom: 15px; }
                    .tool-item { background: #f9fafb; border: 1px solid #e5e7eb; padding: 6px 10px; border-radius: 6px; }
                    @media print { .no-print { display: none; } body { padding: 0; } }
                </style>
            </head>
            <body>
                <div class="no-print" style="margin-bottom: 20px; text-align: right;">
                    <button onclick="window.print()" style="background:#d97706; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-weight:bold; cursor:pointer; font-size:14px;">Imprimir / Guardar en PDF</button>
                </div>
                <div class="header">
                    <div>
                        <div class="brand">MYDO-MAR · SISTEMA DE CARPINTERÍA Y DISEÑO 3D</div>
                        <h1>${muebleNombre}</h1>
                        <div style="font-size: 12px; color: #6b7280;">Ficha Técnica Oficial, Esquema de Cortes, Tornillería y Herramientas</div>
                    </div>
                    <div style="text-align: right; font-size: 12px; line-height: 1.5;">
                        <strong>Precio Estimado:</strong> Bs ${mueblePrecio}<br>
                        <strong>Fecha Emisión:</strong> ${new Date().toLocaleDateString()}<br>
                        <strong>Diseñador:</strong> ${maestroNombre}
                    </div>
                </div>

                <div class="meta-grid">
                    <div>
                        <strong>Dimensiones Bounding:</strong> ${muebleDimensiones}<br>
                        <strong>Material Sugerido:</strong> ${muebleMaterial}<br>
                        <strong>Formato de Tablero:</strong> 2750 × 1830 mm (Espesor 18 mm)
                    </div>
                    <div style="text-align: right;">
                        <strong>Total de Piezas:</strong> ${piezasData ? piezasData.length : 0} unidades<br>
                        <strong>Sierra de Corte (Kerf):</strong> 3.0 mm estándar<br>
                        <strong>Tapacanto Estimado:</strong> ~${tapacantoMts} metros lineales
                    </div>
                </div>

                <div class="section-title">1. Lista de Despiece para Corte</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35px;">#</th>
                            <th>Nombre de Pieza</th>
                            <th>Largo / Alto</th>
                            <th>Ancho</th>
                            <th>Espesor</th>
                            <th>Cant.</th>
                            <th>Orientación</th>
                            <th>Tapacanto</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${piezasData && piezasData.length > 0 ? piezasData.map((p, i) => `
                            <tr>
                                <td>${i + 1}</td>
                                <td><strong>${p.name || 'Pieza'}</strong></td>
                                <td>${Math.round(p.pw || 0)} mm</td>
                                <td>${Math.round(p.ph || 0)} mm</td>
                                <td>${p.thickness || 18} mm</td>
                                <td>1 ud.</td>
                                <td>Longitudinal (Veta)</td>
                                <td>PVC 0.45 mm</td>
                            </tr>
                        `).join('') : '<tr><td colspan="8" style="text-align:center;">No hay piezas registradas</td></tr>'}
                    </tbody>
                </table>

                <div class="section-title">2. Tornillería e Insumos de Fijación</div>
                <table>
                    <thead>
                        <tr>
                            <th>Insumo</th>
                            <th>Medida / Tipo</th>
                            <th style="text-align: center;">Cantidad</th>
                            <th>Función Principal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Tornillos Soberbios</strong></td>
                            <td>4×50 mm cincado cabeza PH2 avellanada</td>
                            <td style="text-align:center; font-weight:bold;">${soberbiosQty} uds.</td>
                            <td>Unión estructural en tableros de 18 mm</td>
                        </tr>
                        <tr>
                            <td><strong>Tornillos Madera Spax</strong></td>
                            <td>3.5×16 mm punta fina</td>
                            <td style="text-align:center; font-weight:bold;">${tornillosChicosQty} uds.</td>
                            <td>Fijación de bisagras, correderas y fondos</td>
                        </tr>
                        <tr>
                            <td><strong>Sistema Minifix</strong></td>
                            <td>Caja 15 mm + Perno 34 mm</td>
                            <td style="text-align:center; font-weight:bold;">${minifixQty} sets</td>
                            <td>Ensamble desarmable interior sin tornillos vistos</td>
                        </tr>
                        <tr>
                            <td><strong>Tarugos de Madera</strong></td>
                            <td>8×30 mm estriados</td>
                            <td style="text-align:center; font-weight:bold;">${tarugosQty} uds.</td>
                            <td>Guía y alineación contra esfuerzo de corte</td>
                        </tr>
                        <tr>
                            <td><strong>Tapacanto PVC</strong></td>
                            <td>22×0.45 mm al tono de melamina</td>
                            <td style="text-align:center; font-weight:bold;">~${tapacantoMts} m</td>
                            <td>Sellado perimetral contra humedad</td>
                        </tr>
                        <tr>
                            <td><strong>Regatones Deslizadores</strong></td>
                            <td>Diámetro 15 mm clavables</td>
                            <td style="text-align:center; font-weight:bold;">4 uds.</td>
                            <td>Aislamiento de humedad de suelo (5 mm)</td>
                        </tr>
                    </tbody>
                </table>

                <div class="section-title">3. Herramientas Recomendadas de Taller</div>
                <div class="tools-grid">
                    <div class="tool-item"><strong>1. Taladro / Atornillador:</strong> 12V/18V con embrague de torque posiciones 5 a 8.</div>
                    <div class="tool-item"><strong>2. Broca Avellanadora:</strong> Diámetro 4 mm para avellanar a ras sin quebrar melamina.</div>
                    <div class="tool-item"><strong>3. Punta Atornillar PH2:</strong> Punta imantada para no dañar ranuras.</div>
                    <div class="tool-item"><strong>4. Broca Forstner 35 / 15mm:</strong> Para cazoletas de bisagra y cajas minifix.</div>
                    <div class="tool-item"><strong>5. Escuadra Metálica 300mm:</strong> Verificación de 90° y control de diagonales.</div>
                    <div class="tool-item"><strong>6. Martillo de Goma Blanco:</strong> Ensamble y golpeo de tarugos sin marcar.</div>
                    <div class="tool-item"><strong>7. Huincha Métrica 5m:</strong> Medición en milímetros exactos.</div>
                    <div class="tool-item"><strong>8. Prensas Esquineras 90°:</strong> Sujeción firme durante el atornillado.</div>
                </div>

                <div class="footer-note">
                    <strong>Recomendaciones de Taller y Calidad Mydo-mar:</strong><br>
                    1. Realizar primer corte de despunte perimetral de 10mm en el tablero para sanear bordes de fábrica.<br>
                    2. Respetar siempre el espesor del disco seccionador (3.0 mm kerf) en cada corte de sierra.<br>
                    3. Avellanar suavemente cada perforación para que la cabeza del tornillo no deforme ni rompa el folio melamínico.<br>
                    4. Verificar la escuadra midiendo las diagonales cruzadas (ambas medidas deben ser exactamente idénticas).
                </div>
            </body>
            </html>
        `);
        win.document.close();
    };
</script>
@endsection

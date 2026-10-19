@extends('layouts.app')

@section('content')
@php
    $estructura = null;
    if (!empty($mueble->estructura_json)) {
        $estructura = json_decode($mueble->estructura_json, true);
    }
    $piezas = $estructura['piezas'] ?? [];
    $objetos = $estructura['objetos'] ?? [];
@endphp

<div class="container-fluid px-lg-5 py-4">

    <!-- Encabezado de la Guía de Armado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('catalogo.show', $mueble->id) }}" class="btn btn-outline-secondary btn-sm fw-bold px-3 py-1 rounded-pill">
                    ← Volver a la Ficha
                </a>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    Manual de Taller Interactivo
                </span>
            </div>
            <h1 class="fw-bold text-dark mb-0 fs-3">
                Guía de Armado Paso a Paso: <span class="text-primary">{{ $mueble->nombre }}</span>
            </h1>
            <p class="text-muted small mb-0 mt-1">
                Dimensiones: <strong>{{ $mueble->dimensiones ?? '1200 × 600 × 750 mm' }}</strong> · Material: <strong>{{ $mueble->material ?? 'Melamina 18mm' }}</strong> · Creado por: <strong>Maestro {{ $mueble->user->nombre ?? 'Carpintero' }}</strong>
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-dark fw-bold btn-sm px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Imprimir Manual
            </button>
            @if($linkWhatsapp)
                <a href="{{ $linkWhatsapp }}" target="_blank" class="btn btn-success fw-bold btn-sm px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    Ayuda del Maestro
                </a>
            @endif
        </div>
    </div>

    <!-- Barra de Resumen de Herramientas y Herrajes Requeridos -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-uppercase text-muted fw-bold small d-block" style="font-size:0.7rem;">Tiempo Estimado</span>
                <span class="fs-5 fw-bold text-dark">45 a 60 min</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-uppercase text-muted fw-bold small d-block" style="font-size:0.7rem;">Dificultad</span>
                <span class="fs-5 fw-bold text-success">Fácil · Guiado</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-uppercase text-muted fw-bold small d-block" style="font-size:0.7rem;">Total Pasos</span>
                <span class="fs-5 fw-bold text-primary">6 Etapas Clave</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-uppercase text-muted fw-bold small d-block" style="font-size:0.7rem;">Tornillería</span>
                <span class="fs-5 fw-bold text-warning">Soberbios 50×7mm</span>
            </div>
        </div>
    </div>

    <!-- Workbench Principal Dividido -->
    <div class="row g-4 align-items-start mb-5">

        <!-- Columna Izquierda: Visor 3D Interactivo de Ensamblaje -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                
                <!-- Barra superior del lienzo 3D -->
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded">3D Interactivo</span>
                        <span class="small fw-semibold text-secondary" id="visor-step-badge">Visualizando: Etapa 1</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" id="btn-camera-iso" class="btn btn-outline-light btn-sm py-0 px-2 small" title="Vista Isométrica">Iso</button>
                        <button type="button" id="btn-camera-front" class="btn btn-outline-light btn-sm py-0 px-2 small" title="Vista Frontal">Frente</button>
                        <button type="button" id="btn-camera-top" class="btn btn-outline-light btn-sm py-0 px-2 small" title="Vista Superior">Planta</button>
                    </div>
                </div>

                <!-- Canvas Three.js -->
                <div class="position-relative" style="height: 480px; background: radial-gradient(circle at 50% 40%, #f8fafc 0%, #cbd5e1 100%);">
                    @if(!empty($objetos))
                        <div id="armado-three-canvas" style="width: 100%; height: 100%;"></div>
                    @else
                        <!-- Si el mueble no tiene estructura 3D guardada, mostramos representación visual elegante -->
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 text-center">
                            @if($mueble->imagen_url)
                                <img src="{{ asset('storage/' . $mueble->imagen_url) }}" alt="{{ $mueble->nombre }}" style="max-height: 280px; max-width: 90%; object-fit: contain;" class="rounded-3 shadow-sm mb-3">
                            @else
                                <svg width="64" height="64" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" class="mb-3"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg>
                            @endif
                            <p class="text-dark fw-bold mb-1">Diagrama Constructivo del Modelo</p>
                            <span class="text-muted small">Sigue los pasos detallados a la derecha para ensamblar el mueble a escuadra perfecta.</span>
                        </div>
                    @endif

                    <!-- Overlay de Ayuda de Navegación -->
                    <div class="position-absolute bottom-0 start-0 m-3 bg-dark text-white px-3 py-1 rounded-pill small opacity-75 d-none d-md-flex align-items-center gap-2" style="font-size: 11px;">
                        <span>Clic izquierdo para orbitar · Rueda para zoom · Clic derecho para desplazar</span>
                    </div>
                </div>

                <!-- Barra de Navegación de Pasos (Stepper Controls) -->
                <div class="card-footer bg-white p-3 border-top">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-between">
                            <button type="button" id="btn-step-prev" class="btn btn-outline-secondary fw-bold px-3 d-inline-flex align-items-center gap-1 rounded-pill">
                                ◀ Anterior
                            </button>
                            <button type="button" id="btn-step-next" class="btn btn-primary fw-bold px-4 d-inline-flex align-items-center gap-1 rounded-pill">
                                Siguiente ▶
                            </button>
                        </div>

                        <!-- Selector directo de pasos (Puntos 1-6) -->
                        <div class="d-flex align-items-center gap-1" id="stepper-dots">
                            @for($i = 1; $i <= 6; $i++)
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold rounded-circle step-dot-btn {{ $i === 1 ? 'active btn-primary text-white' : '' }}" data-step="{{ $i }}" style="width: 32px; height: 32px; padding: 0;">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>

                        <!-- Botón Auto-Play -->
                        <button type="button" id="btn-autoplay" class="btn btn-outline-warning text-dark fw-bold btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            <span id="autoplay-label">Auto-Armado</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Columna Derecha: Tarjeta de Instrucción Activa y Lista de Pasos -->
        <div class="col-lg-5">
            
            <!-- Tarjeta Dinámica del Paso Activo -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 border-start border-5 border-primary" id="card-active-step">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold text-uppercase small" id="step-badge-num">
                        Etapa 1 de 6
                    </span>
                    <span class="text-muted small fw-semibold" id="step-progress-percent">17% completado</span>
                </div>

                <!-- Barra de Progreso -->
                <div class="progress mb-3" style="height: 6px;">
                    <div class="progress-bar bg-primary" id="step-progress-bar" role="progressbar" style="width: 17%;"></div>
                </div>

                <h3 class="fw-bold text-dark fs-4 mb-2" id="step-title">
                    Preparación, Perforaciones y Deslizadores
                </h3>
                <p class="text-secondary small lh-base mb-3" id="step-desc">
                    Desembala todas las piezas en un piso limpio o sobre cartón para no rayar los cantos de melamina. Coloca los deslizadores plásticos en la cara inferior de la base y realiza las perforaciones guía con broca de 5mm para los tornillos soberbios.
                </p>

                <!-- Piezas & Herrajes involucrados en este paso -->
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="row g-2 small">
                        <div class="col-6">
                            <strong class="text-dark d-block text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Piezas Requeridas:</strong>
                            <span class="text-muted" id="step-pieces">Base inferior, 2 Laterales</span>
                        </div>
                        <div class="col-6">
                            <strong class="text-dark d-block text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Fijación / Tornillería:</strong>
                            <span class="text-muted" id="step-hardware">4x Deslizadores, Broca 5mm</span>
                        </div>
                    </div>
                </div>

                <!-- Pro Tip del Maestro Carpintero -->
                <div class="p-3 rounded-3 border-start border-4 border-warning" style="background: #fffbeb;">
                    <div class="d-flex gap-2">
                        <div>
                            <strong class="text-dark d-block small fw-bold">Tip del Maestro Carpintero:</strong>
                            <span class="text-secondary small" id="step-tip">
                                Siempre avellana ligeramente la entrada del tornillo en la melamina para que la cabeza del tornillo quede al ras del tablero y no levante el papel melamínico.
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acordeón / Lista Completa de Pasos -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        Resumen de Pasos del Manual
                    </h6>
                    <span class="text-muted small">Haz clic para saltar</span>
                </div>
                <div class="list-group list-group-flush small" id="steps-list-group">
                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 active" data-step="1">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 24px; height: 24px;">1</span>
                        <div class="flex-grow-1 text-truncate">
                            <strong class="d-block text-truncate">Preparación y Deslizadores</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Acondicionamiento de tableros base</span>
                        </div>
                        <span class="badge bg-light text-dark border">Inicio</span>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3" data-step="2">
                        <span class="badge bg-secondary rounded-circle p-2" style="width: 24px; height: 24px;">2</span>
                        <div class="flex-grow-1 text-truncate">
                            <strong class="d-block text-truncate">Ensamble de Laterales y Base</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Fijación en L a 90 grados</span>
                        </div>
                        <span class="badge bg-light text-dark border">Estructura</span>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3" data-step="3">
                        <span class="badge bg-secondary rounded-circle p-2" style="width: 24px; height: 24px;">3</span>
                        <div class="flex-grow-1 text-truncate">
                            <strong class="d-block text-truncate">Instalación del Techo / Cubierta</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Cierre perimetral del cajón</span>
                        </div>
                        <span class="badge bg-light text-dark border">Cajón</span>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3" data-step="4">
                        <span class="badge bg-secondary rounded-circle p-2" style="width: 24px; height: 24px;">4</span>
                        <div class="flex-grow-1 text-truncate">
                            <strong class="d-block text-truncate">Parante Central y Divisiones</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Montaje de tabiques interiores</span>
                        </div>
                        <span class="badge bg-light text-dark border">Interior</span>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3" data-step="5">
                        <span class="badge bg-secondary rounded-circle p-2" style="width: 24px; height: 24px;">5</span>
                        <div class="flex-grow-1 text-truncate">
                            <strong class="d-block text-truncate">Escuadrado y Fijación del Fondo</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Comprobación de diagonales (MDF 3mm)</span>
                        </div>
                        <span class="badge bg-light text-dark border">Rigidez</span>
                    </button>

                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3" data-step="6">
                        <span class="badge bg-secondary rounded-circle p-2" style="width: 24px; height: 24px;">6</span>
                        <div class="flex-grow-1 text-truncate">
                            <strong class="d-block text-truncate">Estantes, Herrajes y Acabados</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Bisagras, repisas y tapatornillos</span>
                        </div>
                        <span class="badge bg-light text-dark border">Final</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- SECCIÓN INFERIOR: Lista de Herramientas Recomendadas y Consejos de Seguridad -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Herramientas de Taller Necesarias
                </h5>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-secondary">
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <strong>Atornillador eléctrico / Taladro</strong> con punta Phillips (PH2) y embrague regulable.
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <strong>Broca de 5mm con avellanador</strong> para tornillos soberbios de melamina.
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <strong>Escuadra metálica de carpintero (30 cm)</strong> para asegurar esquinas a 90°.
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <strong>Flexómetro / Huincha métrica</strong> para verificar igualdad de diagonales.
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <strong>Martillo de goma</strong> para asentar tarugos de madera sin marcar la melamina.
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 border-start border-4 border-warning">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Recomendaciones de Seguridad y Durabilidad
                </h5>
                <p class="text-secondary small lh-base mb-2">
                    <strong>1. Cuidado con el apriete:</strong> No sobretorquees los tornillos soberbios; la melamina puede perder su agarre interno si se gira en falso.
                </p>
                <p class="text-secondary small lh-base mb-2">
                    <strong>2. Aislamiento de piso:</strong> Nunca apoyes el canto de melamina directamente sobre pisos que puedan ser trapeados con agua; coloca siempre los deslizadores plásticos de 5mm.
                </p>
                <p class="text-secondary small lh-base mb-0">
                    <strong>3. Fijación a la pared:</strong> Para muebles altos (mayores a 1200 mm), fija la parte superior a la pared mediante un soporte en L para evitar vuelcos accidentales.
                </p>
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
    // Datos del Manual de Armado
    const stepsData = [
        {
            num: 1,
            title: "Preparación, Perforaciones y Deslizadores",
            desc: "Desembala todas las piezas en un piso limpio o sobre cartón para no rayar los cantos de melamina. Coloca los deslizadores plásticos en la cara inferior de la base y realiza las perforaciones guía con broca de 5mm para los tornillos soberbios.",
            pieces: "Base inferior, 2 Laterales exteriores",
            hardware: "4x Deslizadores de piso, Broca 5mm con avellanador",
            tip: "Siempre avellana ligeramente la entrada del tornillo en la melamina para que la cabeza quede al ras del tablero y no levante el papel melamínico.",
            progress: "17%",
            stage: 1
        },
        {
            num: 2,
            title: "Ensamble de Laterales y Base Inferior",
            desc: "Presenta el Lateral Izquierdo a 90° sobre la Base inferior. Atornilla desde la parte externa hacia el canto de la base utilizando tornillos soberbios 50×7mm a 50mm de cada extremo. Repite con el Lateral Derecho.",
            pieces: "Base inferior (1x), Lateral Izquierdo (1x), Lateral Derecho (1x)",
            hardware: "4x Tornillos Soberbios 50×7mm, Llave Allen / Punta Allen 4mm",
            tip: "Aprieta los tornillos a un 90% inicialmente. Mide con la escuadra en las esquinas internas y luego termina de dar el apriete final para no forzar la melamina.",
            progress: "33%",
            stage: 2
        },
        {
            num: 3,
            title: "Instalación del Techo / Cubierta Superior",
            desc: "Coloca la cubierta superior cerrando el cajón perimetral. Atornilla a través de los laterales hacia el canto del techo para consolidar la estructura del mueble.",
            pieces: "Techo / Tapa Superior (1x)",
            hardware: "4x Tornillos Soberbios 50×7mm",
            tip: "Si vas a colocar luz LED superior o molduras decorativas, este es el momento ideal para pasar los cables antes de fijar el fondo.",
            progress: "50%",
            stage: 3
        },
        {
            num: 4,
            title: "Montaje de Parante Central y Divisiones",
            desc: "Inserta el parante vertical divisorio en la posición calculada. Fíjalo atornillando desde la cara exterior de la base inferior y desde el techo para separar los módulos de almacenaje.",
            pieces: "Parante vertical intermedio (1x)",
            hardware: "4x Tornillos Soberbios 50×7mm",
            tip: "Mide con flexómetro la distancia exacta en la base y en el techo para asegurar que el parante quede perfectamente paralelo a los laterales.",
            progress: "67%",
            stage: 4
        },
        {
            num: 5,
            title: "Escuadrado y Fijación del Fondo (MDF 3mm)",
            desc: "¡Paso crítico! Mide con la huincha las dos diagonales en 'X' del mueble. Ajusta la inclinación hasta que ambas diagonales midan exactamente lo mismo (escuadra perfecta). Clava o atornilla el fondo perimetralmente cada 15 cm.",
            pieces: "Tablero de Fondo Durolac / MDF 3mm",
            hardware: "Clavos de 1 pulgada con arandela o Tornillos 3.5×15mm",
            tip: "El fondo no es solo estético; es el elemento que le da la rigidez estructural a todo el mueble para que nunca se tuerza ni tambalee.",
            progress: "84%",
            stage: 5
        },
        {
            num: 6,
            title: "Repisas Interiores, Herrajes y Tapatornillos",
            desc: "Coloca los soportes de estantes e introduce las repisas intermedias. Instala las bisagras de cazoleta de 35mm en las puertas, calibra la luz de separación y pega los adhesivos tapatornillos del mismo tono de la melamina.",
            pieces: "Repisas regulables, Puertas / Frentes",
            hardware: "Soportes de repisas, Bisagras cazoleta 35mm, Tapatornillos adhesivos",
            tip: "Usa el tornillo de regulación central de la bisagra para alinear milimétricamente las puertas y lograr una separación uniforme de 2mm.",
            progress: "100%",
            stage: 6
        }
    ];

    let currentStep = 1;
    let autoplayInterval = null;
    const totalSteps = 6;

    // Elementos DOM de Instrucciones
    const stepBadgeNum = document.getElementById('step-badge-num');
    const stepProgressPercent = document.getElementById('step-progress-percent');
    const stepProgressBar = document.getElementById('step-progress-bar');
    const stepTitle = document.getElementById('step-title');
    const stepDesc = document.getElementById('step-desc');
    const stepPieces = document.getElementById('step-pieces');
    const stepHardware = document.getElementById('step-hardware');
    const stepTip = document.getElementById('step-tip');
    const visorStepBadge = document.getElementById('visor-step-badge');
    const stepsListGroup = document.getElementById('steps-list-group');
    const stepperDots = document.getElementById('stepper-dots');

    function updateStepView(stepNum) {
        currentStep = Math.max(1, Math.min(totalSteps, stepNum));
        const data = stepsData[currentStep - 1];

        // Actualizar textos
        stepBadgeNum.textContent = `Etapa ${data.num} de ${totalSteps}`;
        stepProgressPercent.textContent = `${data.progress} completado`;
        stepProgressBar.style.width = data.progress;
        stepTitle.textContent = data.title;
        stepDesc.textContent = data.desc;
        stepPieces.textContent = data.pieces;
        stepHardware.textContent = data.hardware;
        stepTip.textContent = data.tip;
        visorStepBadge.textContent = `Visualizando: Etapa ${data.num} - ${data.title}`;

        // Actualizar dots
        document.querySelectorAll('.step-dot-btn').forEach(btn => {
            const s = parseInt(btn.getAttribute('data-step'));
            if (s === currentStep) {
                btn.className = 'btn btn-sm btn-primary text-white fw-bold rounded-circle step-dot-btn active';
            } else if (s < currentStep) {
                btn.className = 'btn btn-sm btn-success text-white fw-bold rounded-circle step-dot-btn';
            } else {
                btn.className = 'btn btn-sm btn-outline-secondary fw-bold rounded-circle step-dot-btn';
            }
        });

        // Actualizar lista vertical
        document.querySelectorAll('#steps-list-group button').forEach(btn => {
            const s = parseInt(btn.getAttribute('data-step'));
            if (s === currentStep) {
                btn.classList.add('active');
                btn.querySelector('.badge').className = 'badge bg-primary rounded-circle p-2';
            } else {
                btn.classList.remove('active');
                if (s < currentStep) {
                    btn.querySelector('.badge').className = 'badge bg-success rounded-circle p-2';
                } else {
                    btn.querySelector('.badge').className = 'badge bg-secondary rounded-circle p-2';
                }
            }
        });

        // Actualizar escena 3D si existe
        if (typeof update3DAssemblyStage === 'function') {
            update3DAssemblyStage(currentStep);
        }
    }

    // Botones Siguiente / Anterior
    document.getElementById('btn-step-prev').addEventListener('click', () => {
        stopAutoplay();
        updateStepView(currentStep - 1);
    });

    document.getElementById('btn-step-next').addEventListener('click', () => {
        stopAutoplay();
        updateStepView(currentStep + 1);
    });

    // Clic en dots y lista
    document.querySelectorAll('.step-dot-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            stopAutoplay();
            updateStepView(parseInt(btn.getAttribute('data-step')));
        });
    });

    document.querySelectorAll('#steps-list-group button').forEach(btn => {
        btn.addEventListener('click', () => {
            stopAutoplay();
            updateStepView(parseInt(btn.getAttribute('data-step')));
        });
    });

    // Auto-Play
    const btnAutoplay = document.getElementById('btn-autoplay');
    const autoplayLabel = document.getElementById('autoplay-label');

    function stopAutoplay() {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
            autoplayInterval = null;
            btnAutoplay.classList.remove('btn-warning', 'text-dark');
            btnAutoplay.classList.add('btn-outline-warning', 'text-dark');
            autoplayLabel.textContent = 'Auto-Armado';
        }
    }

    btnAutoplay.addEventListener('click', () => {
        if (autoplayInterval) {
            stopAutoplay();
        } else {
            btnAutoplay.classList.remove('btn-outline-warning');
            btnAutoplay.classList.add('btn-warning', 'text-dark');
            autoplayLabel.textContent = 'Pausar';
            if (currentStep >= totalSteps) currentStep = 0;
            autoplayInterval = setInterval(() => {
                if (currentStep < totalSteps) {
                    updateStepView(currentStep + 1);
                } else {
                    stopAutoplay();
                }
            }, 3000);
        }
    });

    // ==========================================
    // MOTOR 3D THREE.JS PARA EL ENSAMBLE
    // ==========================================
    const objetosData = @json($objetos);
    let scene, camera, renderer, controls, animId;
    let meshList = [];

    if (objetosData && objetosData.length > 0) {
        const canvasContainer = document.getElementById('armado-three-canvas');
        if (canvasContainer) {
            const width = canvasContainer.clientWidth || 500;
            const height = canvasContainer.clientHeight || 480;

            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xf1f5f9);

            camera = new THREE.PerspectiveCamera(45, width / height, 1, 10000);
            camera.position.set(1100, 900, 1300);

            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(width, height);
            renderer.setPixelRatio(window.devicePixelRatio);
            renderer.shadowMap.enabled = true;
            canvasContainer.appendChild(renderer.domElement);

            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;

            // Iluminación
            const ambient = new THREE.AmbientLight(0xffffff, 0.75);
            scene.add(ambient);
            const dirLight = new THREE.DirectionalLight(0xffffff, 0.6);
            dirLight.position.set(600, 1200, 800);
            scene.add(dirLight);
            const dirLight2 = new THREE.DirectionalLight(0xffffff, 0.35);
            dirLight2.position.set(-600, 600, -800);
            scene.add(dirLight2);

            // Suelo con rejilla suave
            const grid = new THREE.GridHelper(2000, 20, 0x94a3b8, 0xe2e8f0);
            scene.add(grid);

            // Texturas
            const textureCatalog = {
                'roble': '{{ asset("textures/roble_natural.jpg") }}',
                'nogal': '{{ asset("textures/nogal_oscuro.jpg") }}',
                'blanca': '{{ asset("textures/melamina_blanca.jpg") }}',
                'grafito': '{{ asset("textures/gris_grafito.jpg") }}',
                'marmol': '{{ asset("textures/marmol_calacatta.jpg") }}',
                'haya': '{{ asset("textures/haya_madera.jpg") }}'
            };
            const textureLoader = new THREE.TextureLoader();
            const edgeMat = new THREE.LineBasicMaterial({ color: 0x1f2937, linewidth: 1 });
            const highlightMat = new THREE.MeshStandardMaterial({ color: 0xfbbf24, roughness: 0.3, metalness: 0.2 });

            const group = new THREE.Group();

            // Calcular orden secuencial de piezas para el ensamble
            // Clasificamos cada pieza en etapa de 1 a 6
            const totalPiecesCount = objetosData.length;

            objetosData.forEach((o, idx) => {
                const w = o.w || 100;
                const h = o.h || 100;
                const d = o.d || 100;
                const geom = new THREE.BoxGeometry(w, h, d);
                const texUrl = textureCatalog[o.textureKey || 'roble'] || textureCatalog['roble'];
                const tex = textureLoader.load(texUrl);
                tex.wrapS = THREE.RepeatWrapping;
                tex.wrapT = THREE.RepeatWrapping;
                tex.repeat.set(Math.max(1, +(w/400).toFixed(1)), Math.max(1, +(h/400).toFixed(1)));

                const mat = new THREE.MeshStandardMaterial({
                    map: tex,
                    roughness: 0.55,
                    metalness: 0.05
                });

                const mesh = new THREE.Mesh(geom, mat);
                if (o.pos) mesh.position.set(o.pos[0], o.pos[1], o.pos[2]);
                if (o.rot) mesh.rotation.set(o.rot[0], o.rot[1], o.rot[2]);

                const edges = new THREE.EdgesGeometry(geom);
                const line = new THREE.LineSegments(edges, edgeMat);
                mesh.add(line);

                // Determinar etapa de ensamble según posición / índice
                let stage = 1;
                const ratio = idx / Math.max(1, totalPiecesCount - 1);
                if (ratio < 0.2) stage = 1;       // Base y primeros laterales
                else if (ratio < 0.4) stage = 2;  // Marco lateral
                else if (ratio < 0.6) stage = 3;  // Techo
                else if (ratio < 0.75) stage = 4; // Parante
                else if (ratio < 0.9) stage = 5;  // Fondo
                else stage = 6;                   // Repisas y accesorios

                mesh.userData = {
                    stage: stage,
                    originalPos: mesh.position.clone(),
                    originalMat: mat,
                    name: o.name || `Pieza ${idx + 1}`
                };

                group.add(mesh);
                meshList.push(mesh);
            });

            scene.add(group);

            // Centrar cámara
            const box = new THREE.Box3().setFromObject(group);
            const center = new THREE.Vector3();
            box.getCenter(center);
            controls.target.copy(center);

            const size = new THREE.Vector3();
            box.getSize(size);
            const maxDim = Math.max(size.x, size.y, size.z);
            camera.position.set(center.x + maxDim * 1.3, center.y + maxDim * 0.9, center.z + maxDim * 1.4);
            controls.update();

            // Función para actualizar visibilidad y resaltado según etapa activa
            window.update3DAssemblyStage = function(stageNum) {
                meshList.forEach(m => {
                    if (m.userData.stage <= stageNum) {
                        m.visible = true;
                        if (m.userData.stage === stageNum) {
                            // Resaltar piezas agregadas en esta etapa
                            m.children[0].material.color.setHex(0xf59e0b); // Borde dorado
                            m.children[0].material.linewidth = 2;
                        } else {
                            m.children[0].material.color.setHex(0x1f2937); // Borde normal
                        }
                    } else {
                        // Piezas futuras no se muestran o modo fantasma
                        m.visible = false;
                    }
                });
            };

            // Vistas predefinidas
            document.getElementById('btn-camera-iso')?.addEventListener('click', () => {
                camera.position.set(center.x + maxDim * 1.3, center.y + maxDim * 0.9, center.z + maxDim * 1.4);
                controls.target.copy(center);
                controls.update();
            });

            document.getElementById('btn-camera-front')?.addEventListener('click', () => {
                camera.position.set(center.x, center.y, center.z + maxDim * 2.2);
                controls.target.copy(center);
                controls.update();
            });

            document.getElementById('btn-camera-top')?.addEventListener('click', () => {
                camera.position.set(center.x, center.y + maxDim * 2.5, center.z + 1);
                controls.target.copy(center);
                controls.update();
            });

            function animate() {
                animId = requestAnimationFrame(animate);
                controls.update();
                renderer.render(scene, camera);
            }
            animate();

            // Iniciar en etapa 1
            update3DAssemblyStage(1);

            window.addEventListener('resize', () => {
                const nw = canvasContainer.clientWidth;
                const nh = canvasContainer.clientHeight;
                if (nw && nh) {
                    camera.aspect = nw / nh;
                    camera.updateProjectionMatrix();
                    renderer.setSize(nw, nh);
                }
            });
        }
    }
</script>
@endsection

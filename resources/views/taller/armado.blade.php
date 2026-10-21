@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- 1. HEADER TERMINAL DE ARMADO (Estilo Oficial Mydo-mar) -->
    <div class="card bg-dark text-white border-0 rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill">
                        Terminal de Taller
                    </span>
                    <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill font-monospace">
                        Orden #ORD-{{ str_pad($diseno->id_diseno_muebles, 4, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="badge bg-light text-dark px-3 py-1.5 rounded-pill font-monospace small">
                        Planta Madeart
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-white">{{ $diseno->nombre_diseno_muebles }}</h2>
                <p class="text-secondary mb-0 small">
                    <strong>Categoría:</strong> {{ $diseno->categoria->nombre_categoria_muebles ?? 'General' }} &nbsp;•&nbsp;
                    <strong>Dificultad:</strong> {{ $diseno->nivel->nombre_nivel_muebles ?? 'Intermedio' }} &nbsp;•&nbsp;
                    <strong>Sellado Temporal:</strong> <span class="font-monospace text-warning">{{ $diseno->id_dim_tiempo_publicacion }}</span>
                </p>
            </div>
            
            <div class="d-flex flex-column align-items-md-end gap-2">
                {{-- Avance de Montaje --}}
                <div class="text-md-end">
                    <div class="small text-secondary mb-1">Avance de Montaje:</div>
                    <span class="fs-4 fw-bold {{ $progreso->completado_muebles ? 'text-success' : 'text-warning' }}" id="progresoTexto">
                        {{ $progreso->porcentaje_progreso_muebles }}%
                    </span>
                    <div class="progress bg-secondary bg-opacity-25 mt-1" style="width: 180px; height: 8px; border-radius: 6px;">
                        <div class="progress-bar {{ $progreso->completado_muebles ? 'bg-success' : 'bg-warning' }}" id="barraProgreso" role="progressbar" style="width: {{ $progreso->porcentaje_progreso_muebles }}%;"></div>
                    </div>
                </div>

                {{-- Botón de Descarga PDF --}}
                <a id="btn-descargar-plano-pdf"
                   href="{{ route('taller.plano-pdf', $diseno->id_diseno_muebles) }}"
                   class="btn btn-warning fw-bold text-dark shadow-sm px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2 mt-1"
                   title="Descargar plano técnico oficial para el taller de producción">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 16l-4-4h3V4h2v8h3l-4 4z"/><rect x="3" y="18" width="18" height="2" rx="1"/>
                    </svg>
                    Descargar Plano Técnico (PDF)
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
            <svg width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill text-success flex-shrink-0" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <div class="fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Columna Izquierda: Secuencia de Ensamblaje Paso a Paso (7 cols) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Secuencia de Ensamblaje Paso a Paso</h5>
                        <p class="text-muted small mb-0">Flujo de montaje verificado para operarios de planta</p>
                    </div>
                    <span class="badge bg-light text-muted border font-monospace">muebles_pasos_ensamblaje</span>
                </div>

                @php
                    $pasos = $diseno->pasosEnsamblaje;
                    if($pasos->isEmpty()) {
                        $pasos = collect([
                            (object)['numero_paso_muebles' => 1, 'titulo_paso_muebles' => 'Preparación de tablero base y fijación de zócalo / patas de apoyo.'],
                            (object)['numero_paso_muebles' => 2, 'titulo_paso_muebles' => 'Ensamble de laterales y divisiones internas mediante unión Minifix con perno.'],
                            (object)['numero_paso_muebles' => 3, 'titulo_paso_muebles' => 'Instalación de cubierta superior, verificación de escuadras y ajuste de rigidez.'],
                        ]);
                    }
                    $pasoActual = $progreso->paso_actual_muebles ?: 1;
                @endphp

                <div class="d-flex flex-col gap-2 mb-4">
                    @foreach($pasos as $paso)
                        @php
                            $esActivo = $paso->numero_paso_muebles == $pasoActual;
                            $esCompletado = $paso->numero_paso_muebles < $pasoActual || $progreso->completado_muebles;
                        @endphp
                        <div class="p-3 mb-2 rounded-3 border transition {{ $esActivo ? 'border-start border-4 border-warning bg-warning bg-opacity-10' : ($esCompletado ? 'border-start border-4 border-success bg-light' : 'bg-light border-0') }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $esCompletado ? 'bg-success text-white' : ($esActivo ? 'bg-warning text-dark fw-bold' : 'bg-secondary') }}">
                                    Paso {{ $paso->numero_paso_muebles }} de {{ count($pasos) }}
                                    {{ $esCompletado ? '✓ Completado' : ($esActivo ? '▶ En Proceso' : '') }}
                                </span>
                                <span class="text-muted small font-monospace">Taller Madeart</span>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $paso->titulo_paso_muebles }}</h6>
                        </div>
                    @endforeach
                </div>

                <!-- Controles de Taller para el Operario -->
                <div class="bg-light p-3 rounded-3 border d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <form action="{{ route('taller.progreso', $diseno->id_diseno_muebles) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="paso_actual" value="{{ max(1, $pasoActual - 1) }}">
                        <input type="hidden" name="total_pasos" value="{{ count($pasos) }}">
                        <button type="submit" class="btn btn-outline-secondary fw-bold px-3 py-2 rounded-3" {{ $pasoActual <= 1 ? 'disabled' : '' }}>
                            ◀ Paso Anterior
                        </button>
                    </form>

                    <form action="{{ route('taller.progreso', $diseno->id_diseno_muebles) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="paso_actual" value="{{ min(count($pasos), $pasoActual + 1) }}">
                        <input type="hidden" name="total_pasos" value="{{ count($pasos) }}">
                        <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Confirmar Paso y Avanzar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Despiece Técnico y Herrajes Requeridos (5 cols) -->
        <div class="col-lg-5">
            <!-- Despiece Milimétrico de Piezas -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                <div class="card-header bg-white border-0 p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" class="text-warning text-dark" viewBox="0 0 24 24">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                        Despiece de Corte
                    </h6>
                    <span class="badge bg-light text-muted border font-monospace">{{ $diseno->piezas->count() }} Piezas</span>
                </div>
                <div class="table-responsive" style="max-height: 250px;">
                    <table class="table table-hover table-sm align-middle mb-0 font-mono small">
                        <thead class="table-light text-muted" style="font-size: 0.72rem;">
                            <tr>
                                <th class="ps-3">Pieza</th>
                                <th>Dimensiones (mm)</th>
                                <th class="pe-3 text-end">Área (m²)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diseno->piezas as $pz)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark font-sans">{{ $pz->nombre_pieza_muebles }}</td>
                                    <td class="text-dark">{{ (int)$pz->ancho_mm_muebles }} × {{ (int)$pz->alto_mm_muebles }} × {{ (int)$pz->espesor_mm_muebles }}</td>
                                    <td class="pe-3 text-end text-muted">{{ number_format($pz->metros_cuadrados, 3) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted font-sans">
                                        No hay despiece cargado para este mueble.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Insumos y Herrajes Requeridos (BOM) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-0 p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" class="text-warning text-dark" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        Insumos y Herrajes Requeridos
                    </h6>
                    <span class="badge bg-light text-muted border font-monospace">BOM</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0 font-mono small">
                        <thead class="table-light text-muted" style="font-size: 0.72rem;">
                            <tr>
                                <th class="ps-3">Herraje</th>
                                <th>Cantidad</th>
                                <th class="pe-3 text-end">Costo Est.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diseno->herrajesProyecto as $hp)
                                <tr>
                                    <td class="ps-3 font-sans text-dark fw-medium">
                                        {{ $hp->catalogoHerraje->nombre_herraje_global ?? 'Herraje' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-20 text-dark fw-bold border border-warning border-opacity-25 px-2 py-0.5 rounded-pill">
                                            {{ $hp->cantidad_necesaria_proyecto }} uds
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end text-dark font-sans fw-bold">
                                        Bs. {{ number_format($hp->subtotal_costo, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted font-sans">
                                        No se han vinculado herrajes a este proyecto.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

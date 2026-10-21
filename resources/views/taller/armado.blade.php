@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Header Terminal de Armado -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #064e3b 0%, #1e293b 100%); border-left: 5px solid #10b981 !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success px-3 py-1 rounded-pill fw-bold">Terminal de Taller</span>
                        <span class="badge bg-secondary px-3 py-1 rounded-pill">Orden #ORD-{{ str_pad($diseno->id_diseno_muebles, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="badge bg-dark text-info px-3 py-1 rounded-pill font-monospace">Madeart S.R.L.</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">{{ $diseno->nombre_diseno_muebles }}</h2>
                    <p class="text-secondary mb-0 small">
                        <strong>Categoría:</strong> {{ $diseno->categoria->nombre_categoria_muebles ?? 'General' }} &nbsp;•&nbsp;
                        <strong>Nivel de Dificultad:</strong> {{ $diseno->nivel->nombre_nivel_muebles ?? 'Intermedio' }} &nbsp;•&nbsp;
                        <strong>Sellado Temporal:</strong> <span class="font-monospace text-warning">{{ $diseno->id_dim_tiempo_publicacion }}</span>
                    </p>
                </div>

                <div class="d-flex flex-column align-items-end gap-2">
                    {{-- Progreso --}}
                    <div class="text-end">
                        <div class="small text-secondary mb-1">Avance de Montaje:</div>
                        <span class="fs-4 fw-bold {{ $progreso->completado_muebles ? 'text-success' : 'text-warning' }}" id="progresoTexto">
                            {{ $progreso->porcentaje_progreso_muebles }}%
                        </span>
                        <div class="progress bg-dark mt-1" style="width: 180px; height: 8px;">
                            <div class="progress-bar {{ $progreso->completado_muebles ? 'bg-success' : 'bg-warning' }}" id="barraProgreso" role="progressbar" style="width: {{ $progreso->porcentaje_progreso_muebles }}%;"></div>
                        </div>
                    </div>

                    {{-- Botón de Descarga PDF --}}
                    <a id="btn-descargar-plano-pdf"
                       href="{{ route('taller.plano-pdf', $diseno->id_diseno_muebles) }}"
                       class="btn btn-sm fw-bold d-inline-flex align-items-center gap-2 shadow-sm"
                       style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: #fff; border: none; border-radius: 8px; padding: 7px 16px; letter-spacing: 0.4px;"
                       title="Descargar plano técnico oficial para el taller de producción">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 16l-4-4h3V4h2v8h3l-4 4z"/><rect x="3" y="18" width="18" height="2" rx="1"/>
                        </svg>
                        Descargar Plano PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
            <svg width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill text-success flex-shrink-0" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        
        <!-- Columna Izquierda: Guía Interactiva Paso a Paso -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 text-white mb-4" style="background:#131c2e; border:1px solid #263859 !important;">
                <div class="card-header bg-transparent border-bottom border-secondary py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                        <span>🔨</span> Secuencia de Ensamblaje Paso a Paso
                    </h5>
                    <span class="badge bg-secondary font-monospace">muebles_pasos_ensamblaje</span>
                </div>
                
                <div class="card-body p-4">
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

                    <div class="list-group list-group-flush mb-4">
                        @foreach($pasos as $paso)
                            @php
                                $esActivo = $paso->numero_paso_muebles == $pasoActual;
                                $esCompletado = $paso->numero_paso_muebles < $pasoActual || $progreso->completado_muebles;
                            @endphp
                            <div class="list-group-item bg-transparent text-white border-secondary p-3 mb-2 rounded-3 {{ $esActivo ? 'border-success' : '' }}" 
                                 style="{{ $esActivo ? 'background: rgba(16, 185, 129, 0.1) !important; border-left: 4px solid #10b981 !important;' : 'background: #0f172a !important;' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge {{ $esCompletado ? 'bg-success' : ($esActivo ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                        Paso {{ $paso->numero_paso_muebles }} de {{ count($pasos) }}
                                        {{ $esCompletado ? '✓ Completado' : ($esActivo ? '▶ En Progreso' : '') }}
                                    </span>
                                    <span class="font-monospace small text-secondary">Registro en vivo</span>
                                </div>
                                <h6 class="fw-bold mb-1 text-light">{{ $paso->titulo_paso_muebles }}</h6>
                            </div>
                        @endforeach
                    </div>

                    <!-- Controles de Taller para el Operario -->
                    <div class="bg-dark p-3 rounded-3 border border-secondary d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <form action="{{ route('taller.progreso', $diseno->id_diseno_muebles) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="paso_actual" value="{{ max(1, $pasoActual - 1) }}">
                            <input type="hidden" name="total_pasos" value="{{ count($pasos) }}">
                            <button type="submit" class="btn btn-outline-secondary btn-sm" {{ $pasoActual <= 1 ? 'disabled' : '' }}>
                                ◀ Paso Anterior
                            </button>
                        </form>

                        <form action="{{ route('taller.progreso', $diseno->id_diseno_muebles) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="paso_actual" value="{{ min(count($pasos), $pasoActual + 1) }}">
                            <input type="hidden" name="total_pasos" value="{{ count($pasos) }}">
                            <button type="submit" class="btn btn-success fw-bold px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                Confirmar Paso y Avanzar en Base de Datos
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Columna Derecha: Despiece Técnico y Herrajes Requeridos (BOM) -->
        <div class="col-lg-5">
            
            <!-- Despiece Milimétrico -->
            <div class="card border-0 shadow-sm rounded-4 text-white mb-4" style="background:#131c2e; border:1px solid #263859 !important;">
                <div class="card-header bg-transparent border-bottom border-secondary py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                        <span>📐</span> Despiece de Corte (muebles_piezas)
                    </h6>
                    <span class="badge bg-secondary font-monospace">{{ $diseno->piezas->count() }} Piezas</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 240px;">
                        <table class="table table-dark table-sm table-hover align-middle mb-0 font-monospace small" style="background: transparent;">
                            <thead>
                                <tr class="text-secondary text-uppercase" style="border-bottom: 2px solid #263859; font-size: 0.7rem;">
                                    <th class="ps-3">Pieza</th>
                                    <th>Dimensiones (mm)</th>
                                    <th class="pe-3 text-end">Área (m²)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($diseno->piezas as $pz)
                                    <tr>
                                        <td class="ps-3 text-white fw-bold">{{ $pz->nombre_pieza_muebles }}</td>
                                        <td class="text-info">{{ (int)$pz->ancho_mm_muebles }} x {{ (int)$pz->alto_mm_muebles }} x {{ (int)$pz->espesor_mm_muebles }}</td>
                                        <td class="pe-3 text-end text-secondary">{{ number_format($pz->metros_cuadrados, 3) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-secondary">
                                            No hay despiece cargado para este mueble.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Insumos y Herrajes Requeridos (BOM) -->
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e; border:1px solid #263859 !important;">
                <div class="card-header bg-transparent border-bottom border-secondary py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                        <span>🔩</span> Lista de Herrajes BOM (Insumos)
                    </h6>
                    <span class="badge bg-secondary font-monospace">muebles_herrajes</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-sm align-middle mb-0 font-monospace small" style="background: transparent;">
                            <thead>
                                <tr class="text-secondary text-uppercase" style="border-bottom: 2px solid #263859; font-size: 0.7rem;">
                                    <th class="ps-3">Herraje</th>
                                    <th>Cantidad</th>
                                    <th class="pe-3 text-end">Costo Est.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($diseno->herrajesProyecto as $hp)
                                    <tr>
                                        <td class="ps-3 text-white">
                                            {{ $hp->catalogoHerraje->nombre_herraje_global ?? 'Herraje' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $hp->cantidad_necesaria_proyecto }} uds</span>
                                        </td>
                                        <td class="pe-3 text-end text-success">
                                            Bs. {{ number_format($hp->subtotal_costo, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-secondary">
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

</div>
@endsection

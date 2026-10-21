@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- 1. HEADER TERMINAL DE TALLER (Estilo Oficial Mydo-mar) -->
    <div class="card bg-dark text-white border-0 rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill">
                        Terminal de Taller y Fábrica
                    </span>
                    <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill">
                        Madeart Carpintería
                    </span>
                    <span class="badge bg-light text-dark px-3 py-1.5 rounded-pill font-monospace small">
                        Subrol: Armador de Muebles
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-white">Órdenes de Ensamblaje y Proyectos de Planta</h2>
                <p class="text-secondary mb-0 small">
                    Visualización técnica de muebles modulados, despiece milimétrico de tableros y guías de ensamblaje interactivas paso a paso.
                </p>
            </div>
            <div>
                <a href="{{ route('empresa.empleados.index') }}" class="btn btn-warning fw-bold text-dark shadow-sm px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-2 text-nowrap">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Panel Madeart B2B
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TARJETAS DE ESTADÍSTICAS RÁPIDAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Proyectos para Montaje</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $disenos->total() }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 9h18M9 3v18"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">En Proceso de Taller</p>
                        <h2 class="fw-bold text-dark mb-0">Activo</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Guías Técnicas PDF</p>
                        <h2 class="fw-bold text-dark mb-0">Listas</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. LISTA DE PROYECTOS DE ARMADO (Tarjetas idénticas al Catálogo Mydo-mar) -->
    <div class="row g-4">
        @forelse($disenos as $diseno)
            @php
                $progresoPct = $progresos[$diseno->id_diseno_muebles] ?? 0;
            @endphp
            <div class="col-sm-6 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white d-flex flex-column">
                    <!-- Imagen o Render 3D -->
                    <div class="position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        @if($diseno->imagen_render_url)
                            <img src="{{ asset('storage/' . $diseno->imagen_render_url) }}" alt="{{ $diseno->nombre_diseno_muebles }}" class="card-img-top w-100 h-100 object-fit-cover d-block">
                        @else
                            <div class="text-center p-3">
                                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-secondary opacity-50 mb-2">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <path d="M3 9h18M9 3v18"/>
                                </svg>
                                <div><span class="badge bg-light text-muted border font-monospace">Modelo 3D Three.js</span></div>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border">{{ $diseno->categoria->nombre_categoria_muebles ?? 'Muebles' }}</span>
                                <span class="badge bg-warning bg-opacity-15 text-dark fw-bold border border-warning border-opacity-25 font-monospace">
                                    {{ $diseno->nivel->nombre_nivel_muebles ?? 'Nivel Taller' }}
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2">{{ $diseno->nombre_diseno_muebles }}</h5>
                            <p class="text-muted small mb-3">
                                {{ Str::limit($diseno->descripcion, 90) }}
                            </p>

                            <!-- Despiece y Herrajes -->
                            <div class="bg-light p-2.5 rounded-3 mb-3 small font-monospace d-flex justify-content-between text-muted border">
                                <span>Piezas: <strong class="text-dark">{{ $diseno->piezas->count() }}</strong></span>
                                <span>Pasos: <strong class="text-dark">{{ $diseno->pasosEnsamblaje->count() ?: 3 }}</strong></span>
                                <span>Herrajes: <strong class="text-dark">{{ $diseno->herrajesProyecto->count() }} tipos</strong></span>
                            </div>

                            <!-- Barra de Progreso del Operario -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center small mb-1">
                                    <span class="text-muted">Avance de Armado:</span>
                                    <span class="fw-bold {{ $progresoPct == 100 ? 'text-success' : 'text-warning' }}">{{ $progresoPct }}%</span>
                                </div>
                                <div class="progress bg-light" style="height: 8px; border-radius: 6px;">
                                    <div class="progress-bar {{ $progresoPct == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $progresoPct }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 pt-3 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small d-block" style="font-size: 0.72rem;">Costo Estimado</span>
                                <span class="fw-bold text-dark fs-5">
                                    Bs. {{ number_format($diseno->precio_estimado_total ?: 450, 2) }}
                                </span>
                            </div>
                            <a href="{{ route('taller.armado', $diseno->id_diseno_muebles) }}" class="btn btn-warning fw-bold text-dark shadow-sm px-3 py-2 rounded-3 d-inline-flex align-items-center gap-1.5">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                </svg>
                                Iniciar Armado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm rounded-4 p-5 bg-white">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted opacity-50 mx-auto mb-3" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/>
                    </svg>
                    <h4 class="fw-bold text-dark mb-2">No hay proyectos de muebles registrados en el sistema de planta</h4>
                    <p class="text-muted mb-4">Crea un diseño desde el módulo 3D para generar automáticamente el despiece y la orden de taller.</p>
                    <div>
                        <a href="{{ route('muebles.create') }}" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Ir al Diseñador 3D Paramétrico
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($disenos->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $disenos->links() }}
        </div>
    @endif
</div>
@endsection

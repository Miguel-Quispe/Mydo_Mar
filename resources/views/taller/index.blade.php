@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Header Terminal de Taller -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); border-left: 5px solid #10b981 !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success px-3 py-1 rounded-pill fw-bold">Terminal de Taller y Fábrica</span>
                        <span class="badge bg-dark px-3 py-1 rounded-pill">Madeart Carpintería</span>
                        <span class="badge bg-secondary px-3 py-1 rounded-pill font-monospace">Subrol: Armador de Muebles</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Órdenes de Ensamblaje y Proyectos de Planta</h2>
                    <p class="text-light opacity-75 mb-0 small">
                        Visualización técnica de muebles modulados, despiece milimétrico de tableros y guías de ensamblaje interactivas paso a paso.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('empresa.empleados.index') }}" class="btn btn-outline-light fw-semibold px-3 py-2">
                        🏢 Panel Madeart B2B
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Proyectos de Armado -->
    <div class="row g-4">
        @forelse($disenos as $diseno)
            @php
                $progresoPct = $progresos[$diseno->id_diseno_muebles] ?? 0;
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-white" style="background:#131c2e; border:1px solid #263859 !important;">
                    
                    <!-- Imagen o Render 3D -->
                    <div style="height: 180px; background:#0b0f19; border-top-left-radius:14px; border-top-right-radius:14px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        @if($diseno->imagen_render_url)
                            <img src="{{ asset('storage/' . $diseno->imagen_render_url) }}" alt="{{ $diseno->nombre_diseno_muebles }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="text-center p-3">
                                <div style="font-size:3rem;">📦</div>
                                <span class="badge bg-secondary font-monospace">Modelo 3D Three.js</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info bg-opacity-25 text-info border border-info">{{ $diseno->categoria->nombre_categoria_muebles ?? 'Muebles' }}</span>
                                <span class="badge bg-secondary font-monospace">{{ $diseno->nivel->nombre_nivel_muebles ?? 'Nivel Taller' }}</span>
                            </div>

                            <h5 class="fw-bold text-white mb-2">{{ $diseno->nombre_diseno_muebles }}</h5>
                            <p class="text-secondary small mb-3">
                                {{ Str::limit($diseno->descripcion, 90) }}
                            </p>

                            <!-- Despiece y Herrajes -->
                            <div class="bg-dark p-2 rounded-3 mb-3 small font-monospace d-flex justify-content-between text-secondary">
                                <span>Piezas: <strong class="text-white">{{ $diseno->piezas->count() }}</strong></span>
                                <span>Pasos: <strong class="text-white">{{ $diseno->pasosEnsamblaje->count() ?: 3 }}</strong></span>
                                <span>Herrajes: <strong class="text-warning">{{ $diseno->herrajesProyecto->count() }} tipos</strong></span>
                            </div>

                            <!-- Barra de Progreso del Operario -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center small mb-1">
                                    <span class="text-secondary">Progreso de Armado:</span>
                                    <span class="fw-bold {{ $progresoPct == 100 ? 'text-success' : 'text-warning' }}">{{ $progresoPct }}%</span>
                                </div>
                                <div class="progress bg-dark" style="height: 6px;">
                                    <div class="progress-bar {{ $progresoPct == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $progresoPct }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 pt-3 border-top border-secondary d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success fs-5">
                                Bs. {{ number_format($diseno->precio_estimado_total ?: 450, 2) }}
                            </span>
                            <a href="{{ route('taller.armado', $diseno->id_diseno_muebles) }}" class="btn btn-sm btn-success fw-bold px-3 py-2 d-inline-flex align-items-center gap-1 shadow-sm">
                                <span>🔨</span> Iniciar / Ver Armado
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-white" style="background:#131c2e;">
                    <h4 class="fw-bold mb-2">No hay proyectos de muebles registrados en el sistema de planta</h4>
                    <p class="text-secondary mb-3">Crea un diseño desde el módulo 3D para generar automáticamente el despiece y la orden de taller.</p>
                    <div>
                        <a href="{{ route('muebles.create') }}" class="btn btn-warning fw-bold px-4 py-2">
                            🎨 Ir al Diseñador 3D Paramétrico
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($disenos->hasPages())
        <div class="mt-4">
            {{ $disenos->links() }}
        </div>
    @endif

</div>
@endsection

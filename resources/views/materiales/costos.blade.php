@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Mensajes de Estado -->
    @if(session('exito'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
            <svg width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill text-success flex-shrink-0 me-2" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <strong>¡Excelente!</strong> {{ session('exito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. BANNER OFICIAL DE COSTOS DE MATERIALES (Estilo Oficial Mydo-mar) -->
    <div class="card bg-dark text-white border-0 rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill">
                        Tarifario Oficial de Materiales
                    </span>
                    <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill">
                        Planta Madeart & Mydo-mar
                    </span>
                    <span class="badge bg-light text-dark px-3 py-1.5 rounded-pill font-monospace small">
                        Clave: {{ $user->clave_rol ?? 'MAE-001' }}
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-white">Costos de Tableros, Herrajes y Fijaciones</h2>
                <p class="text-secondary mb-0 small">
                    Control confidencial de precios de compra para hojas de melamina (18mm, 15mm, 3mm), tapacantos, herrajes de cierre suave y tornillos de armado.
                </p>
            </div>
            <div>
                <a href="{{ route('muebles.create') }}" class="btn btn-warning fw-bold text-dark shadow-sm px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-2 text-nowrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Diseñar Mueble 3D
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TARJETAS DE ESTADÍSTICAS RÁPIDAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tableros Registrados</p>
                        <h2 class="fw-bold text-dark mb-0">{{ count($materiales) }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Herrajes y Fijaciones</p>
                        <h2 class="fw-bold text-dark mb-0">{{ count($herrajes) }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Aprovechamiento Estándar</p>
                        <h2 class="fw-bold text-success mb-0">85%</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Formato Plancha Estándar</p>
                        <h4 class="fw-bold text-dark mb-0 font-monospace">2440×1830</h4>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 9h18M9 3v18"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. TABLA DE TABLEROS Y HOJAS DE MELAMINA -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-warning"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 9h18M9 3v18"></path></svg>
                <h5 class="fw-bold mb-0 text-white">Hojas de Melamina y Tableros (Planchas 2.44m × 1.83m)</h5>
            </div>
            <span class="badge bg-warning text-dark font-monospace fw-bold">MDP / MDFLAC</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Textura / Código</th>
                        <th>Nombre del Tablero</th>
                        <th>Tipo</th>
                        <th>Espesor</th>
                        <th class="text-end">Precio Plancha (S/.)</th>
                        <th class="text-end">Equiv. por m²</th>
                        <th class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materiales as $mat)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                @if(isset($mat->textura))
                                    <img src="{{ asset('textures/' . $mat->textura) }}" 
                                         alt="{{ $mat->nombre_material_muebles }}" 
                                         class="rounded border shadow-sm" 
                                         style="width: 38px; height: 38px; object-fit: cover;">
                                @else
                                    <span class="badge bg-secondary p-2 rounded">MEL</span>
                                @endif
                                <span class="font-monospace fw-bold text-secondary">MEL-0{{ $loop->iteration }}</span>
                            </div>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $mat->nombre_material_muebles }}</strong>
                            <div class="text-muted small">Tablero base para modulación y corte sin desportillado</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $mat->tipo_material_muebles ?? 'Melamina' }}</span>
                        </td>
                        <td class="font-monospace fw-bold text-dark">
                            {{ $mat->espesor_material_muebles }} mm
                        </td>
                        <td class="text-end">
                            <span class="fs-6 font-monospace fw-bold text-dark">
                                S/. {{ number_format($mat->precio_lamina_muebles, 2) }}
                            </span>
                        </td>
                        <td class="text-end font-monospace text-muted small">
                            S/. {{ number_format($mat->precio_lamina_muebles / 4.465, 2) }}/m²
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-warning text-dark fw-bold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditMaterial{{ $mat->id_material_muebles }}">
                                Editar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. TABLA DE HERRAJES, TORNILLERÍA Y SERVICIOS -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-warning"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                <h5 class="fw-bold mb-0 text-white">Herrajes Globales, Tornillos y Servicios de Taller</h5>
            </div>
            <span class="badge bg-warning text-dark font-monospace fw-bold">BOM TALLER</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Descripción del Insumo</th>
                        <th>Categoría</th>
                        <th>Unidad</th>
                        <th>Visibilidad</th>
                        <th class="text-end">Precio Referencial (S/.)</th>
                        <th class="text-end pe-4">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($herrajes as $her)
                    <tr>
                        <td class="ps-4 font-monospace fw-bold text-secondary">
                            HER-{{ sprintf('%02d', $loop->iteration) }}
                        </td>
                        <td>
                            <strong class="text-dark">{{ $her->nombre_herraje_global }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $her->categoria ?? 'General' }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ $her->unidad ?? 'Unidad' }}
                        </td>
                        <td>
                            @if(($her->tipo_visibilidad_herraje ?? 'oculto') == 'oculto')
                                <span class="badge bg-secondary text-white small">Oculto</span>
                            @else
                                <span class="badge bg-info text-dark small">Visto</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="fs-6 font-monospace fw-bold text-dark">
                                S/. {{ number_format($her->precio_referencial, 2) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2.5 py-1 rounded-pill">
                                Activo
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

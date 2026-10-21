@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- 1. ENCABEZADO DE AUDITORÍA FORENSE (Estilo Oficial Mydo-mar) -->
    <div class="card bg-dark text-white border-0 rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill">
                        Consola de Seguridad Forense
                    </span>
                    <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill">
                        Patrón Append-Only
                    </span>
                    <span class="badge bg-light text-dark px-3 py-1.5 rounded-pill font-monospace small">
                        Inmutable (Sin UPDATE ni DELETE)
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-white">Trazabilidad de Credenciales y Subroles B2B</h2>
                <p class="text-secondary mb-0 small">
                    Registro cronológico sellado mediante el motor dimensional <code>dim_tiempo</code>. Cada suceso captura la fotografía inalterable de modificaciones para cumplimiento y no repudio.
                </p>
            </div>
            <div>
                <a href="{{ route('empresa.empleados.index') }}" class="btn btn-warning fw-bold text-dark shadow-sm px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-2 text-nowrap">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Gestión de Empleados
                </a>
            </div>
        </div>
    </div>

    <!-- 2. MÉTRICAS FORENSES (Stat Cards Mydo-mar) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Eventos</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $metricas['total_eventos'] }}</h2>
                        <span class="text-muted small" style="font-size: 0.75rem;">users_credenciales_auditoria</span>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Altas de Personal</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $metricas['altas'] }}</h2>
                        <span class="text-success small fw-medium" style="font-size: 0.75rem;">CREACION_LOTE</span>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Reseteos de Clave</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $metricas['reseteos'] }}</h2>
                        <span class="text-danger small fw-medium" style="font-size: 0.75rem;">RESET_PASSWORD</span>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Cambios de Subrol</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $metricas['cambios_rol'] }}</h2>
                        <span class="text-info small fw-medium" style="font-size: 0.75rem;">CAMBIO_SUBROL</span>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. BARRA DE BÚSQUEDA Y FILTROS HTAP (Blanca Limpia) -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <form action="{{ route('empresa.auditoria.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">Empleado Involucrado</label>
                <select name="usuario_id" class="form-select" style="border-radius: 10px;">
                    <option value="">Todos los Empleados</option>
                    @foreach($empleados as $emp)
                        <option value="{{ $emp->id_usuario_users }}" {{ request('usuario_id') == $emp->id_usuario_users ? 'selected' : '' }}>
                            {{ $emp->nombre_usuario_users }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">Tipo de Evento</label>
                <select name="tipo_cambio" class="form-select" style="border-radius: 10px;">
                    <option value="">Todos los Tipos</option>
                    @foreach($tiposCambio as $tc)
                        <option value="{{ $tc }}" {{ request('tipo_cambio') == $tc ? 'selected' : '' }}>
                            {{ $tc }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-dark mb-1">Año (dim_tiempo)</label>
                <select name="anio" class="form-select" style="border-radius: 10px;">
                    <option value="">Todos</option>
                    <option value="2026" {{ request('anio') == '2026' ? 'selected' : '' }}>2026</option>
                    <option value="2025" {{ request('anio') == '2025' ? 'selected' : '' }}>2025</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-dark mb-1">Mes</label>
                <select name="mes" class="form-select" style="border-radius: 10px;">
                    <option value="">Todos</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>Mes {{ $m }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-bold text-dark w-100 shadow-sm" style="border-radius: 10px;">
                    Filtrar
                </button>
                @if(request()->hasAny(['usuario_id', 'tipo_cambio', 'anio', 'mes']))
                    <a href="{{ route('empresa.auditoria.index') }}" class="btn btn-light border text-muted" style="border-radius: 10px;">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. TABLA DEL REGISTRO FORENSE DE EVENTOS -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-0 p-4 pb-2 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Bitácora de Eventos de Seguridad Inmutables</h5>
                <p class="text-muted small mb-0">Auditoría cronológica criptográfica sin posibilidad de alteración</p>
            </div>
            <span class="badge bg-warning bg-opacity-15 text-dark fw-bold border border-warning border-opacity-25 px-3 py-1.5 rounded-pill font-monospace">
                Append-Only Log
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-monospace small">
                <thead class="table-light text-muted" style="font-size: 0.78rem;">
                    <tr>
                        <th class="ps-4">ID Evento</th>
                        <th>Timestamp (dim_tiempo)</th>
                        <th>Empleado Afectado</th>
                        <th>Subrol Operativo</th>
                        <th>Tipo de Modificación</th>
                        <th>Responsable</th>
                        <th class="pe-4">Snapshot Criptográfico</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditorias as $aud)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                #{{ str_pad($aud->id_auditoria_credencial, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <div class="text-dark fw-medium">
                                    {{ $aud->dimTiempo ? $aud->dimTiempo->fecha_hora_exacta : now() }}
                                </div>
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.65rem;">
                                    Smart Key: {{ $aud->id_dim_tiempo_evento }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark font-monospace">
                                    {{ $aud->usuarioAfectado->nombre_usuario_users ?? 'Usuario #' . $aud->id_usuario_users }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border font-sans">
                                    {{ $aud->usuarioAfectado->rolOperativo->nombre_rol_empresa ?? 'Sin Subrol' }}
                                </span>
                            </td>
                            <td>
                                @if(str_contains($aud->tipo_cambio, 'RESET'))
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">
                                        {{ $aud->tipo_cambio }}
                                    </span>
                                @elseif(str_contains($aud->tipo_cambio, 'CREACION'))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                        {{ $aud->tipo_cambio }}
                                    </span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                                        {{ $aud->tipo_cambio }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted font-sans">
                                    {{ $aud->usuarioModificador->nombre_usuario_users ?? 'Gerencia Madeart' }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <div class="small" style="font-size: 0.72rem;">
                                    @if($aud->valor_anterior_hash)
                                        <code class="text-muted bg-light px-1 py-0.5 rounded" title="{{ $aud->valor_anterior_hash }}">{{ substr($aud->valor_anterior_hash, 0, 8) }}...</code>
                                        <span class="text-warning fw-bold mx-1">&rarr;</span>
                                    @else
                                        <span class="text-muted">NULL</span> <span class="text-warning fw-bold mx-1">&rarr;</span>
                                    @endif
                                    @if($aud->valor_nuevo_hash)
                                        <code class="text-dark bg-warning bg-opacity-20 px-1 py-0.5 rounded fw-bold" title="{{ $aud->valor_nuevo_hash }}">{{ substr($aud->valor_nuevo_hash, 0, 8) }}...</code>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted font-sans">
                                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted opacity-50 mb-2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <div>No hay registros de auditoría que coincidan con los filtros aplicados.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditorias->hasPages())
            <div class="p-3 border-top bg-light d-flex justify-content-center">
                {{ $auditorias->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Encabezado de Auditoría Forense -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #18122B 0%, #1e1b4b 100%); border-left: 5px solid #f59e0b !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">Consola de Seguridad Forense</span>
                        <span class="badge bg-danger bg-opacity-75 text-white px-3 py-1 rounded-pill">Patrón Append-Only</span>
                        <span class="badge bg-secondary px-3 py-1 rounded-pill font-monospace">Inmutable (Sin UPDATE ni DELETE)</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Trazabilidad de Credenciales y Subroles B2B</h2>
                    <p class="text-secondary mb-0 small">
                        Registro cronológico sellado mediante el motor dimensional <code>dim_tiempo</code>. Cada suceso captura la fotografía inalterable de modificaciones para cumplimiento de normas de auditoría y no repudio.
                    </p>
                </div>

                <div>
                    <a href="{{ route('empresa.empleados.index') }}" class="btn btn-outline-light fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Volver a Gestión de Empleados
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Auditoría -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Total Eventos Registrados</div>
                    <div class="fs-2 fw-bold text-warning">{{ $metricas['total_eventos'] }}</div>
                    <div class="small text-secondary">users_credenciales_auditoria</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Altas de Personal</div>
                    <div class="fs-2 fw-bold text-success">{{ $metricas['altas'] }}</div>
                    <div class="small text-secondary">Eventos CREACION_LOTE</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Reseteos de Clave</div>
                    <div class="fs-2 fw-bold text-danger">{{ $metricas['reseteos'] }}</div>
                    <div class="small text-secondary">Eventos RESET_PASSWORD</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Cambios de Subrol</div>
                    <div class="fs-2 fw-bold text-info">{{ $metricas['cambios_rol'] }}</div>
                    <div class="small text-secondary">Reasignaciones de planta</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros HTAP (Dimensión Tiempo) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: #131c2e; border: 1px solid #263859 !important;">
        <div class="card-body p-3">
            <form action="{{ route('empresa.auditoria.index') }}" method="GET" class="row g-2 align-items-center">
                
                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Filtrar por Empleado</label>
                    <select name="usuario_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                        <option value="">Todos los Empleados</option>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id_usuario_users }}" {{ request('usuario_id') == $emp->id_usuario_users ? 'selected' : '' }}>
                                {{ $emp->nombre_usuario_users }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Tipo de Modificación</label>
                    <select name="tipo_cambio" class="form-select form-select-sm bg-dark text-white border-secondary">
                        <option value="">Todos los Tipos</option>
                        @foreach($tiposCambio as $tc)
                            <option value="{{ $tc }}" {{ request('tipo_cambio') == $tc ? 'selected' : '' }}>
                                {{ $tc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-secondary small fw-bold mb-1">Año (dim_tiempo)</label>
                    <select name="anio" class="form-select form-select-sm bg-dark text-white border-secondary">
                        <option value="">Todos</option>
                        <option value="2026" {{ request('anio') == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ request('anio') == '2025' ? 'selected' : '' }}>2025</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-secondary small fw-bold mb-1">Mes (dim_tiempo)</label>
                    <select name="mes" class="form-select form-select-sm bg-dark text-white border-secondary">
                        <option value="">Todos los Meses</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>Mes {{ $m }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2 align-items-end" style="margin-top: 28px;">
                    <button type="submit" class="btn btn-sm btn-warning fw-bold w-100">Consultar HTAP</button>
                    @if(request()->hasAny(['usuario_id', 'tipo_cambio', 'anio', 'mes']))
                        <a href="{{ route('empresa.auditoria.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    <!-- Registro Forense de Eventos -->
    <div class="card border-0 shadow-sm rounded-4 text-white" style="background: #131c2e; border: 1px solid #263859 !important;">
        <div class="card-header bg-transparent border-bottom border-secondary py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                <span>🛡️</span> Log de Eventos de Seguridad Inmutables
            </h5>
            <span class="badge bg-secondary font-monospace">Append-Only Logs</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 font-monospace small" style="background: transparent;">
                    <thead>
                        <tr class="text-secondary text-uppercase" style="border-bottom: 2px solid #263859; font-size: 0.75rem;">
                            <th class="ps-4">ID Evento</th>
                            <th>Timestamp Exacto (dim_tiempo)</th>
                            <th>Empleado Afectado</th>
                            <th>Subrol en Planta</th>
                            <th>Tipo de Modificación</th>
                            <th>Responsable</th>
                            <th class="pe-4">Snapshot Criptográfico</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditorias as $aud)
                            <tr>
                                <td class="ps-4 text-warning fw-bold">#{{ str_pad($aud->id_auditoria_credencial, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="text-light">
                                        {{ $aud->dimTiempo ? $aud->dimTiempo->fecha_hora_exacta : now() }}
                                    </div>
                                    <span class="badge bg-dark text-secondary" style="font-size: 0.65rem;">
                                        Key: {{ $aud->id_dim_tiempo_evento }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-info">
                                        {{ $aud->usuarioAfectado->nombre_usuario_users ?? 'Usuario #' . $aud->id_usuario_users }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $aud->usuarioAfectado->rolOperativo->nombre_rol_empresa ?? 'Sin Subrol' }}
                                    </span>
                                </td>
                                <td>
                                    @if(str_contains($aud->tipo_cambio, 'RESET'))
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger">{{ $aud->tipo_cambio }}</span>
                                    @elseif(str_contains($aud->tipo_cambio, 'CREACION'))
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success">{{ $aud->tipo_cambio }}</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-25 text-info border border-info">{{ $aud->tipo_cambio }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-light">
                                        {{ $aud->usuarioModificador->nombre_usuario_users ?? 'Gerencia Madeart' }}
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <div class="small" style="font-size: 0.72rem; color: #94a3b8;">
                                        @if($aud->valor_anterior_hash)
                                            <span class="text-secondary" title="{{ $aud->valor_anterior_hash }}">{{ substr($aud->valor_anterior_hash, 0, 10) }}...</span>
                                            <span class="text-warning">&rarr;</span>
                                        @else
                                            <span class="text-secondary">NULL</span> <span class="text-warning">&rarr;</span>
                                        @endif
                                        @if($aud->valor_nuevo_hash)
                                            <span class="text-success fw-bold" title="{{ $aud->valor_nuevo_hash }}">{{ substr($aud->valor_nuevo_hash, 0, 10) }}...</span>
                                        @else
                                            <span class="text-secondary">N/A</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-secondary">
                                    No hay registros de auditoría que coincidan con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($auditorias->hasPages())
                <div class="p-3 border-top border-secondary">
                    {{ $auditorias->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

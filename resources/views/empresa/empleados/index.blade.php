@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- 1. BANNER CORPORATIVO B2B (Estilo Oficial Mydo-mar) -->
    <div class="card bg-dark text-white border-0 rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill">
                        Módulo Corporativo B2B
                    </span>
                    <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill">
                        Planta Madeart
                    </span>
                    <span class="badge bg-light text-dark px-3 py-1.5 rounded-pill font-monospace small">
                        Smart Key: {{ $empresa->dimTiempo->id_dim_tiempo ?? date('YmdH') }}
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-white">{{ $empresa->razon_social_empresa_users }}</h2>
                <p class="text-secondary mb-0 small">
                    <strong>NIT:</strong> {{ $empresa->nit_empresa_users }} &nbsp;•&nbsp; 
                    <strong>Contacto WhatsApp:</strong> {{ $empresa->telefono_whatsapp_empresa_users }} &nbsp;•&nbsp;
                    <strong>Estado:</strong> <span class="text-success fw-semibold">Operativo / Activo</span>
                </p>
            </div>
            <div>
                <a href="{{ route('empresa.auditoria.index') }}" class="btn btn-warning fw-bold text-dark shadow-sm px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-2 text-nowrap">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Consola de Auditoría Forense
                </a>
            </div>
        </div>
    </div>

    <!-- 2. ALERTAS DEL SISTEMA -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
            <svg width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill text-success flex-shrink-0" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <div class="fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <div class="fw-bold mb-1">Por favor revise los siguientes campos:</div>
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 3. RESUMEN DEL LOTE GENERADO (VOUCHER DE CREDENCIALES) -->
    @if(session('lote_generado'))
        @php $lote = session('lote_generado'); @endphp
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 border-start border-4 border-warning overflow-hidden">
            <div class="card-header bg-warning bg-opacity-10 border-0 p-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="text-warning text-dark" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="fw-bold text-dark">Lote de Credenciales Generado Exitosamente</span>
                    <span class="badge bg-warning text-dark font-monospace">{{ count($lote) }} Empleados</span>
                </div>
                <button type="button" onclick="window.print();" class="btn btn-sm btn-dark fw-bold rounded-2 d-inline-flex align-items-center gap-1 shadow-sm">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir Vouchers
                </button>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Guarde o entregue estas contraseñas temporales a los operarios. Por seguridad forense inmutable, no podrán volver a consultarse en texto plano.
                </p>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Usuario</th>
                                <th>Correo Corporativo</th>
                                <th>Subrol Asignado</th>
                                <th>Contraseña Temporal</th>
                            </tr>
                        </thead>
                        <tbody class="font-monospace small">
                            @foreach($lote as $item)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $item['username'] }}</td>
                                    <td class="text-secondary">{{ $item['email'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $item['rol'] }}</span></td>
                                    <td><code class="fw-bold text-dark bg-warning bg-opacity-25 px-2 py-1 rounded">{{ $item['password'] }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- 4. TARJETAS DE MÉTRICAS (ESTILO DASHBOARD MYDO-MAR) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Personal de Planta</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $empleados->total() }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Armadores de Muebles</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $subroles->where('nombre_rol_empresa', 'Armador de Muebles')->first()->nombre_rol_empresa ?? 'Activos' }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Subroles de Fábrica</p>
                        <h2 class="fw-bold text-dark mb-0">{{ count($subroles) }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-secondary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Auditoría Inmutable</p>
                        <h4 class="fw-bold text-dark mb-0">Append-Only</h4>
                    </div>
                    <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. DISTRIBUCIÓN PRINCIPAL (FORMULARIO A LA IZQUIERDA + NÓMINA A LA DERECHA) -->
    <div class="row g-4">
        <!-- Columna 1: Formulario de Generación Masiva (4 cols) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Generar Lote de Personal</h5>
                        <p class="text-muted small mb-0">Alta rápida con contraseñas seguras</p>
                    </div>
                </div>

                <form action="{{ route('empresa.empleados.generarLote') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Cantidad de Empleados</label>
                        <input type="number" name="cantidad" min="1" max="50" value="5" class="form-control" style="border-radius: 10px;" required>
                        <div class="form-text small text-muted">Genera entre 1 y 50 cuentas operativas.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Prefijo para Nombre de Usuario</label>
                        <input type="text" name="prefijo" value="operario_madeart" class="form-control font-monospace" style="border-radius: 10px;" required>
                        <div class="form-text small text-muted">Ejemplo resultante: <code>operario_madeart_01</code></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Subrol Operativo en Planta</label>
                        <select name="id_empresa_rol" class="form-select" style="border-radius: 10px;" required>
                            @foreach($subroles as $sr)
                                <option value="{{ $sr->id_empresa_rol }}" {{ $sr->es_rol_predeterminado ? 'selected' : '' }}>
                                    {{ $sr->nombre_rol_empresa }} {{ $sr->es_rol_predeterminado ? '(Predeterminado)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notificar_email" id="chkEmail" checked>
                            <label class="form-check-label small text-muted" for="chkEmail">
                                Notificar credenciales por correo electrónico
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark w-100 shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Generar Cuentas de Lote
                    </button>
                </form>
            </div>
        </div>

        <!-- Columna 2: Tabla de Nómina y Filtros (8 cols) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <!-- Filtros y Búsqueda -->
                <div class="card-header bg-white border-0 p-4 pb-2">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Nómina de Operarios de Fábrica</h5>
                            <p class="text-muted small mb-0">Cuentas activas registradas bajo la empresa Madeart</p>
                        </div>
                        
                        <form action="{{ route('empresa.empleados.index') }}" method="GET" class="d-flex gap-2">
                            <select name="subrol" class="form-select form-select-sm" style="border-radius: 8px; min-width: 150px;">
                                <option value="">Todos los Subroles</option>
                                @foreach($subroles as $sr)
                                    <option value="{{ $sr->id_empresa_rol }}" {{ request('subrol') == $sr->id_empresa_rol ? 'selected' : '' }}>
                                        {{ $sr->nombre_rol_empresa }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-dark fw-bold px-3" style="border-radius: 8px;">
                                Filtrar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Personal -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-4">Usuario</th>
                                <th>Subrol Asignado</th>
                                <th>Correo Corporativo</th>
                                <th>Registro Temporal</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($empleados as $emp)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-warning bg-opacity-10 text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.82rem;">
                                                {{ strtoupper(substr($emp->nombre_usuario_users, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark font-monospace">{{ $emp->nombre_usuario_users }}</div>
                                                @if($emp->cuenta_generada_por_empresa)
                                                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.65rem;">Lote Corporativo</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-15 text-dark fw-bold border border-warning border-opacity-25 px-2.5 py-1 rounded-pill">
                                            {{ $emp->rolOperativo->nombre_rol_empresa ?? 'Operario Base' }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $emp->email_usuario_users }}</td>
                                    <td>
                                        <span class="text-dark small font-monospace">
                                            {{ $emp->dimTiempo ? $emp->dimTiempo->fecha_hora_exacta : '2026-10-21 08:00' }}
                                        </span>
                                        <br>
                                        <span class="badge bg-light text-muted font-monospace" style="font-size: 0.65rem;">Key: {{ $emp->id_dim_tiempo_registro }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-2" type="button" data-bs-toggle="dropdown">
                                                Opciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-1">
                                                <li>
                                                    <button type="button" class="dropdown-item small py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCambiarRol-{{ $emp->id_usuario_users }}">
                                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        Cambiar Subrol
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider my-1 opacity-25"></li>
                                                <li>
                                                    <form action="{{ route('empresa.empleados.resetPassword', $emp->id_usuario_users) }}" method="POST" onsubmit="return confirm('¿Seguro que desea resetear la contraseña de {{ $emp->nombre_usuario_users }}? Este evento se registrará en la auditoría forense inmutable.');">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item small text-danger py-2 d-flex align-items-center gap-2">
                                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                                            Resetear Contraseña
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Modal Cambiar Subrol (Blanco Limpio) -->
                                        <div class="modal fade" id="modalCambiarRol-{{ $emp->id_usuario_users }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold text-dark">Reasignar Subrol</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('empresa.empleados.cambiarSubrol', $emp->id_usuario_users) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body text-start">
                                                            <p class="text-muted small mb-3">
                                                                Seleccione el nuevo subrol operativo para el usuario <strong>{{ $emp->nombre_usuario_users }}</strong>.
                                                            </p>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Nuevo Subrol de Planta</label>
                                                                <select name="id_empresa_rol" class="form-select" style="border-radius: 10px;" required>
                                                                    @foreach($subroles as $sr)
                                                                        <option value="{{ $sr->id_empresa_rol }}" {{ $emp->id_empresa_rol == $sr->id_empresa_rol ? 'selected' : '' }}>
                                                                            {{ $sr->nombre_rol_empresa }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="alert alert-light border small text-muted mb-0">
                                                                Este cambio generará automáticamente un registro inmutable en la bitácora de auditoría forense.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light border text-muted fw-bold rounded-2 px-3" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-warning fw-bold text-dark rounded-2 px-3 shadow-sm">Guardar Cambio</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted opacity-50 mb-2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                        <div class="fw-medium">No se encontraron empleados registrados.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($empleados->hasPages())
                    <div class="p-3 border-top bg-light d-flex justify-content-center">
                        {{ $empleados->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

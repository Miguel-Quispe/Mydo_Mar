@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Encabezado Corporativo B2B -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 5px solid #38bdf8 !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary px-3 py-1 rounded-pill">Módulo Corporativo B2B</span>
                        <span class="badge bg-secondary px-3 py-1 rounded-pill">Planta Madeart</span>
                        <span class="badge bg-info text-dark px-3 py-1 rounded-pill font-monospace">Smart Key: {{ $empresa->dimTiempo->id_dim_tiempo ?? date('YmdH') }}</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">{{ $empresa->razon_social_empresa_users }}</h2>
                    <p class="text-secondary mb-0 small">
                        <strong>NIT:</strong> {{ $empresa->nit_empresa_users }} &nbsp;•&nbsp; 
                        <strong>Contacto WhatsApp:</strong> {{ $empresa->telefono_whatsapp_empresa_users }} &nbsp;•&nbsp;
                        <strong>Estado:</strong> <span class="text-success fw-semibold">Operativo / Activo</span>
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('empresa.auditoria.index') }}" class="btn btn-outline-warning fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Consola Forense de Auditoría
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas del Sistema -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
            <svg width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill text-success flex-shrink-0" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
            <div class="fw-bold mb-1">Por favor corrija los siguientes errores:</div>
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Modal / Alerta de Credenciales del Lote Recién Generado -->
    @if(session('lote_generado'))
        @php $lote = session('lote_generado'); @endphp
        <div class="card border-0 shadow-lg rounded-4 mb-4 bg-dark text-white border-warning" style="border: 2px solid #f59e0b !important;">
            <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center rounded-top-4">
                <div class="fw-bold fs-5">
                    🎉 Lote Generado Exitosamente: {{ $lote['total_creados'] }} Cuentas Creadas
                </div>
                <span class="badge bg-dark text-warning font-monospace">Subrol: {{ $lote['subrol'] }}</span>
            </div>
            <div class="card-body p-4">
                <p class="text-warning-emphasis small mb-3">
                    <strong>IMPORTANTE:</strong> Estas son las contraseñas provisionales autogeneradas. Por motivos de seguridad y auditoría forense, los hashes se sellaron en la base de datos y no podrán visualizarse en texto plano nuevamente. Copie o descargue esta lista.
                </p>

                <div class="table-responsive">
                    <table class="table table-dark table-striped table-bordered align-middle mb-0 font-monospace small">
                        <thead>
                            <tr class="table-secondary text-dark">
                                <th>Usuario</th>
                                <th>Correo Corporativo</th>
                                <th>Contraseña Temporal</th>
                                <th>Subrol Asignado</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lote['cuentas'] as $cuenta)
                                <tr>
                                    <td class="fw-bold text-info">{{ $cuenta['username'] }}</td>
                                    <td>{{ $cuenta['email'] }}</td>
                                    <td><span class="badge bg-light text-dark fs-6">{{ $cuenta['plain_password'] }}</span></td>
                                    <td><span class="badge bg-primary">{{ $cuenta['subrol'] }}</span></td>
                                    <td class="text-secondary">{{ $cuenta['fecha_hora'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if(session('password_reseteada'))
        @php $reset = session('password_reseteada'); @endphp
        <div class="alert alert-warning border-0 rounded-3 shadow-sm d-flex justify-content-between align-items-center mb-4">
            <div>
                <strong>Nueva Contraseña para {{ $reset['username'] }}:</strong>
                <span class="badge bg-dark text-warning fs-6 font-monospace ms-2">{{ $reset['nueva_password'] }}</span>
                <span class="small text-muted ms-2">(Evento de seguridad registrado en tabla de auditoría forense)</span>
            </div>
        </div>
    @endif

    <!-- Métricas Rápidas de Planta -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Total Empleados</div>
                    <div class="fs-2 fw-bold text-white">{{ $metricas['total_empleados'] }}</div>
                    <div class="small text-info">users_cuentas en Madeart</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Armadores de Fábrica</div>
                    <div class="fs-2 fw-bold text-success">{{ $metricas['armadores'] }}</div>
                    <div class="small text-secondary">Subrol: Armador de Muebles</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Diseñadores 3D</div>
                    <div class="fs-2 fw-bold text-info">{{ $metricas['disenadores'] }}</div>
                    <div class="small text-secondary">Subrol: Modulador Three.js</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background:#131c2e;">
                <div class="card-body p-3">
                    <div class="text-secondary small fw-semibold text-uppercase">Supervisores</div>
                    <div class="fs-2 fw-bold text-warning">{{ $metricas['supervisores'] }}</div>
                    <div class="small text-secondary">Subrol: Control de Calidad</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Formulario: Generación Masiva por Lote -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: #131c2e; border: 1px solid #263859 !important;">
        <div class="card-header bg-transparent border-bottom border-secondary py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-bold fs-5 text-white d-flex align-items-center gap-2">
                    <span class="text-warning">⚡</span> Alta Masiva de Cuentas Operativas (Lote Atómico)
                </div>
                <span class="badge bg-secondary font-monospace">INSERT &rarr; users_cuentas</span>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('empresa.empleados.generarLote') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-bold">Subrol Operativo en Fábrica *</label>
                        <select name="empresa_rol_id" class="form-select bg-dark text-white border-secondary" required>
                            @foreach($subroles as $sr)
                                <option value="{{ $sr->id_empresa_rol }}" {{ old('empresa_rol_id') == $sr->id_empresa_rol ? 'selected' : '' }}>
                                    {{ $sr->nombre_rol_empresa }} {{ $sr->es_rol_predeterminado ? '(Predeterminado)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-secondary small fw-bold">Prefijo de Usuario *</label>
                        <input type="text" name="prefijo_usuario" class="form-control bg-dark text-white border-secondary font-monospace" 
                               placeholder="ej: madeart_armador" value="{{ old('prefijo_usuario', 'madeart_armador') }}" required>
                        <div class="form-text text-secondary small">Se concatenará con correlativo _01, _02...</div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label text-secondary small fw-bold">Cantidad de Cuentas *</label>
                        <input type="number" name="cantidad" class="form-control bg-dark text-white border-secondary" 
                               min="1" max="20" value="{{ old('cantidad', 5) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-secondary small fw-bold">Dominio de Correo</label>
                        <input type="text" name="dominio_email" class="form-control bg-dark text-white border-secondary" 
                               value="{{ old('dominio_email', '@madeart.com') }}">
                    </div>

                    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center mt-3 pt-3 border-top border-secondary">
                        <div class="text-secondary small">
                            🛡️ Toda cuenta generada sella su evento en <span class="badge bg-secondary font-monospace">users_credenciales_auditoria</span> y calcula automáticamente la Smart Key en <span class="badge bg-secondary font-monospace">dim_tiempo</span>.
                        </div>
                        <button type="submit" class="btn btn-success fw-bold px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                            Generar Cuentas y Registrar en Base de Datos
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Personal y Búsqueda -->
    <div class="card border-0 shadow-sm rounded-4 text-white" style="background: #131c2e; border: 1px solid #263859 !important;">
        <div class="card-header bg-transparent border-bottom border-secondary py-3">
            <div class="row align-items-center g-2">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-0 text-white">Nómina de Empleados y Cuentas Activas</h5>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('empresa.empleados.index') }}" method="GET" class="d-flex gap-2">
                        <select name="subrol_id" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()">
                            <option value="">Todos los Subroles</option>
                            @foreach($subroles as $sr)
                                <option value="{{ $sr->id_empresa_rol }}" {{ request('subrol_id') == $sr->id_empresa_rol ? 'selected' : '' }}>
                                    {{ $sr->nombre_rol_empresa }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="buscar" class="form-control form-control-sm bg-dark text-white border-secondary" 
                               placeholder="Buscar usuario o correo..." value="{{ request('buscar') }}">
                        <button type="submit" class="btn btn-sm btn-outline-info">Filtrar</button>
                        @if(request()->hasAny(['subrol_id', 'buscar']))
                            <a href="{{ route('empresa.empleados.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                    <thead>
                        <tr class="text-secondary small text-uppercase" style="border-bottom: 2px solid #263859;">
                            <th class="ps-4">Usuario</th>
                            <th>Subrol de Fábrica</th>
                            <th>Correo Corporativo</th>
                            <th>Sello de Creación (dim_tiempo)</th>
                            <th class="text-end pe-4">Acciones de Seguridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $emp)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($emp->nombre_usuario_users, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white font-monospace">{{ $emp->nombre_usuario_users }}</div>
                                            @if($emp->cuenta_generada_por_empresa)
                                                <span class="badge bg-secondary font-monospace" style="font-size: 0.65rem;">Generada por Empresa</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($emp->id_empresa_rol == 1)
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success">{{ $emp->rolOperativo->nombre_rol_empresa ?? 'Armador' }}</span>
                                    @elseif($emp->id_empresa_rol == 2)
                                        <span class="badge bg-info bg-opacity-25 text-info border border-info">{{ $emp->rolOperativo->nombre_rol_empresa ?? 'Diseñador' }}</span>
                                    @elseif($emp->id_empresa_rol == 3)
                                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning">{{ $emp->rolOperativo->nombre_rol_empresa ?? 'Supervisor' }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $emp->rolOperativo->nombre_rol_empresa ?? 'Sin Asignar' }}</span>
                                    @endif
                                </td>
                                <td class="text-light small">{{ $emp->email_usuario_users }}</td>
                                <td>
                                    <span class="text-secondary small font-monospace">
                                        {{ $emp->dimTiempo ? $emp->dimTiempo->fecha_hora_exacta : '2026-10-21 08:00' }}
                                    </span>
                                    <br>
                                    <span class="badge bg-dark text-secondary font-monospace" style="font-size: 0.65rem;">Key: {{ $emp->id_dim_tiempo_registro }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Opciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                                            <li>
                                                <button type="button" class="dropdown-item text-light small" data-bs-toggle="modal" data-bs-target="#modalCambiarRol-{{ $emp->id_usuario_users }}">
                                                    ✏️ Cambiar Subrol de Fábrica
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider border-secondary"></li>
                                            <li>
                                                <form action="{{ route('empresa.empleados.resetPassword', $emp->id_usuario_users) }}" method="POST" onsubmit="return confirm('¿Seguro que desea resetear la contraseña de {{ $emp->nombre_usuario_users }}? Este evento se registrará en la auditoría forense inmutable.');">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-warning small">
                                                        🔑 Resetear Contraseña (Forense)
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Modal Cambiar Subrol -->
                                    <div class="modal fade" id="modalCambiarRol-{{ $emp->id_usuario_users }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content bg-dark text-white border-secondary">
                                                <div class="modal-header border-secondary">
                                                    <h6 class="modal-title fw-bold">Reasignar Subrol a {{ $emp->nombre_usuario_users }}</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('empresa.empleados.cambiarSubrol', $emp->id_usuario_users) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-body text-start">
                                                        <label class="form-label small text-secondary">Nuevo Subrol Operativo</label>
                                                        <select name="id_empresa_rol" class="form-select bg-dark text-white border-secondary" required>
                                                            @foreach($subroles as $sr)
                                                                <option value="{{ $sr->id_empresa_rol }}" {{ $emp->id_empresa_rol == $sr->id_empresa_rol ? 'selected' : '' }}>
                                                                    {{ $sr->nombre_rol_empresa }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="form-text text-secondary small mt-2">
                                                            El cambio generará automáticamente un registro inmutable en la tabla de auditoría forense.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-sm btn-primary">Guardar Cambio</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary">
                                    No se encontraron empleados registrados con los criterios seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($empleados->hasPages())
                <div class="p-3 border-top border-secondary">
                    {{ $empleados->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

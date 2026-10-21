@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-9 col-lg-8 col-xl-7">
            
            <!-- Botón de Regreso a la Pantalla Anterior -->
            <div class="mb-3">
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : url('/') }}" 
                   onclick="if (window.history.length > 1) { window.history.back(); return false; }"
                   class="btn-regresar-back">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Volver a la pantalla anterior</span>
                </a>
            </div>

            <!-- Tarjeta Principal de Registro -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                
                <!-- Encabezado Oscuro con la Marca -->
                <div class="card-header bg-dark text-white p-4 text-center border-0">
                    <h4 class="fw-bold mb-1">Crea tu cuenta en <span class="text-warning">Mydo-mar</span></h4>
                    <p class="text-muted small mb-0">Selecciona el tipo de cuenta y completa los datos para ingresar</p>
                </div>

                <div class="card-body p-4 p-sm-5 bg-white">
                    <form method="POST" action="{{ route('register') }}" id="form-registro">
                        @csrf

                        <!-- Selector Visual de Tipo de Cuenta / Rol -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark mb-2 d-flex justify-content-between align-items-center">
                                <span>Tipo de Cuenta:</span>
                                <span class="badge bg-secondary-subtle text-dark border fw-semibold">Selección obligatoria</span>
                            </label>

                            <!-- Hidden input para el envío de rol_id -->
                            <input type="hidden" name="rol_id" id="rol_id" value="{{ old('rol_id', '2') }}">

                            <div class="row g-2" id="selector-roles">
                                <!-- Opción 1: Cliente -->
                                <div class="col-md-4">
                                    <div class="card h-100 p-3 text-center border rounded-3 cursor-pointer role-card transition-all {{ old('rol_id', '2') == '2' ? 'border-warning bg-warning bg-opacity-10 shadow-sm' : 'border-secondary-subtle bg-light' }}" 
                                         data-role-value="2" 
                                         style="cursor: pointer; transition: all 0.2s ease;">
                                        <div class="mb-2">
                                            <span class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 42px; height: 42px;">
                                                <svg width="20" height="20" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1 fs-6">Cliente</h6>
                                        <p class="text-muted small mb-0" style="font-size: 0.78rem; line-height: 1.25;">Explora muebles, guarda favoritos y cotiza despieces</p>
                                    </div>
                                </div>

                                <!-- Opción 2: Maestro Carpintero -->
                                <div class="col-md-4">
                                    <div class="card h-100 p-3 text-center border rounded-3 cursor-pointer role-card transition-all {{ old('rol_id') == '1' ? 'border-warning bg-warning bg-opacity-10 shadow-sm' : 'border-secondary-subtle bg-light' }}" 
                                         data-role-value="1" 
                                         style="cursor: pointer; transition: all 0.2s ease;">
                                        <div class="mb-2">
                                            <span class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 42px; height: 42px;">
                                                <svg width="20" height="20" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1 fs-6">Maestro</h6>
                                        <p class="text-muted small mb-0" style="font-size: 0.78rem; line-height: 1.25;">Diseña piezas, publica proyectos y gestiona pedidos</p>
                                    </div>
                                </div>

                                <!-- Opción 3: Empresa / B2B -->
                                <div class="col-md-4">
                                    <div class="card h-100 p-3 text-center border rounded-3 cursor-pointer role-card transition-all {{ old('rol_id') == '3' ? 'border-warning bg-warning bg-opacity-10 shadow-sm' : 'border-secondary-subtle bg-light' }}" 
                                         data-role-value="3" 
                                         style="cursor: pointer; transition: all 0.2s ease;">
                                        <div class="mb-2">
                                            <span class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 42px; height: 42px;">
                                                <svg width="20" height="20" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                            <h6 class="fw-bold text-dark mb-0 fs-6">Empresa B2B</h6>
                                            <span class="badge bg-warning text-dark px-1 py-0" style="font-size: 0.65rem;">Pro</span>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size: 0.78rem; line-height: 1.25;">Mueblerías, insumos de melamina y 4 subroles de fábrica</p>
                                    </div>
                                </div>
                            </div>
                            @error('rol_id')
                                <div class="text-danger small mt-2"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <!-- SECCIÓN DINÁMICA: DATOS CORPORATIVOS & INSUMOS (SOLO PARA EMPRESA B2B rol_id = 3) -->
                        <div id="seccion-empresa" class="mb-4 p-3 p-sm-4 rounded-4 border border-warning-subtle bg-warning bg-opacity-10 {{ old('rol_id') == '3' ? '' : 'd-none' }}">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-warning text-dark p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M3 21h18M3 7v14m18-14v14M6 7V3h12v4M9 11h2m-2 4h2m4-4h2m-2 4h2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">Datos de la Empresa & Cotización de Insumos</h6>
                                        <small class="text-muted">Aprovisionamiento corporativo y subroles operativos</small>
                                    </div>
                                </div>
                                <span class="badge bg-dark text-warning px-2 py-1 small">Inquilino B2B</span>
                            </div>

                            <!-- Razón Social y NIT -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label for="razon_social" class="form-label small fw-bold text-dark mb-1">Razón Social o Nombre Comercial *</label>
                                    <input id="razon_social" type="text"
                                           class="form-control fs-6 bg-white @error('razon_social') is-invalid @enderror"
                                           name="razon_social" value="{{ old('razon_social') }}"
                                           placeholder="Ej: Mueblería Santa Cruz S.R.L." style="border-radius: 10px;">
                                    @error('razon_social')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="nit" class="form-label small fw-bold text-dark mb-1">NIT / Id Fiscal *</label>
                                    <input id="nit" type="text"
                                           class="form-control fs-6 bg-white @error('nit') is-invalid @enderror"
                                           name="nit" value="{{ old('nit') }}"
                                           placeholder="Ej: 1023849021" style="border-radius: 10px;">
                                    @error('nit')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- WhatsApp Corporativo -->
                            <div class="mb-3">
                                <label for="telefono_whatsapp" class="form-label small fw-bold text-dark mb-1">WhatsApp Oficial de Gerencia / Cotizaciones</label>
                                <input id="telefono_whatsapp" type="text"
                                       class="form-control fs-6 bg-white @error('telefono_whatsapp') is-invalid @enderror"
                                       name="telefono_whatsapp" value="{{ old('telefono_whatsapp') }}"
                                       placeholder="+591 78945612" style="border-radius: 10px;">
                                <div class="form-text small text-muted" style="font-size: 0.76rem;">
                                    Se usará para la recepción directa de pedidos de tableros, tornillos y avisos de producción.
                                </div>
                                @error('telefono_whatsapp')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <!-- Áreas de Insumos & Líneas de Fábrica -->
                            <div class="bg-white p-3 rounded-3 border mb-3">
                                <div class="fw-bold small text-dark mb-2">Insumos y Líneas Operativas de tu Empresa:</div>
                                <div class="form-check form-check-inline mb-1">
                                    <input class="form-check-input" type="checkbox" id="insumo_tableros" name="insumos[]" value="tableros" checked>
                                    <label class="form-check-label small text-dark" for="insumo_tableros">
                                        Tableros y Melaminas (Optimización de corte y cálculo de m²)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline mb-1">
                                    <input class="form-check-input" type="checkbox" id="insumo_tornilleria" name="insumos[]" value="tornillos" checked>
                                    <label class="form-check-label small text-dark" for="insumo_tornilleria">
                                        Tornillería y Herrajes (BOM exacto de tornillos autorroscantes y bisagras)
                                    </label>
                                </div>
                            </div>

                            <!-- Subroles Operativos Preconfigurados -->
                            <div class="bg-white p-3 rounded-3 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-bold text-dark">4 Subroles Operativos Creados Automáticamente:</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Listo para fábrica</span>
                                </div>
                                <div class="row g-2 text-muted small" style="font-size: 0.78rem;">
                                    <div class="col-6 col-md-3 d-flex align-items-center gap-1">
                                        <svg width="14" height="14" fill="currentColor" class="text-warning flex-shrink-0" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                                        <span>Armador Planta</span>
                                    </div>
                                    <div class="col-6 col-md-3 d-flex align-items-center gap-1">
                                        <svg width="14" height="14" fill="currentColor" class="text-warning flex-shrink-0" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                                        <span>Diseñador 3D</span>
                                    </div>
                                    <div class="col-6 col-md-3 d-flex align-items-center gap-1">
                                        <svg width="14" height="14" fill="currentColor" class="text-warning flex-shrink-0" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                                        <span>Supervisor Calidad</span>
                                    </div>
                                    <div class="col-6 col-md-3 d-flex align-items-center gap-1">
                                        <svg width="14" height="14" fill="currentColor" class="text-warning flex-shrink-0" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                                        <span>Transporte & Montaje</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DATOS DEL USUARIO / TITULAR DE LA CUENTA -->
                        <div class="border-top pt-3 mb-3">
                            <h6 class="fw-bold text-dark mb-3" id="titulo-datos-personales">Datos de la Cuenta</h6>

                            <!-- Nombre Completo -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label small fw-bold text-dark mb-1">Nombre(s) *</label>
                                <input id="nombre" type="text"
                                       class="form-control form-control-lg fs-6 @error('nombre') is-invalid @enderror"
                                       name="nombre" value="{{ old('nombre') }}" required autocomplete="given-name" autofocus
                                       placeholder="Juan" style="border-radius: 10px;">
                                @error('nombre')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="apellidoPaterno" class="form-label small fw-bold text-dark mb-1">Apellido Paterno *</label>
                                    <input id="apellidoPaterno" type="text"
                                           class="form-control fs-6 @error('apellidoPaterno') is-invalid @enderror"
                                           name="apellidoPaterno" value="{{ old('apellidoPaterno') }}" required placeholder="Pérez" style="border-radius: 10px;">
                                    @error('apellidoPaterno')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidoMaterno" class="form-label small fw-bold text-dark mb-1">Apellido Materno</label>
                                    <input id="apellidoMaterno" type="text"
                                           class="form-control fs-6 @error('apellidoMaterno') is-invalid @enderror"
                                           name="apellidoMaterno" value="{{ old('apellidoMaterno') }}" placeholder="Gómez" style="border-radius: 10px;">
                                    @error('apellidoMaterno')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contacto y Login -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label small fw-bold text-dark mb-1">Teléfono / Celular *</label>
                                    <input id="telefono" type="text"
                                           class="form-control fs-6 @error('telefono') is-invalid @enderror"
                                           name="telefono" value="{{ old('telefono') }}" required placeholder="+591 70000000" style="border-radius: 10px;">
                                    @error('telefono')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label small fw-bold text-dark mb-1">Correo Electrónico *</label>
                                    <input id="email" type="email"
                                           class="form-control fs-6 @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required placeholder="juan@ejemplo.com" style="border-radius: 10px;">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contraseñas -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="password" class="form-label small fw-bold text-dark mb-1">Contraseña *</label>
                                    <input id="password" type="password"
                                           class="form-control fs-6 @error('password') is-invalid @enderror"
                                           name="password" required placeholder="••••••••" style="border-radius: 10px;">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password-confirm" class="form-label small fw-bold text-dark mb-1">Confirmar Contraseña *</label>
                                    <input id="password-confirm" type="password" class="form-control fs-6" name="password_confirmation" required placeholder="••••••••" style="border-radius: 10px;">
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Registro -->
                        <div class="d-grid mb-3">
                            <button type="submit" id="btn-submit-registro" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm py-2" style="border-radius: 10px;">
                                {{ old('rol_id') == '3' ? 'Registrar Empresa y Activar Subroles' : 'Completar Registro' }}
                            </button>
                        </div>
                    </form>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Enlace a Login -->
                    <div class="text-center">
                        <p class="small text-muted mb-0">¿Ya tienes una cuenta registrada?</p>
                        <a href="{{ route('login') }}" class="fw-bold text-dark text-decoration-none small">
                            Inicia sesión aquí
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Lógica interactiva para la selección de rol y sección corporativa -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleCards = document.querySelectorAll('.role-card');
    const rolInput = document.getElementById('rol_id');
    const seccionEmpresa = document.getElementById('seccion-empresa');
    const razonSocialInput = document.getElementById('razon_social');
    const nitInput = document.getElementById('nit');
    const btnSubmit = document.getElementById('btn-submit-registro');
    const tituloDatosPersonales = document.getElementById('titulo-datos-personales');

    function actualizarVistaRol(rol) {
        rolInput.value = rol;

        // Actualizar diseño de las tarjetas
        roleCards.forEach(card => {
            if (card.getAttribute('data-role-value') === String(rol)) {
                card.classList.remove('border-secondary-subtle', 'bg-light');
                card.classList.add('border-warning', 'bg-warning', 'bg-opacity-10', 'shadow-sm');
            } else {
                card.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10', 'shadow-sm');
                card.classList.add('border-secondary-subtle', 'bg-light');
            }
        });

        // Toggle sección de empresa
        if (String(rol) === '3') {
            seccionEmpresa.classList.remove('d-none');
            razonSocialInput.setAttribute('required', 'required');
            nitInput.setAttribute('required', 'required');
            btnSubmit.textContent = 'Registrar Empresa y Activar Subroles';
            tituloDatosPersonales.textContent = 'Datos del Gerente / Administrador';
        } else {
            seccionEmpresa.classList.add('d-none');
            razonSocialInput.removeAttribute('required');
            nitInput.removeAttribute('required');
            btnSubmit.textContent = 'Completar Registro';
            tituloDatosPersonales.textContent = 'Datos de la Cuenta';
        }
    }

    roleCards.forEach(card => {
        card.addEventListener('click', function () {
            const rolVal = this.getAttribute('data-role-value');
            actualizarVistaRol(rolVal);
        });
    });

    // Inicializar estado según valor actual o old()
    actualizarVistaRol(rolInput.value || '2');
});
</script>
@endsection

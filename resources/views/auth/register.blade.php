@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-7 col-lg-6">
            
            <!-- Botón de Regreso a la Pantalla Anterior -->
            <div class="mb-3">
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : url('/') }}" 
                   onclick="if (window.history.length > 1) { window.history.back(); return false; }"
                   class="btn btn-outline-secondary btn-sm fw-bold px-3 py-2 shadow-sm rounded-3 d-inline-flex align-items-center gap-2 bg-white">
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
                    <p class="text-muted small mb-0">Elige tu rol y completa tus datos para empezar</p>
                </div>

                <div class="card-body p-4 p-sm-5 bg-white">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Selección de Rol -->
                        <div class="mb-4 bg-light p-3 rounded-3 border">
                            <label for="rol_id" class="form-label small fw-bold text-dark mb-2">Quiero registrarme como:</label>
                            <select id="rol_id" name="rol_id" class="form-select form-select-lg fs-6 fw-bold border-secondary-subtle @error('rol_id') is-invalid @enderror">
                                <option value="2" selected>Cliente / Visita (Ver catálogo y cotizar)</option>
                                <option value="1">Maestro Carpintero (Diseñar y publicar muebles)</option>
                            </select>
                            @error('rol_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <!-- Nombre Completo (Fila) -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label small fw-bold text-dark">Nombre(s)</label>
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
                                <label for="apellidoPaterno" class="form-label small fw-bold text-dark">Apellido Paterno</label>
                                <input id="apellidoPaterno" type="text" 
                                       class="form-control fs-6 @error('apellidoPaterno') is-invalid @enderror" 
                                       name="apellidoPaterno" value="{{ old('apellidoPaterno') }}" required placeholder="Pérez" style="border-radius: 10px;">
                                @error('apellidoPaterno')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="apellidoMaterno" class="form-label small fw-bold text-dark">Apellido Materno</label>
                                <input id="apellidoMaterno" type="text" 
                                       class="form-control fs-6 @error('apellidoMaterno') is-invalid @enderror" 
                                       name="apellidoMaterno" value="{{ old('apellidoMaterno') }}" required placeholder="Gómez" style="border-radius: 10px;">
                                @error('apellidoMaterno')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <!-- Contacto -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label small fw-bold text-dark">Teléfono / WhatsApp</label>
                                <input id="telefono" type="text" 
                                       class="form-control fs-6 @error('telefono') is-invalid @enderror" 
                                       name="telefono" value="{{ old('telefono') }}" required placeholder="+591 70000000" style="border-radius: 10px;">
                                @error('telefono')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold text-dark">Correo Electrónico</label>
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
                                <label for="password" class="form-label small fw-bold text-dark">Contraseña</label>
                                <input id="password" type="password" 
                                       class="form-control fs-6 @error('password') is-invalid @enderror" 
                                       name="password" required placeholder="••••••••" style="border-radius: 10px;">
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password-confirm" class="form-label small fw-bold text-dark">Confirmar Contraseña</label>
                                <input id="password-confirm" type="password" class="form-control fs-6" name="password_confirmation" required placeholder="••••••••" style="border-radius: 10px;">
                            </div>
                        </div>

                        <!-- Botón de Registro -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm py-2" style="border-radius: 10px;">
                                Completar Registro
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
@endsection
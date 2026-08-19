@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-7 col-lg-6">
            
            <!-- Botón de Regresar -->
            <div class="mb-3">
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm fw-bold border-0 d-inline-flex align-items-center gap-2">
                    ← Volver a la página principal
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
                            <label for="role" class="form-label small fw-bold text-dark mb-2">Quiero registrarme como:</label>
                            <select id="role" name="role" class="form-select form-select-lg fs-6 fw-bold border-secondary-subtle">
                                <option value="cliente" selected>Cliente / Visita (Ver catálogo y cotizar)</option>
                                <option value="maestro">Maestro Carpintero (Diseñar y publicar muebles)</option>
                            </select>
                        </div>

                        <!-- Nombre Completo (Fila) -->
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-bold text-dark">Nombre(s)</label>
                            <input id="name" type="text" 
                                   class="form-control form-control-lg fs-6 @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                   placeholder="Juan" style="border-radius: 10px;">
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="apellido_paterno" class="form-label small fw-bold text-dark">Apellido Paterno</label>
                                <input id="apellido_paterno" type="text" class="form-control fs-6" name="apellido_paterno" placeholder="Pérez" style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6">
                                <label for="apellido_materno" class="form-label small fw-bold text-dark">Apellido Materno</label>
                                <input id="apellido_materno" type="text" class="form-control fs-6" name="apellido_materno" placeholder="Gómez" style="border-radius: 10px;">
                            </div>
                        </div>

                        <!-- Contacto -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label small fw-bold text-dark">Teléfono / WhatsApp</label>
                                <input id="telefono" type="text" class="form-control fs-6" name="telefono" placeholder="+591 70000000" style="border-radius: 10px;">
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
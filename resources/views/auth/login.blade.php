@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5 col-lg-4">
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

            
            <!-- Tarjeta Principal de Login -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-sm-5">
                    
                    <!-- Encabezado con Marca -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark mb-1">¡Bienvenido de nuevo!</h3>
                        <p class="text-muted small">Ingresa tus credenciales para acceder a <span class="fw-bold text-dark">Mydo-mar</span></p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Campo: Correo Electrónico -->
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-dark">Correo Electrónico</label>
                            <input id="email" type="email" 
                                   class="form-control form-control-lg fs-6 @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                   placeholder="ejemplo@correo.com"
                                   style="border-radius: 10px;">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Campo: Contraseña -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label small fw-bold text-dark mb-0">Contraseña</label>
                                @if (Route::has('password.request'))
                                    <a class="small text-muted text-decoration-none" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>
                            <input id="password" type="password" 
                                   class="form-control form-control-lg fs-6 @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password" 
                                   placeholder="••••••••"
                                   style="border-radius: 10px;">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Recordarme -->
                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="remember">
                                Recordar mi sesión
                            </label>
                        </div>

                        <!-- Botón de Ingreso Principal -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm py-2" style="border-radius: 10px;">
                                Iniciar Sesión
                            </button>
                        </div>

                    </form>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Enlace a Registro -->
                    <div class="text-center">
                        <p class="small text-muted mb-0">¿Aún no tienes una cuenta?</p>
                        <a href="{{ route('register') }}" class="fw-bold text-dark text-decoration-none small">
                            Crea una cuenta aquí
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

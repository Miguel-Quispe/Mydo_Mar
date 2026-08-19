@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Botón Volver -->
    <div class="mb-4">
        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
            ← Volver al Catálogo
        </a>
    </div>

    <!-- Mensaje de éxito tras acciones (como agregar a favoritos) -->
    @if(session('exito_favorito'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            {{ session('exito_favorito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tarjeta Principal de Detalle -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white p-3 p-md-4">
        <div class="row g-4 align-items-center">
            
            <!-- Columna Izquierda: Fotografía -->
            <div class="col-lg-6">
                <div class="position-relative overflow-hidden rounded-3 border bg-light d-flex align-items-center justify-content-center" style="min-height: 380px;">
                    @if($mueble->imagen_url)
                        <img src="{{ asset('storage/' . $mueble->imagen_url) }}" 
                             alt="{{ $mueble->nombre }}" 
                             class="img-fluid w-100 object-fit-cover d-block" 
                             style="max-height: 480px;">
                    @else
                        <div class="text-center p-5 text-secondary opacity-50">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M3 9h18M9 3v18"/>
                            </svg>
                            <p class="mt-2 mb-0 small fw-bold text-uppercase">Sin imagen</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha: Información y Acciones -->
            <div class="col-lg-6 d-flex flex-column justify-content-between">
                <div>
                    <!-- Contador de Vistas y Título -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h2 class="fw-bold text-dark text-capitalize mb-0 display-6">
                            {{ $mueble->nombre }}
                        </h2>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small d-inline-flex align-items-center gap-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            {{ $mueble->vistas ?? 0 }} vistas
                        </span>
                    </div>

                    <!-- Maestro / Diseñador -->
                    <div class="bg-light p-3 rounded-3 mb-4 border-start border-4 border-warning">
                        <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Diseñado y Creado por:</span>
                        <span class="fw-bold text-dark">Maestro {{ $mueble->user->nombre ?? 'Carpintero' }} {{ $mueble->user->apellido_paterno ?? '' }}</span>
                    </div>

                    <hr class="my-3 opacity-25">

                    <!-- Parámetros / Especificaciones -->
                    @if($mueble->parametros_diseno)
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            Parámetros / Dimensiones
                        </h6>
                        <div class="p-3 bg-light rounded-3 border text-dark fw-medium small">
                            {{ $mueble->parametros_diseno }}
                        </div>
                    </div>
                    @endif

                    <!-- Descripción Detailed -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            Descripción
                        </h6>
                        <p class="text-secondary lh-lg mb-0 small">
                            {{ $mueble->descripcion ?? 'Sin descripción detallada disponible para este modelo.' }}
                        </p>
                    </div>
                </div>

                <!-- Botones de Acción (Contacto y Favoritos) -->
                <div class="pt-3 border-top d-flex flex-column flex-sm-row gap-2">
                    
                    <!-- Botón de WhatsApp -->
                    @if($linkWhatsapp)
                        <a href="{{ $linkWhatsapp }}" 
                           target="_blank" 
                           class="btn btn-success btn-lg fw-bold flex-grow-1 shadow-sm py-2 d-flex align-items-center justify-content-center gap-2" 
                           style="border-radius: 10px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            Contactar por WhatsApp
                        </a>
                    @else
                        <button class="btn btn-secondary btn-lg fw-bold flex-grow-1 py-2" style="border-radius: 10px;" disabled>
                            Contacto no disponible
                        </button>
                    @endif

                    <!-- Botón Guardar en Favoritos -->
                    @auth
                        <form action="{{ route('favoritos.toggle', $mueble->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning btn-lg fw-bold text-dark w-100 py-2 h-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                Favorito
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-warning btn-lg fw-bold text-dark w-100 py-2 h-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            Favorito
                        </a>
                    @endauth

                </div>

            </div>
        </div>
    </div>

</div>
@endsection
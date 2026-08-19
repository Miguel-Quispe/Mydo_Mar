@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Encabezado de la Sección -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">Catálogo de Muebles</h2>
        <p class="text-muted small mb-0">Explora diseños únicos elaborados por nuestros Maestros Carpinteros.</p>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-5">
        <form action="{{ route('catalogo.index') }}" method="GET">
            <div class="row g-2 align-items-center">
                <!-- Campo de Búsqueda -->
                <div class="col-md-7">
                    <input type="text" 
                           name="buscar" 
                           class="form-control form-control-lg fs-6" 
                           placeholder="Buscar por nombre, tipo de mueble o descripción..." 
                           value="{{ request('buscar') }}" 
                           style="border-radius: 10px;">
                </div>

                <!-- Ordenar Por -->
                <div class="col-md-3">
                    <select name="orden" class="form-select form-select-lg fs-6 text-secondary" style="border-radius: 10px;">
                        <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
                        <option value="populares" {{ request('orden') == 'populares' ? 'selected' : '' }}>Más populares</option>
                        <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre (A-Z)</option>
                    </select>
                </div>

                <!-- Botón Buscar -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark w-100 fs-6 shadow-sm" style="border-radius: 10px;">
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Grid de Tarjetas de Muebles -->
    <div class="row g-4">
        @forelse($muebles as $mueble)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white d-flex flex-column">
                    
                    <!-- Contenedor de Imagen -->
                    <div class="position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
                        @if($mueble->imagen_url)
                            <img src="{{ asset('storage/' . $mueble->imagen_url) }}" 
                                 class="card-img-top w-100 h-100 object-fit-cover d-block" 
                                 alt="{{ $mueble->nombre }}">
                        @else
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-secondary opacity-50">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M3 9h18M9 3v18"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Cuerpo de la Tarjeta -->
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="fw-bold text-dark mb-1 text-capitalize">
                            {{ $mueble->nombre }}
                        </h5>
                        
                        <p class="card-text text-muted small flex-grow-1 mb-3">
                            {{ Str::limit($mueble->descripcion, 65) }}
                        </p>

                        <!-- Botones de Acción (Guardar / Detalles) -->
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-3 border-top mb-3">
                            
                            @auth
                                <!-- Verificación de Estado Guardado -->
                                @if(isset($favoritosIds) && in_array($mueble->id, $favoritosIds))
                                    <!-- Estado: Ya Guardado (al hacer clic lo quita) -->
                                    <form action="{{ route('favoritos.toggle', $mueble->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm fw-bold px-3 py-2 opacity-100 d-flex align-items-center gap-1" style="border-radius: 8px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                                            Guardado
                                        </button>
                                    </form>
                                @else
                                    <!-- Estado: Disponible para Guardar -->
                                    <form action="{{ route('favoritos.toggle', $mueble->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-1" style="border-radius: 8px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                            Guardar
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm fw-bold px-3 py-2 d-flex align-items-center gap-1" style="border-radius: 8px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                    Guardar
                                </a>
                            @endauth

                            <!-- Botón Ver Detalles -->
                            <a href="{{ route('catalogo.show', $mueble->id) }}" class="btn btn-outline-warning btn-sm fw-bold text-dark px-3 py-2" style="border-radius: 8px;">
                                Ver Detalles
                            </a>
                        </div>

                        <!-- Pie de Tarjeta (Fecha) -->
                        <div class="text-end">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                Publicado: {{ $mueble->created_at ? $mueble->created_at->format('d/m/Y') : '08/08/2026' }}
                            </span>
                        </div>

                    </div>

                </div>
            </div>
        @empty
            <!-- Mensaje en caso de no encontrar resultados -->
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm rounded-4 p-5 bg-white d-inline-block" style="max-width: 500px;">
                    <h5 class="fw-bold text-dark mb-2">No se encontraron muebles</h5>
                    <p class="text-muted small mb-0">Intenta buscar con otros términos o limpia los filtros aplicados.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($muebles->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $muebles->links() }}
        </div>
    @endif

</div>
@endsection
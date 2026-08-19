@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Encabezado y Botón Volver -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Mis Muebles Favoritos</h2>
            <p class="text-muted small mb-0">Colección de diseños guardados para tus próximos proyectos.</p>
        </div>
        <div>
            <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
                ← Volver al Catálogo
            </a>
        </div>
    </div>

    @if(session('exito_favorito'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            {{ session('exito_favorito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Grid de Muebles Favoritos -->
    <div class="row g-4">
        @forelse($favoritos as $mueble)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white">
                    
                    <!-- Imagen del Mueble -->
                    <div class="position-relative">
                        @if($mueble->imagen_url)
                            <img src="{{ asset('storage/' . $mueble->imagen_url) }}" 
                                 class="card-img-top object-fit-cover" 
                                 alt="{{ $mueble->nombre }}" 
                                 style="height: 200px;">
                        @else
                            <div class="bg-light border-bottom d-flex align-items-center justify-content-center" style="height: 200px;">
                                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-secondary opacity-50">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <path d="M3 9h18M9 3v18"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Contenido -->
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="fw-bold text-dark mb-2 text-capitalize">
                            {{ $mueble->nombre }}
                        </h5>
                        <p class="card-text text-muted small flex-grow-1 mb-3">
                            {{ Str::limit($mueble->descripcion, 70) }}
                        </p>

                        <!-- Botones de Acción -->
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-2 border-top">
                            <!-- Botón Quitar de Favoritos -->
                            <form action="{{ route('favoritos.toggle', $mueble->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3 py-2" style="border-radius: 8px;">
                                    Quitar
                                </button>
                            </form>

                            <!-- Botón Ver Detalles -->
                            <a href="{{ route('catalogo.show', $mueble->id) }}" class="btn btn-warning btn-sm fw-bold text-dark px-3 py-2 shadow-sm" style="border-radius: 8px;">
                                Ver Detalles
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <!-- Estado Vacío (Sin Favoritos) -->
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm rounded-4 p-5 bg-white d-inline-block" style="max-width: 500px;">
                    <h5 class="fw-bold text-dark mb-2">Aún no tienes favoritos guardados</h5>
                    <p class="text-muted small mb-4">Explora nuestro catálogo y guarda los diseños que más te gusten para verlos aquí.</p>
                    <a href="{{ route('catalogo.index') }}" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm" style="border-radius: 10px;">
                        Explorar Catálogo
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($favoritos->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $favoritos->links() }}
        </div>
    @endif

</div>
@endsection

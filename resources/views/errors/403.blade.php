@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-6 col-lg-5 text-center">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-sm-5 bg-white">
                <h1 class="display-1 fw-bold text-danger mb-0">403</h1>
                <h3 class="fw-bold text-dark mb-2">Acceso Restringido</h3>
                <p class="text-muted small mb-4">No tienes los permisos necesarios para acceder a esta sección de la plataforma.</p>
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
                        Volver al Inicio
                    </a>
                    <a href="{{ route('catalogo.index') }}" class="btn btn-warning fw-bold text-dark px-3 py-2 shadow-sm" style="border-radius: 10px;">
                        Explorar Catálogo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Mensaje de éxito tras guardar, editar o eliminar -->
    @if(session('exito'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <strong>¡Excelente!</strong> {{ session('exito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. BANNER DE BIENVENIDA -->
    <div class="card bg-dark text-white border-0 rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-2">
                    Clave Rol: {{ $user->clave_rol }}
                </span>
                <h2 class="fw-bold mb-1">¡Bienvenido, Maestro {{ $user->nombre }}!</h2>
                <p class="text-secondary mb-0 small">Panel de administración de tus diseños y proyectos de carpintería.</p>
            </div>
            <div>
                <a href="{{ route('muebles.create') }}" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm px-4 py-2 rounded-3 text-nowrap d-inline-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Crear Nuevo Mueble
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TARJETAS DE METRICAS (STAT CARDS) -->
    <div class="row g-3 mb-4">
        <!-- Métrica 1 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Mis Muebles Creados</p>
                        <h2 class="fw-bold text-dark mb-0">{{ $totalMuebles }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métrica 2 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Teléfono de Contacto</p>
                        <h4 class="fw-bold text-dark mb-0">{{ $user->telefono }}</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métrica 3 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Correo Registrado</p>
                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">{{ $user->email }}</h6>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. TABLA DE MUEBLES RECIENTES -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        
        <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">Muebles Recientes</h5>
        </div>

        <div class="card-body p-0">
            @if($muebles->isEmpty())
                <div class="text-center py-5 text-muted">
                    <p class="fs-5 mb-2">Aún no has registrado ningún mueble.</p>
                    <small>Haz clic en "Crear Nuevo Mueble" para subir tu primer diseño.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Imagen</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Vistas / Precio</th>
                                <th>Fecha</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($muebles as $mueble)
                            <tr>
                                <td class="ps-4">
                                    @if($mueble->imagen_url)
                                        <img src="{{ asset('storage/' . $mueble->imagen_url) }}" class="rounded-3 border object-fit-cover" width="60" height="60" alt="{{ $mueble->nombre }}">
                                    @else
                                        <div class="bg-light text-secondary border rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                <path d="M3 9h18M9 3v18"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">{{ $mueble->nombre }}</td>
                                <td class="text-muted small">{{ Str::limit($mueble->descripcion, 40) }}</td>
                                <td>
                                    @if(isset($mueble->precio))
                                        <span class="text-success fw-bold">Bs. {{ number_format($mueble->precio, 2) }}</span>
                                    @else
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            {{ $mueble->vistas }} vistas
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $mueble->created_at->format('d/m/Y') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group gap-1">
                                        <a href="{{ route('maestro.muebles.edit', $mueble->id) }}" class="btn btn-outline-warning btn-sm fw-bold text-dark rounded-2">Editar</a>
                                        
                                        <form action="{{ route('maestro.muebles.destroy', $mueble->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este mueble? Esta acción no se puede deshacer.');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold rounded-2">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
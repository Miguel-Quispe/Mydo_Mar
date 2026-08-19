@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Mensajes de Alerta -->
    @if(session('exito'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('exito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Encabezado -->
    <div class="p-4 bg-primary text-white rounded shadow-sm mb-4">
        <h2 class="fw-bold mb-1">Panel de Control General</h2>
        <p class="mb-0 opacity-75">Gestión de usuarios, publicaciones y configuración del sistema.</p>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold fs-7">Total Usuarios</h6>
                    <h3 class="fw-bold text-primary mb-0">{{ $totalUsuarios }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold fs-7">Admins</h6>
                    <h3 class="fw-bold text-danger mb-0">{{ $totalAdmins }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold fs-7">Maestros</h6>
                    <h3 class="fw-bold text-success mb-0">{{ $totalMaestros }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold fs-7">Clientes</h6>
                    <h3 class="fw-bold text-info mb-0">{{ $totalClientes }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold fs-7">Muebles</h6>
                    <h3 class="fw-bold text-warning mb-0">{{ $totalMuebles }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación por Pestañas (Tabs) -->
    <ul class="nav nav-tabs fw-bold mb-4" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-dark" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">
                Gestión de Usuarios ({{ $totalUsuarios }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="muebles-tab" data-bs-toggle="tab" data-bs-target="#muebles" type="button">
                Gestión de Muebles ({{ $totalMuebles }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="perfil-tab" data-bs-toggle="tab" data-bs-target="#perfil" type="button">
                Mi Cuenta Admin
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">

        <!-- PESTAÑA 1: GESTIÓN DE USUARIOS -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Rol / Clave</th>
                                    <th>Es Admin</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $u)
                                <tr>
                                    <td class="text-muted small">{{ $u->id }}</td>
                                    <td class="fw-bold">{{ $u->nombre }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->telefono ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $u->clave_rol ?? 'Cliente' }}</span>
                                    </td>
                                    <td>
                                        @if($u->admin)
                                            <span class="badge bg-danger">Administrador</span>
                                        @else
                                            <span class="badge bg-light text-dark border">Usuario</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Otorgar / Quitar Admin -->
                                            <form action="{{ route('admin.users.toggleAdmin', $u->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $u->admin ? 'btn-outline-warning' : 'btn-outline-primary' }}">
                                                    {{ $u->admin ? 'Quitar Admin' : 'Hacer Admin' }}
                                                </button>
                                            </form>

                                            <!-- Eliminar Usuario -->
                                            @if($u->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Confirma eliminar a {{ $u->nombre }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-light text-muted border">Tú</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- PESTAÑA 2: GESTIÓN DE MUEBLES -->
        <div class="tab-pane fade" id="muebles" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre del Mueble</th>
                                    <th>Publicado Por (Maestro)</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($muebles as $m)
                                <tr>
                                    <td class="text-muted small">{{ $m->id }}</td>
                                    <td style="width: 60px;">
                                        @if($m->imagen_url)
                                            <img src="{{ asset('storage/' . $m->imagen_url) }}" class="rounded object-fit-cover" style="width: 44px; height: 44px;">
                                        @else
                                            <div class="bg-light rounded text-center py-2 text-muted small" style="width: 44px;">—</div>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ $m->nombre }}</td>
                                    <td>{{ $m->user->nombre ?? 'Desconocido' }}</td>
                                    <td class="text-muted small">{{ $m->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.muebles.destroy', $m->id) }}" method="POST" onsubmit="return confirm('¿Eliminar la publicación: {{ $m->nombre }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $muebles->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- PESTAÑA 3: PERFIL DEL ADMIN -->
        <div class="tab-pane fade" id="perfil" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre de Administrador</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $user->nombre) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <input type="text" class="form-control bg-light" value="Administrador Principal (admin=true)" readonly>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para conservar la actual">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4">Actualizar Administrador</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand fw-bold fs-4 text-warning" href="{{ url('/') }}">
                    Mydo-mar
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center gap-2">
                        
                        <!-- Enlace público -->
                        <li class="nav-item">
                            <a href="{{ route('catalogo.index') }}" class="nav-link fw-semibold">Explorar Catálogo</a>
                        </li>

                        @auth
                            <!-- Si es Administrador Principal -->
                            @if(auth()->user()->admin)
                                <li class="nav-item">
                                    <a href="{{ route('admin.perfil') }}" class="btn btn-danger fw-bold px-3">Panel Admin</a>
                                </li>
                            
                                <!-- Si es Maestro Carpintero -->
                            @elseif(auth()->user()->rol_id == 1 || str_starts_with(auth()->user()->clave_rol ?? '', 'MAE'))
                                <li class="nav-item">
                                    <a href="{{ route('maestro.dashboard') }}" class="btn btn-warning fw-bold px-3">Mi Panel (Maestro)</a>
                                </li>

                            <!-- Si es Cliente -->
                            @else
                                <li class="nav-item">
                                    <a href="{{ route('favoritos.index') }}" class="btn btn-outline-warning fw-bold px-3">Mis Favoritos</a>
                                </li>
                            @endif

                            <!-- Menú Desplegable de Perfil -->
                            <li class="nav-item ms-2">
                                <div class="dropdown">
                                    <button class="btn text-white fw-semibold d-flex align-items-center gap-2 border-0 bg-transparent dropdown-toggle shadow-none" 
                                            type="button" 
                                            id="userDropdown" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <!-- Ícono de usuario -->
                                        <span class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle" style="width: 32px; height: 32px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#111827" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                            </svg>
                                        </span>
                                        <span class="text-warning fw-bold">Mi Perfil</span>
                                    </button>
                                    
                                    <!-- Opciones Desplegables -->
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 p-2" aria-labelledby="userDropdown" style="min-width: 180px;">
                                        <li>
                                            <a class="dropdown-item fw-semibold rounded-2 py-2 d-flex align-items-center gap-2" href="{{ route('perfil.index') }}">
                                                Editar Perfil
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1 text-muted opacity-25"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item fw-semibold text-danger rounded-2 py-2 d-flex align-items-center gap-2">
                                                    Cerrar Sesión
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @else
                            <!-- Opciones de Invitado -->
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-outline-light me-1 fw-bold">Iniciar Sesión</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-warning fw-bold">Registrarse</a>
                                </li>
                            @endif
                        @endauth

                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>

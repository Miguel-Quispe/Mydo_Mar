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
        /* Botón de regreso con animación interactiva */
        .btn-regresar-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151 !important;
            background-color: #ffffff !important;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            text-decoration: none !important;
            transition: all 0.22s ease-in-out;
            cursor: pointer;
        }
        .btn-regresar-back svg {
            transition: transform 0.22s ease-in-out;
            color: #4b5563;
        }
        .btn-regresar-back:hover {
            color: #111827 !important;
            background-color: #f3f4f6 !important;
            border-color: #9ca3af;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12) !important;
            transform: translateX(-4px);
        }
        .btn-regresar-back:hover svg {
            transform: translateX(-3px);
            color: #111827;
        }
        .btn-regresar-back:active {
            transform: scale(0.97);
        }
    
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
                        
                        <!-- Enlaces públicos y módulos B2B -->
                        <li class="nav-item">
                            <a href="{{ route('catalogo.index') }}" class="nav-link fw-semibold">Explorar Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('empresa.empleados.index') }}" class="nav-link fw-semibold {{ request()->routeIs('empresa.*') ? 'active text-warning' : 'text-white-50' }} d-flex align-items-center gap-1">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Madeart B2B
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('empresa.auditoria.index') }}" class="nav-link fw-semibold {{ request()->routeIs('empresa.auditoria.*') ? 'active text-warning' : 'text-white-50' }} d-flex align-items-center gap-1">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Auditoría Forense
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('taller.index') }}" class="nav-link fw-semibold {{ request()->routeIs('taller.*') ? 'active text-warning' : 'text-white-50' }} d-flex align-items-center gap-1">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Terminal de Taller
                            </a>
                        </li>

                        @auth
                            <!-- Si es Administrador Principal -->
                            @if(auth()->user()->admin)
                                <li class="nav-item">
                                    <a href="{{ route('admin.perfil') }}" class="btn btn-danger fw-bold px-3">Panel Admin</a>
                                </li>
                            
                                <!-- Si es Empresa B2B / Gerente -->
                            @elseif(auth()->user()->rol_id == 3 || str_starts_with(auth()->user()->clave_rol ?? '', 'EMP'))
                                <li class="nav-item">
                                    <a href="{{ route('empresa.empleados.index') }}" class="btn btn-warning fw-bold px-3 d-flex align-items-center gap-1 shadow-sm">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                        <span>Gestión Empresa B2B</span>
                                    </a>
                                </li>
                            <!-- Si es Maestro Carpintero -->
                            @elseif(auth()->user()->rol_id == 1 || str_starts_with(auth()->user()->clave_rol ?? '', 'MAE') || str_starts_with(auth()->user()->clave_rol ?? '', 'MST'))
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
                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1m5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
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

    <!-- Modal de Notificación Centrado -->
    <div id="notification-modal" class="d-none">
        <div class="notification-content">
            <div id="notification-icon"></div>
            <div id="notification-message"></div>
        </div>
    </div>

    <style>
        #notification-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease-in-out;
        }

        .notification-content {
            background: white;
            padding: 40px 60px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: scaleIn 0.3s ease-in-out;
        }

        #notification-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            position: relative;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 4;
            stroke: #28a745;
            stroke-miterlimit: 10;
            animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
        }

        .checkmark circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 4;
            stroke-miterlimit: 10;
            stroke: #28a745;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .checkmark path {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        .crossmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 4;
            stroke: #dc3545;
            stroke-miterlimit: 10;
            animation: fillError 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
        }

        .crossmark circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 4;
            stroke-miterlimit: 10;
            stroke: #dc3545;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .crossmark path {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: strokeCross 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        #notification-message {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }

        @keyframes strokeCross {
            100% { stroke-dashoffset: 0; }
        }

        @keyframes scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }

        @keyframes fill {
            100% { box-shadow: inset 0px 0px 0px 40px #28a745; }
        }

        @keyframes fillError {
            100% { box-shadow: inset 0px 0px 0px 40px #dc3545; }
        }
    </style>

    <script>
        function showNotification(type, message) {
            const modal = document.getElementById('notification-modal');
            const icon = document.getElementById('notification-icon');
            const msg = document.getElementById('notification-message');

            modal.classList.remove('d-none');
            msg.textContent = message;

            if (type === 'success') {
                icon.innerHTML = `
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="25" fill="none"/>
                        <path fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                `;
            } else if (type === 'error') {
                icon.innerHTML = `
                    <svg class="crossmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="25" fill="none"/>
                        <path fill="none" d="M16 16 L36 36 M36 16 L16 36"/>
                    </svg>
                `;
            }

            setTimeout(() => {
                modal.classList.add('d-none');
            }, 2000);
        }

        // Detectar si hay errores en el formulario de login
        document.addEventListener('DOMContentLoaded', function() {
            var showLoginError = {{ session('login_error') ? 'true' : 'false' }};
            var showJustLoggedIn = {{ session('just_logged_in') ? 'true' : 'false' }};
            var showRegisterSuccess = {{ session('success') ? 'true' : 'false' }};

            if (showLoginError) {
                setTimeout(function() {
                    showNotification('error', 'Credenciales incorrectas');
                }, 300);
            }

            if (showJustLoggedIn) {
                setTimeout(function() {
                    showNotification('success', '¡Sesión iniciada correctamente!');
                }, 500);
            }

            if (showRegisterSuccess) {
                setTimeout(function() {
                    showNotification('success', '¡Registro exitoso!');
                }, 300);
            }
        });
    </script>
</body>
</html>

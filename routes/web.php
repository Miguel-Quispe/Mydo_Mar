<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\CheckRol;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\MuebleController;

// --- RUTAS PÚBLICAS ---
Route::get('/', [WelcomeController::class, 'index'])->name('inicio');
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/catalogo/{id}', [CatalogoController::class, 'show'])->name('catalogo.show');

// Rutas de autenticación (Login, Registro)
Auth::routes();

// --- RUTAS PROTEGIDAS POR AUTENTICACIÓN Y ROL ---
Route::middleware(['auth'])->group(function () {

    // Ruta /home general
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas accesibles para cualquier usuario autenticado (Clientes / Maestros)
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // Rutas EXCLUSIVAS para el Administrador (admin = true)
    Route::middleware(['es_admin'])->group(function () {
        Route::get('/admin/perfil', [PerfilController::class, 'adminDashboard'])->name('admin.perfil');

        // Acciones sobre Usuarios
        Route::patch('/admin/users/{id}/toggle-admin', [PerfilController::class, 'toggleAdmin'])->name('admin.users.toggleAdmin');
        Route::delete('/admin/users/{id}', [PerfilController::class, 'destroyUser'])->name('admin.users.destroy');

        // Acciones sobre Muebles
        Route::delete('/admin/muebles/{id}', [PerfilController::class, 'destroyMueble'])->name('admin.muebles.destroy');
    });

    // Favoritos (Acceso para Clientes - rol_id 2)
    Route::middleware([CheckRol::class . ':2'])->group(function () {
        // Favoritos
        Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
        Route::post('/favoritos/{id}', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    });

    // Panel del Maestro Carpintero (Exclusivo rol_id 1)
    Route::middleware([CheckRol::class . ':1'])->group(function () {
    Route::get('/maestro/dashboard', [MaestroController::class, 'dashboard'])->name('maestro.dashboard');
    
    // Publicación (Ahora usando MuebleController)
    Route::get('/muebles/crear', [MuebleController::class, 'create'])->name('muebles.create');
    Route::post('/muebles', [MuebleController::class, 'store'])->name('muebles.store');

    // Edición y Eliminación
    Route::get('/maestro/muebles/{id}/editar', [MaestroController::class, 'edit'])->name('maestro.muebles.edit');
    Route::put('/maestro/muebles/{id}', [MaestroController::class, 'update'])->name('maestro.muebles.update');
    Route::delete('/maestro/muebles/{id}', [MaestroController::class, 'destroy'])->name('maestro.muebles.destroy');
    });

    // Panel del Administrador (Exclusivo rol_id 0)
    Route::middleware([CheckRol::class . ':0'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('home'); 
        })->name('admin.dashboard');
    });

});
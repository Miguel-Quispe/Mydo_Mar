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
use App\Http\Controllers\EmpresaEmpleadoController;
use App\Http\Controllers\EmpresaAuditoriaController;
use App\Http\Controllers\TallerArmadoController;

// --- RUTAS PÚBLICAS ---
Route::get('/', [WelcomeController::class, 'index'])->name('inicio');
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/catalogo/{id}', [CatalogoController::class, 'show'])->name('catalogo.show');
Route::get('/catalogo/{id}/guia-armado', [CatalogoController::class, 'guiaArmado'])->name('catalogo.armado');

// --- MÓDULO B2B CORPORATIVO (MADEART & AUDITORÍA FORENSE - SOLO GERENTES/ADMINS) ---
Route::middleware(['auth', 'b2b_gerente'])->prefix('empresa')->name('empresa.')->group(function () {
    // Módulo A: Gestión Masiva de Personal y Subroles
    Route::get('/empleados', [EmpresaEmpleadoController::class, 'index'])->name('empleados.index');
    Route::post('/empleados/generar-lote', [EmpresaEmpleadoController::class, 'generarLote'])->name('empleados.generarLote');
    Route::post('/empleados/{id}/reset-password', [EmpresaEmpleadoController::class, 'resetPassword'])->name('empleados.resetPassword');
    Route::patch('/empleados/{id}/cambiar-subrol', [EmpresaEmpleadoController::class, 'cambiarSubrol'])->name('empleados.cambiarSubrol');

    // Módulo B: Consola de Auditoría Forense (Append-Only)
    Route::get('/auditoria', [EmpresaAuditoriaController::class, 'index'])->name('auditoria.index');
});

// --- MÓDULO TALLER Y OPERARIOS DE PLANTA (SOLO ARMADORES/SUPERVISORES/PLANTA) ---
Route::middleware(['auth', 'armador_taller'])->prefix('taller')->name('taller.')->group(function () {
    Route::get('/', [TallerArmadoController::class, 'index'])->name('index');
    Route::get('/armado/{id}', [TallerArmadoController::class, 'show'])->name('armado');
    Route::post('/armado/{id}/progreso', [TallerArmadoController::class, 'actualizarProgreso'])->name('progreso');
    Route::get('/armado/{id}/plano-pdf', [TallerArmadoController::class, 'exportarPlanoPdf'])->name('plano-pdf');
});

// Endpoint API para cálculo BOM en tiempo real
Route::post('/api/muebles/calcular-bom', [MuebleController::class, 'calcularBom'])->name('api.muebles.bom');

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
        Route::get('/maestro/muebles/{id}/editar', [MaestroController::class, 'edit'])->name('maestro.muebles.edit');
        Route::put('/maestro/muebles/{id}', [MaestroController::class, 'update'])->name('maestro.muebles.update');
        Route::delete('/maestro/muebles/{id}', [MaestroController::class, 'destroy'])->name('maestro.muebles.destroy');
    });

    // Diseñador 3D Paramétrico y Persistencia de Modelos (Diseñadores 3D, Maestros y Admins)
    Route::middleware(['disenador_3d'])->group(function () {
        Route::get('/muebles/crear', [MuebleController::class, 'create'])->name('muebles.create');
        Route::post('/muebles', [MuebleController::class, 'store'])->name('muebles.store');
    });

    // Panel del Administrador (Exclusivo rol_id 0)
    Route::middleware([CheckRol::class . ':0'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('home'); 
        })->name('admin.dashboard');
    });

});
// --- MÓDULO TARIFARIO DE MATERIALES Y COSTOS (EXCLUSIVO MAESTROS Y EMPRESAS) ---
use App\Http\Controllers\MaterialCostoController;
Route::middleware(['auth'])->prefix('materiales')->name('materiales.')->group(function () {
    Route::get('/costos', [MaterialCostoController::class, 'index'])->name('costos');
    Route::put('/costos/material/{id}', [MaterialCostoController::class, 'updateMaterial'])->name('updateMaterial');
    Route::put('/costos/herraje/{id}', [MaterialCostoController::class, 'updateHerraje'])->name('updateHerraje');
});

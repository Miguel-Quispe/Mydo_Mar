<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Redirige al usuario a su panel correspondiente según su rol.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = auth()->user();

        // Prioridad máxima: Si es Administrador (admin = true)
        if ($user && $user->admin) {
            return redirect()->route('admin.perfil');
        }

        // Si es Maestro Carpintero (rol_id = 1)
        if ($user && $user->rol_id == 1) {
            return redirect()->route('maestro.dashboard');
        }

        // Si es Cliente (rol_id = 2) o cualquier otro, lo mandamos al catálogo
        return redirect()->route('catalogo.index');
    }
}
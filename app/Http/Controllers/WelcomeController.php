<?php

namespace App\Http\Controllers;

use App\Models\Mueble;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    /**
     * Muestra la landing page a visitantes o redirige al panel si ya ingresaron.
     */
    public function index()
    {
        // Si el usuario ya inició sesión, lo mandamos a su panel correspondiente
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->admin) {
                return redirect()->route('admin.perfil');
            }

            if ($user->rol_id == 1) {
                return redirect()->route('maestro.dashboard');
            }

            return redirect()->route('catalogo.index');
        }

        // Visitantes: obtener los últimos 6 muebles públicos para la sección destacada
        $mueblesDestacados = Mueble::where('es_publico', true)
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('welcome', compact('mueblesDestacados'));
    }
}

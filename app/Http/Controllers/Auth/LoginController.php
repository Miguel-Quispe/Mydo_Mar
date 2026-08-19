<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Redirección según el rol_id del usuario después del login.
     */
    protected function authenticated($request, $user)
    {
        // Redirección según el rol_id del usuario
        if ($user->rol_id == 0) {
            return redirect()->route('admin.dashboard'); // Administrador
        }

        if ($user->rol_id == 1) {
            return redirect()->route('maestro.dashboard'); // Maestro Carpintero
        }

        // Por defecto (rol_id = 2 u otros) manda al catálogo público
        return redirect()->route('catalogo.index'); 
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    /**
     * Maneja una petición entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $rol  El ID del rol permitido
     */
    public function handle(Request $request, Closure $next, int $rol): Response
    {
        // Si el usuario es Admin, siempre lo mandamos a su panel exclusivo
        if (auth()->check() && auth()->user()->admin) {
            return redirect()->route('admin.perfil');
        }

        // Si el usuario está autenticado y su rol coincide
        if (auth()->check() && auth()->user()->rol_id == $rol) {
            return $next($request);
        }

        // Si no cumple con el rol, rebotarlo con un aviso de no autorizado
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
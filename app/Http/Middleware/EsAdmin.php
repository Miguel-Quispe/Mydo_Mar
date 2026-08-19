<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->admin == true) {
            return $next($request);
        }

        abort(403, 'Acceso denegado. Se requieren permisos de administrador.');
    }
}

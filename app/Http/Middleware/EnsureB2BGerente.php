<?php

namespace App\Http\Middleware;

use App\Models\UserCuenta;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureB2BGerente
{
    /**
     * Garantiza que solo usuarios con rol de Gerente de Empresa o Administrador Global
     * puedan acceder al módulo B2B corporativo y consola de auditoría forense.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('alerta', 'Debe iniciar sesión para acceder al módulo corporativo.');
        }

        $user = auth()->user();

        // 1. Si es Administrador del sistema
        if ($user->admin || ($user->rol_id === 0)) {
            return $next($request);
        }

        // 2. Verificar cuenta corporativa B2B por correo o relación
        try {
            $cuenta = UserCuenta::where('email_usuario_users', $user->email)->first();
        } catch (\Throwable $e) {
            $cuenta = null;
        }

        if ($cuenta && $cuenta->id_empresa_users && in_array($cuenta->id_rol_users, [1, 2])) {
            // Rol 1: Admin Global, Rol 2: Gerente Empresa
            return $next($request);
        }

        // 3. Si no cumple con los privilegios, denegar acceso de forma limpia
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acceso denegado. Se requiere rol de Gerente de Empresa o Administrador Global.',
            ], 403);
        }

        abort(403, 'Acceso denegado. Este módulo está restringido exclusivamente a Gerentes de Empresa y Administradores.');
    }
}

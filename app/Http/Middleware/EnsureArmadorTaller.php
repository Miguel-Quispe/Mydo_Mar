<?php

namespace App\Http\Middleware;

use App\Models\UserCuenta;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureArmadorTaller
{
    /**
     * Garantiza que solo operarios autorizados (Armadores, Supervisores de Planta, Maestros o Gerentes)
     * puedan acceder al terminal de taller y actualizar el progreso de ensamblaje.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('alerta', 'Debe iniciar sesión para acceder al terminal de taller.');
        }

        $user = auth()->user();

        // 1. Si es Administrador o Maestro Carpintero
        if ($user->admin || in_array($user->rol_id, [0, 1])) {
            return $next($request);
        }

        // 2. Verificar cuenta corporativa B2B
        try {
            $cuenta = UserCuenta::where('email_usuario_users', $user->email)->first();
        } catch (\Throwable $e) {
            $cuenta = null;
        }

        if ($cuenta) {
            // Permitido para: Subrol 1 (Armador de Muebles), Subrol 3 (Supervisor de Calidad) o Rol Global 2 (Gerente Empresa)
            if (in_array($cuenta->id_empresa_rol, [1, 3]) || in_array($cuenta->id_rol_users, [1, 2])) {
                return $next($request);
            }
        }

        // 3. Denegar acceso
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acceso denegado. Se requiere perfil de Armador de Muebles, Supervisor o Gerente de Planta.',
            ], 403);
        }

        abort(403, 'Acceso denegado. La terminal de taller está restringida a Armadores de Muebles y Supervisores de planta.');
    }
}

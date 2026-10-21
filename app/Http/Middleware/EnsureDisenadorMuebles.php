<?php

namespace App\Http\Middleware;

use App\Models\UserCuenta;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDisenadorMuebles
{
    /**
     * Garantiza que solo Diseñadores 3D, Maestros Carpinteros o Administradores
     * puedan acceder al modelador paramétrico y crear/editar diseños técnicos.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('alerta', 'Debe iniciar sesión para ingresar al diseñador 3D.');
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
            // Permitido para: Subrol 2 (Diseñador 3D / Modulador) o Rol Global 1 o 2 (Admin / Gerente)
            if ($cuenta->id_empresa_rol === 2 || in_array($cuenta->id_rol_users, [1, 2])) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acceso denegado. Se requiere perfil de Diseñador 3D, Maestro o Administrador.',
            ], 403);
        }

        abort(403, 'Acceso denegado. La creación de proyectos 3D está restringida a Diseñadores 3D y Maestros Carpinteros.');
    }
}

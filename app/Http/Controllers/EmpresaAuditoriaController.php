<?php

namespace App\Http\Controllers;

use App\Models\DimTiempo;
use App\Models\UserCredencialAuditoria;
use App\Models\UserCuenta;
use Illuminate\Http\Request;

class EmpresaAuditoriaController extends Controller
{
    /**
     * Consola Forense de Auditoría de Credenciales (Patrón Append-Only).
     * Solo lectura inmutable: Prohibidas las operaciones de UPDATE y DELETE.
     */
    public function index(Request $request)
    {
        $query = UserCredencialAuditoria::with([
            'usuarioAfectado.empresa',
            'usuarioAfectado.rolOperativo',
            'usuarioModificador',
            'dimTiempo'
        ]);

        // Filtro por tipo de cambio
        if ($request->filled('tipo_cambio')) {
            $query->where('tipo_cambio', 'like', "%{$request->tipo_cambio}%");
        }

        // Filtro por usuario específico
        if ($request->filled('usuario_id')) {
            $query->where('id_usuario_users', $request->usuario_id);
        }

        // Filtro HTAP por año y mes usando dim_tiempo (cero cálculo de fechas en runtime)
        if ($request->filled('anio')) {
            $query->whereHas('dimTiempo', function ($q) use ($request) {
                $q->where('anio', $request->anio);
            });
        }

        if ($request->filled('mes')) {
            $query->whereHas('dimTiempo', function ($q) use ($request) {
                $q->where('mes', $request->mes);
            });
        }

        $auditorias = $query->orderBy('id_auditoria_credencial', 'desc')->paginate(20);

        // Lista de empleados para selector de filtro
        $empleados = UserCuenta::where('id_empresa_users', 1)
            ->select('id_usuario_users', 'nombre_usuario_users')
            ->get();

        // Tipos de cambio registrados
        $tiposCambio = UserCredencialAuditoria::distinct()->pluck('tipo_cambio');

        // Métricas de auditoría forense
        $metricas = [
            'total_eventos' => UserCredencialAuditoria::count(),
            'reseteos' => UserCredencialAuditoria::where('tipo_cambio', 'like', '%RESET%')->count(),
            'altas' => UserCredencialAuditoria::where('tipo_cambio', 'like', '%CREACION%')->count(),
            'cambios_rol' => UserCredencialAuditoria::where('tipo_cambio', 'like', '%SUBROL%')->count(),
        ];

        return view('empresa.auditoria.index', compact('auditorias', 'empleados', 'tiposCambio', 'metricas'));
    }
}

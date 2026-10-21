<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerarEmpleadosRequest;
use App\Models\EmpresaRol;
use App\Models\EmpresaUser;
use App\Models\UserCuenta;
use App\Services\EmpleadoBatchService;
use Illuminate\Http\Request;

class EmpresaEmpleadoController extends Controller
{
    protected EmpleadoBatchService $batchService;

    public function __construct(EmpleadoBatchService $batchService)
    {
        $this->batchService = $batchService;
    }

    /**
     * Muestra el panel gerencial de gestión de empleados corporativos y subroles.
     */
    public function index(Request $request)
    {
        // Empresa piloto Madeart (ID: 1)
        $empresa = EmpresaUser::with(['roles', 'dimTiempo'])->firstOrCreate(
            ['id_empresa_users' => 1],
            [
                'id_dim_tiempo_creacion' => 2026102108,
                'razon_social_empresa_users' => 'Madeart Carpintería & Diseño S.R.L.',
                'nit_empresa_users' => '1023849021',
                'telefono_whatsapp_empresa_users' => '+59178945612',
                'activa_empresa_users' => true,
            ]
        );

        $subroles = EmpresaRol::where('id_empresa_users', $empresa->id_empresa_users)->get();

        // Consulta de empleados con filtro opcional por subrol
        $query = UserCuenta::where('id_empresa_users', $empresa->id_empresa_users)
            ->with(['rolOperativo', 'dimTiempo']);

        if ($request->filled('subrol_id')) {
            $query->where('id_empresa_rol', $request->subrol_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_usuario_users', 'like', "%{$buscar}%")
                  ->orWhere('email_usuario_users', 'like', "%{$buscar}%");
            });
        }

        $empleados = $query->orderBy('id_usuario_users', 'desc')->paginate(15);

        // Métricas rápidas de planta
        $metricas = [
            'total_empleados' => UserCuenta::where('id_empresa_users', $empresa->id_empresa_users)->count(),
            'armadores' => UserCuenta::where('id_empresa_users', $empresa->id_empresa_users)->where('id_empresa_rol', 1)->count(),
            'disenadores' => UserCuenta::where('id_empresa_users', $empresa->id_empresa_users)->where('id_empresa_rol', 2)->count(),
            'supervisores' => UserCuenta::where('id_empresa_users', $empresa->id_empresa_users)->where('id_empresa_rol', 3)->count(),
        ];

        return view('empresa.empleados.index', compact('empresa', 'subroles', 'empleados', 'metricas'));
    }

    /**
     * Procesa la creación masiva de cuentas corporativas por lote.
     */
    public function generarLote(GenerarEmpleadosRequest $request)
    {
        $idEmpresa = 1; // Madeart
        $idUsuarioModifica = auth()->id() ?? 1;

        $resultado = $this->batchService->generarLote(
            $idEmpresa,
            (int) $request->empresa_rol_id,
            $request->prefijo_usuario,
            (int) $request->cantidad,
            $request->dominio_email ?? '@madeart.com',
            $idUsuarioModifica
        );

        return redirect()->route('empresa.empleados.index')
            ->with('lote_generado', $resultado)
            ->with('success', "Se generaron exitosamente {$resultado['total_creados']} cuentas de empleado.");
    }

    /**
     * Resetea la contraseña de un empleado y registra el evento forense.
     */
    public function resetPassword(int $id)
    {
        $idUsuarioModifica = auth()->id() ?? 1;
        $resultado = $this->batchService->resetearPassword($id, $idUsuarioModifica);

        return redirect()->route('empresa.empleados.index')
            ->with('password_reseteada', $resultado)
            ->with('success', "Contraseña reseteada para el usuario {$resultado['username']}.");
    }

    /**
     * Actualiza el subrol operativo de un empleado en planta.
     */
    public function cambiarSubrol(Request $request, int $id)
    {
        $request->validate([
            'id_empresa_rol' => 'required|integer|exists:empresa_roles,id_empresa_rol',
        ]);

        $idUsuarioModifica = auth()->id() ?? 1;
        $this->batchService->cambiarSubrol($id, (int) $request->id_empresa_rol, $idUsuarioModifica);

        return redirect()->route('empresa.empleados.index')
            ->with('success', 'Subrol operativo actualizado correctamente con registro de auditoría.');
    }
}

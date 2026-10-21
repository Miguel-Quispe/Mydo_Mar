<?php

namespace App\Http\Controllers;

use App\Models\MuebleDiseno;
use App\Models\MuebleProgresoUsuario;
use App\Models\UserCuenta;
use App\Services\DimTiempoService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TallerArmadoController extends Controller
{
    /**
     * Muestra el panel de órdenes y proyectos en taller para armadores.
     */
    public function index()
    {
        $disenos = MuebleDiseno::with(['empresa', 'categoria', 'nivel', 'piezas', 'pasosEnsamblaje', 'herrajesProyecto.catalogoHerraje'])
            ->orderBy('id_diseno_muebles', 'desc')
            ->paginate(12);

        // Operario activo de muestra (Armador de Madeart)
        $operario = UserCuenta::where('id_empresa_rol', 1)->first() ?? UserCuenta::first();

        // Progresos activos
        $progresos = MuebleProgresoUsuario::where('usuario_id_users', $operario->id_usuario_users ?? 1)
            ->pluck('porcentaje_progreso_muebles', 'id_diseno_muebles');

        return view('taller.index', compact('disenos', 'operario', 'progresos'));
    }

    /**
     * Terminal de taller: Guía táctil paso a paso y despiece para ensamblaje.
     */
    public function show(int $id)
    {
        $diseno = MuebleDiseno::with([
            'piezas.material',
            'herrajesProyecto.catalogoHerraje',
            'pasosEnsamblaje',
            'empresa',
            'categoria'
        ])->findOrFail($id);

        $usuarioId = auth()->id() ?? 1;

        // Buscar o iniciar el progreso del operario con sellado temporal
        $smartKey = DimTiempoService::obtenerOCrearSmartKey();
        $progreso = MuebleProgresoUsuario::firstOrCreate(
            [
                'usuario_id_users' => $usuarioId,
                'id_diseno_muebles' => $diseno->id_diseno_muebles,
            ],
            [
                'id_dim_tiempo_inicio' => $smartKey,
                'paso_actual_muebles' => 1,
                'porcentaje_progreso_muebles' => 0.00,
                'completado_muebles' => false,
            ]
        );

        $totalPasos = $diseno->pasosEnsamblaje->count();
        if ($totalPasos === 0) {
            $totalPasos = 1;
        }

        return view('taller.armado', compact('diseno', 'progreso', 'totalPasos'));
    }

    /**
     * Registra el avance de paso de ensamblaje en tiempo real.
     */
    public function actualizarProgreso(Request $request, int $id)
    {
        $request->validate([
            'paso_actual' => 'required|integer|min:1',
            'total_pasos' => 'required|integer|min:1',
        ]);

        $usuarioId = auth()->id() ?? 1;
        $pasoActual = (int) $request->paso_actual;
        $totalPasos = (int) $request->total_pasos;

        $porcentaje = min(100.0, round(($pasoActual / $totalPasos) * 100, 2));
        $completado = $pasoActual >= $totalPasos;

        $progreso = MuebleProgresoUsuario::updateOrCreate(
            [
                'usuario_id_users' => $usuarioId,
                'id_diseno_muebles' => $id,
            ],
            [
                'paso_actual_muebles' => $pasoActual,
                'porcentaje_progreso_muebles' => $porcentaje,
                'completado_muebles' => $completado,
            ]
        );

        // Notificación automática vía WhatsApp al supervisor de planta cuando se completa el mueble
        if ($completado) {
            try {
                $diseno = MuebleDiseno::with('empresa')->find($id);
                $telefonoSupervisor = $diseno?->empresa?->telefono_whatsapp_empresa_users ?? null;
                if ($telefonoSupervisor) {
                    (new WhatsAppService())->notificarCambioEstadoTaller(
                        $telefonoSupervisor,
                        $id,
                        'Completado al 100% y listo para inspección'
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Error enviando WhatsApp de fin de taller: " . $e->getMessage());
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'paso_actual' => $progreso->paso_actual_muebles,
                'porcentaje' => $progreso->porcentaje_progreso_muebles,
                'completado' => $progreso->completado_muebles,
            ]);
        }

        return redirect()->back()->with('success', "Progreso actualizado al {$porcentaje}%.");
    }

    /**
     * Genera y descarga el Plano Técnico Oficial en PDF para el taller de producción.
     */
    public function exportarPlanoPdf(int $id)
    {
        $diseno = MuebleDiseno::with([
            'piezas.material',
            'herrajesProyecto.catalogoHerraje',
            'pasosEnsamblaje',
            'empresa',
            'categoria',
            'nivel',
        ])->findOrFail($id);

        $fechaGeneracion = now()->setTimezone('America/La_Paz')->format('d/m/Y H:i');

        $pdf = Pdf::loadView('taller.pdf.plano-tecnico', [
            'diseno'          => $diseno,
            'fechaGeneracion' => $fechaGeneracion,
        ])
        ->setPaper('letter', 'portrait')
        ->setOption('defaultFont', 'DejaVu Sans')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', false)
        ->setOption('dpi', 150);

        $nombreArchivo = sprintf(
            'PlanoTecnico_ORD-%s_%s.pdf',
            str_pad($diseno->id_diseno_muebles, 4, '0', STR_PAD_LEFT),
            now()->format('Ymd_His')
        );

        return $pdf->download($nombreArchivo);
    }
}

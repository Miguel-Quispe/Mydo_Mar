<?php

namespace App\Services;

use App\Models\DimTiempo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DimTiempoService
{
    /**
     * Obtiene o crea la Smart Key cronológica en formato YYYYMMDDHH
     * garantizando integridad referencial inmutable para cualquier operación.
     *
     * @param Carbon|null $fecha
     * @return int
     */
    public static function obtenerOCrearSmartKey(?Carbon $fecha = null): int
    {
        $fecha = $fecha ?? now();
        $smartKey = (int) $fecha->format('YmdH');

        DimTiempo::firstOrCreate(
            ['id_dim_tiempo' => $smartKey],
            [
                'fecha_hora_exacta' => $fecha->format('Y-m-d H:i:s'),
                'anio' => (int) $fecha->year,
                'mes' => (int) $fecha->month,
                'dia' => (int) $fecha->day,
                'hora' => (int) $fecha->hour,
            ]
        );

        return $smartKey;
    }
}

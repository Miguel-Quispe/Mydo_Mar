<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuebleProgresoUsuario extends Model
{
    protected $table = 'muebles_progreso_usuario';
    protected $primaryKey = 'id_progreso_muebles';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id_users',
        'id_diseno_muebles',
        'id_dim_tiempo_inicio',
        'paso_actual_muebles',
        'porcentaje_progreso_muebles',
        'completado_muebles',
    ];

    protected $casts = [
        'porcentaje_progreso_muebles' => 'decimal:2',
        'completado_muebles' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(UserCuenta::class, 'usuario_id_users');
    }

    public function diseno()
    {
        return $this->belongsTo(MuebleDiseno::class, 'id_diseno_muebles');
    }

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_inicio');
    }
}

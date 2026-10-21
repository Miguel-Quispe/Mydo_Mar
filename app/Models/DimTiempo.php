<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimTiempo extends Model
{
    protected $table = 'dim_tiempo';
    protected $primaryKey = 'id_dim_tiempo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_dim_tiempo',
        'fecha_hora_exacta',
        'anio',
        'mes',
        'dia',
        'hora',
    ];

    public function empresas()
    {
        return $this->hasMany(EmpresaUser::class, 'id_dim_tiempo_creacion');
    }

    public function cuentas()
    {
        return $this->hasMany(UserCuenta::class, 'id_dim_tiempo_registro');
    }

    public function auditorias()
    {
        return $this->hasMany(UserCredencialAuditoria::class, 'id_dim_tiempo_evento');
    }

    public function disenos()
    {
        return $this->hasMany(MuebleDiseno::class, 'id_dim_tiempo_publicacion');
    }
}

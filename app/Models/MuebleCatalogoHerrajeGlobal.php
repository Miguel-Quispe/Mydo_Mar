<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuebleCatalogoHerrajeGlobal extends Model
{
    protected $table = 'muebles_catalogo_herrajes_global';
    protected $primaryKey = 'id_catalogo_herraje_global';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario_registra_users',
        'id_dim_tiempo_creacion',
        'nombre_herraje_global',
        'tipo_visibilidad_herraje',
        'precio_referencial',
        'activo_global',
    ];

    public function registrador()
    {
        return $this->belongsTo(UserCuenta::class, 'id_usuario_registra_users');
    }

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_creacion');
    }

    public function proyectos()
    {
        return $this->hasMany(MuebleHerrajeProyecto::class, 'id_catalogo_herraje_global');
    }
}

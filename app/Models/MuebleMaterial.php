<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuebleMaterial extends Model
{
    protected $table = 'muebles_materiales';
    protected $primaryKey = 'id_material_muebles';
    public $timestamps = false;

    protected $fillable = [
        'id_dim_tiempo_creacion',
        'nombre_material_muebles',
        'tipo_material_muebles',
        'espesor_material_muebles',
        'precio_lamina_muebles',
    ];

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_creacion');
    }

    public function piezas()
    {
        return $this->hasMany(MueblePieza::class, 'id_material_muebles');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuebleNivel extends Model
{
    protected $table = 'muebles_niveles';
    protected $primaryKey = 'id_nivel_muebles';
    public $timestamps = false;

    protected $fillable = [
        'id_dim_tiempo_creacion',
        'nombre_nivel_muebles',
    ];

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_creacion');
    }

    public function disenos()
    {
        return $this->hasMany(MuebleDiseno::class, 'id_nivel_muebles');
    }
}

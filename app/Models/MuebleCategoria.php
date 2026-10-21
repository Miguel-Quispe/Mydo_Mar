<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuebleCategoria extends Model
{
    protected $table = 'muebles_categorias';
    protected $primaryKey = 'id_categoria_muebles';
    public $timestamps = false;

    protected $fillable = [
        'id_dim_tiempo_creacion',
        'nombre_categoria_muebles',
        'icono_categoria_muebles',
    ];

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_creacion');
    }

    public function disenos()
    {
        return $this->hasMany(MuebleDiseno::class, 'id_categoria_muebles');
    }
}

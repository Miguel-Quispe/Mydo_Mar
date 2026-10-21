<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuebleHerrajeProyecto extends Model
{
    protected $table = 'muebles_herrajes_por_proyecto';
    protected $primaryKey = 'id_herraje_proyecto';
    public $timestamps = false;

    protected $fillable = [
        'id_diseno_muebles',
        'id_catalogo_herraje_global',
        'cantidad_necesaria_proyecto',
    ];

    public function diseno()
    {
        return $this->belongsTo(MuebleDiseno::class, 'id_diseno_muebles');
    }

    public function catalogoHerraje()
    {
        return $this->belongsTo(MuebleCatalogoHerrajeGlobal::class, 'id_catalogo_herraje_global');
    }

    /**
     * Calcula el subtotal del costo de este herraje en el proyecto.
     */
    public function getSubtotalCostoAttribute(): float
    {
        $precio = $this->catalogoHerraje ? (float) $this->catalogoHerraje->precio_referencial : 0.00;
        return $precio * (int) $this->cantidad_necesaria_proyecto;
    }
}

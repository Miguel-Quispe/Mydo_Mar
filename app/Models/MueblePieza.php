<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MueblePieza extends Model
{
    protected $table = 'muebles_piezas';
    protected $primaryKey = 'id_pieza_muebles';
    public $timestamps = false;

    protected $fillable = [
        'id_diseno_muebles',
        'id_material_muebles',
        'nombre_pieza_muebles',
        'ancho_mm_muebles',
        'alto_mm_muebles',
        'espesor_mm_muebles',
        'posicion_x',
        'posicion_y',
        'posicion_z',
        'rotacion_x',
        'rotacion_y',
        'rotacion_z',
    ];

    protected $casts = [
        'ancho_mm_muebles' => 'decimal:2',
        'alto_mm_muebles' => 'decimal:2',
        'espesor_mm_muebles' => 'decimal:2',
        'posicion_x' => 'decimal:2',
        'posicion_y' => 'decimal:2',
        'posicion_z' => 'decimal:2',
        'rotacion_x' => 'decimal:2',
        'rotacion_y' => 'decimal:2',
        'rotacion_z' => 'decimal:2',
    ];

    public function diseno()
    {
        return $this->belongsTo(MuebleDiseno::class, 'id_diseno_muebles');
    }

    public function material()
    {
        return $this->belongsTo(MuebleMaterial::class, 'id_material_muebles');
    }

    /**
     * Calcula los metros cuadrados de la pieza.
     */
    public function getMetrosCuadradosAttribute(): float
    {
        return ($this->ancho_mm_muebles * $this->alto_mm_muebles) / 1000000.0;
    }
}

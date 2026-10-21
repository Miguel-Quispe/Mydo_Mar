<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MueblePasoEnsamblaje extends Model
{
    protected $table = 'muebles_pasos_ensamblaje';
    protected $primaryKey = 'id_paso_muebles';
    public $timestamps = false;

    protected $fillable = [
        'id_diseno_muebles',
        'numero_paso_muebles',
        'titulo_paso_muebles',
    ];

    public function diseno()
    {
        return $this->belongsTo(MuebleDiseno::class, 'id_diseno_muebles');
    }
}

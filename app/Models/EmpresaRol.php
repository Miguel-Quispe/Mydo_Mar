<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaRol extends Model
{
    protected $table = 'empresa_roles';
    protected $primaryKey = 'id_empresa_rol';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa_users',
        'id_dim_tiempo_creacion',
        'nombre_rol_empresa',
        'es_rol_predeterminado',
    ];

    public function empresa()
    {
        return $this->belongsTo(EmpresaUser::class, 'id_empresa_users');
    }

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_creacion');
    }

    public function usuarios()
    {
        return $this->hasMany(UserCuenta::class, 'id_empresa_rol');
    }
}

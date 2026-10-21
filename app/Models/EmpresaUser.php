<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaUser extends Model
{
    protected $table = 'empresa_users';
    protected $primaryKey = 'id_empresa_users';
    public $timestamps = false;

    protected $fillable = [
        'id_dim_tiempo_creacion',
        'razon_social_empresa_users',
        'nit_empresa_users',
        'telefono_whatsapp_empresa_users',
        'activa_empresa_users',
    ];

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_creacion');
    }

    public function roles()
    {
        return $this->hasMany(EmpresaRol::class, 'id_empresa_users');
    }

    public function empleados()
    {
        return $this->hasMany(UserCuenta::class, 'id_empresa_users');
    }

    public function disenos()
    {
        return $this->hasMany(MuebleDiseno::class, 'id_empresa_users');
    }
}

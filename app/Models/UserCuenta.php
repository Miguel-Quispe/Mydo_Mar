<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCuenta extends Model
{
    protected $table = 'users_cuentas';
    protected $primaryKey = 'id_usuario_users';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa_users',
        'id_rol_users',
        'id_empresa_rol',
        'id_dim_tiempo_registro',
        'nombre_usuario_users',
        'email_usuario_users',
        'password_hash_users',
        'cuenta_generada_por_empresa',
    ];

    protected $hidden = [
        'password_hash_users',
    ];

    public function empresa()
    {
        return $this->belongsTo(EmpresaUser::class, 'id_empresa_users');
    }

    public function rolGlobal()
    {
        return $this->belongsTo(UserRol::class, 'id_rol_users');
    }

    public function rolOperativo()
    {
        return $this->belongsTo(EmpresaRol::class, 'id_empresa_rol');
    }

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_registro');
    }

    public function auditoriasRecibidas()
    {
        return $this->hasMany(UserCredencialAuditoria::class, 'id_usuario_users');
    }

    public function auditoriasRealizadas()
    {
        return $this->hasMany(UserCredencialAuditoria::class, 'id_usuario_modifica_users');
    }

    public function disenosCreados()
    {
        return $this->hasMany(MuebleDiseno::class, 'creador_id_usuario_users');
    }
}

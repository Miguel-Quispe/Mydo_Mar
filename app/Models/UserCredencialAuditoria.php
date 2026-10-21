<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCredencialAuditoria extends Model
{
    protected $table = 'users_credenciales_auditoria';
    protected $primaryKey = 'id_auditoria_credencial';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario_users',
        'id_usuario_modifica_users',
        'id_dim_tiempo_evento',
        'tipo_cambio',
        'valor_anterior_hash',
        'valor_nuevo_hash',
    ];

    public function usuarioAfectado()
    {
        return $this->belongsTo(UserCuenta::class, 'id_usuario_users');
    }

    public function usuarioModificador()
    {
        return $this->belongsTo(UserCuenta::class, 'id_usuario_modifica_users');
    }

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_evento');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRol extends Model
{
    protected $table = 'users_roles';
    protected $primaryKey = 'id_rol_users';
    public $timestamps = false;

    protected $fillable = [
        'nombre_rol_users',
    ];

    public function usuarios()
    {
        return $this->hasMany(UserCuenta::class, 'id_rol_users');
    }
}

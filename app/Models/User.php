<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellidoPaterno',
        'apellidoMaterno',
        'telefono',
        'rol_id',
        'clave_rol',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación: Un Maestro Carpintero puede tener varios muebles diseñados
     */
    public function muebles()
    {
        return $this->hasMany(Mueble::class, 'user_id');
    }

    /**
     * Relación: Un cliente puede tener varios muebles favoritos
     */
    public function favoritos()
    {
        return $this->belongsToMany(Mueble::class, 'favoritos')->withTimestamps();
    }
}
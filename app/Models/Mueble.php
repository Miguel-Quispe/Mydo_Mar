<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mueble extends Model
{
    use HasFactory;

    protected $table = 'muebles';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'material',
        'precio',
        'dimensiones',
        'estructura_json',
        'imagen_url',
        'parametros_diseno',
        'es_publico',
        'vistas',
    ];

    /**
     * Relación: Un mueble pertenece a un Usuario (Maestro Carpintero).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Un mueble puede ser favorito de varios usuarios.
     */
    public function esFavoritoDe()
    {
        return $this->belongsToMany(User::class, 'favoritos')->withTimestamps();
    }
}
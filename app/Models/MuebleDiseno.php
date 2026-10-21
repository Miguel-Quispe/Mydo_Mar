<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuebleDiseno extends Model
{
    protected $table = 'muebles_disenos';
    protected $primaryKey = 'id_diseno_muebles';
    public $timestamps = false;

    protected $fillable = [
        'creador_id_usuario_users',
        'id_empresa_users',
        'id_categoria_muebles',
        'id_nivel_muebles',
        'id_dim_tiempo_publicacion',
        'nombre_diseno_muebles',
        'descripcion',
        'configuracion_3d_json',
        'imagen_render_url',
        'precio_estimado_total',
        'publicado_diseno_muebles',
    ];

    protected $casts = [
        'publicado_diseno_muebles' => 'boolean',
        'precio_estimado_total' => 'decimal:2',
    ];

    public function creador()
    {
        return $this->belongsTo(UserCuenta::class, 'creador_id_usuario_users');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaUser::class, 'id_empresa_users');
    }

    public function categoria()
    {
        return $this->belongsTo(MuebleCategoria::class, 'id_categoria_muebles');
    }

    public function nivel()
    {
        return $this->belongsTo(MuebleNivel::class, 'id_nivel_muebles');
    }

    public function dimTiempo()
    {
        return $this->belongsTo(DimTiempo::class, 'id_dim_tiempo_publicacion');
    }

    public function piezas()
    {
        return $this->hasMany(MueblePieza::class, 'id_diseno_muebles');
    }

    public function herrajesProyecto()
    {
        return $this->hasMany(MuebleHerrajeProyecto::class, 'id_diseno_muebles');
    }

    public function pasosEnsamblaje()
    {
        return $this->hasMany(MueblePasoEnsamblaje::class, 'id_diseno_muebles')->orderBy('numero_paso_muebles');
    }

    public function progresosUsuarios()
    {
        return $this->hasMany(MuebleProgresoUsuario::class, 'id_diseno_muebles');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';
    public $timestamps = false; // Tu migración no tiene timestamps por defecto
    protected $fillable = ['nombre', 'descripcion', 'activo'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'departamento_id');
    }
}
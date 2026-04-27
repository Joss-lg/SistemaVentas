<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table = 'cortes_caja';
    public $timestamps = false;

    protected $fillable = [
    'usuario_id', 
    'fecha_apertura', 
    'fecha_cierre', 
    'monto_inicial', 
    'total_ventas_efectivo', 
    'total_ventas_tarjeta', 
    'total_esperado', 
    'total_contado', 
    'difference', 
    'notas'
];
public function usuario()
{
    return $this->belongsTo(Usuario::class, 'usuario_id');
}
}
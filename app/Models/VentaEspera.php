<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaEspera extends Model
{
    protected $table = 'ventas_espera';
    
    // Cambia esto para que Laravel sepa cuál es tu columna de fecha
    public $timestamps = true; 
    const CREATED_AT = 'fecha_pausa'; 
    const UPDATED_AT = null; // Como no tienes columna de actualización

    protected $fillable = ['usuario_id', 'fecha_pausa', 'carrito_data'];

    protected $casts = [
        'carrito_data' => 'array',
        'fecha_pausa' => 'datetime',
    ];
}
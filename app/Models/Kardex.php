<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';
    public $timestamps = false;

    protected $fillable = [
        'producto_id', 'tipo_movimiento', 'cantidad', 
        'precio_unitario', 'usuario_id', 'venta_id', 'fecha'
    ]; //

    public function producto() { return $this->belongsTo(Producto::class); }
}
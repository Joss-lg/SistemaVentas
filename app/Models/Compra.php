<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = ['producto_id', 'proveedor', 'cantidad', 'costo_total', 'metodo_pago'];
    public function producto() {
        return $this->belongsTo(Producto::class);
    }
}
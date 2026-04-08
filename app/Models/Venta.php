<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;

class Venta extends Model
{
    protected $table = 'ventas';
    public $timestamps = false;

protected $fillable = [
    'folio', 
    'fecha', 
    'usuario_id', 
    'cliente_id', 
    'subtotal', 
    'descuento', 
    'total', 
    'tipo_pago',        // Antes: metodo_pago
    'referencia_pago',   // Nuevo: Para el folio de tarjeta/transferencia
    'pago_cliente',     // Antes: monto_recibido
    'cambio', 
    'estado'
];

public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function usuario()
    {
        // Cambiamos User::class por Usuario::class
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}


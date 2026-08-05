<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    // Habilitamos timestamps ya que la migración incluye $table->timestamps()
    public $timestamps = true;

    protected $fillable = [
        'folio',
        'fecha',
        'usuario_id',
        'cliente_id',
        'subtotal',
        'descuento',
        'total',
        'tipo_pago',
        'referencia_pago',
        'pago_cliente',
        'cambio',
        'estado',
        'cancelada_por',
        'motivo_cancelacion',
    ];

    /**
     * Casteo de atributos
     */
    protected $casts = [
        'fecha' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'pago_cliente' => 'decimal:2',
        'cambio' => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
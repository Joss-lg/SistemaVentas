<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    public $timestamps = false;

    protected $fillable = [
        'codigo_barras', 'descripcion', 'precio_costo', 'precio_venta', 
        'stock_actual', 'stock_minimo', 'departamento_id', 'es_granel', 
        'unidad_medida', 'activo'
    ];

    public function departamento()
    {
        // Esto permite que $producto->departamento->nombre funcione
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
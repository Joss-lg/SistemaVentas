<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table = 'cortes_caja';

    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'total_ventas_efectivo',
        'total_ventas_tarjeta',
        'total_transferencia',
        'total_esperado',
        'total_contado',
        'difference',
        'notas',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'total_ventas_efectivo' => 'decimal:2',
        'total_ventas_tarjeta' => 'decimal:2',
        'total_transferencia' => 'decimal:2',
        'total_esperado' => 'decimal:2',
        'total_contado' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Devuelve el turno abierto (sin cerrar) de un usuario, si existe.
     */
    public static function turnoActivo($usuarioId)
    {
        return self::where('usuario_id', $usuarioId)
            ->whereNull('fecha_cierre')
            ->first();
    }
}
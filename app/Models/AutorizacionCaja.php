<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutorizacionCaja extends Model
{
    protected $table = 'autorizaciones_caja';

    protected $fillable = [
        'corte_caja_id', 'usuario_solicita_id', 'usuario_autoriza_id',
        'efectivo_real', 'faltante', 'estado',
    ];

    public function corte()
    {
        return $this->belongsTo(CorteCaja::class, 'corte_caja_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'usuario_solicita_id');
    }

    public function autoriza()
    {
        return $this->belongsTo(Usuario::class, 'usuario_autoriza_id');
    }

    public static function pendienteDe($corteCajaId)
    {
        return self::where('corte_caja_id', $corteCajaId)
            ->where('estado', 'pendiente')
            ->latest()
            ->first();
    }

    public static function aprobadaDe($corteCajaId)
    {
        return self::where('corte_caja_id', $corteCajaId)
            ->where('estado', 'aprobada')
            ->latest()
            ->first();
    }
}
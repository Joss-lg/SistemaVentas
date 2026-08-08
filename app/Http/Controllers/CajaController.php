<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CorteCaja;
use App\Models\Gasto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function aperturaIndex()
    {
        if (CorteCaja::turnoActivo(auth()->id())) {
            return redirect()->route('ventas.index')->with('info', 'El turno ya se encuentra abierto.');
        }

        return view('caja.apertura');
    }

    public function aperturaStore(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        if (CorteCaja::turnoActivo(auth()->id())) {
            return redirect()->route('ventas.index')->with('info', 'El turno ya se encuentra abierto.');
        }

        CorteCaja::create([
            'usuario_id'     => auth()->id(),
            'fecha_apertura' => now(),
            'monto_inicial'  => $request->monto_inicial,
        ]);

        return redirect()->route('ventas.index')->with('success', 'Caja abierta correctamente.');
    }

    public function corteIndex()
    {
        $turno = CorteCaja::turnoActivo(auth()->id());

        if (!$turno) {
            return redirect()->route('caja.apertura')->with('error', 'No tienes un turno abierto.');
        }

        $montoInicial = $turno->monto_inicial;
        $fechaApertura = $turno->fecha_apertura;

        $queryVentas = Venta::where('usuario_id', auth()->id())
            ->where('fecha', '>=', $fechaApertura)
            ->where('estado', '!=', 'cancelada');

        $ventasEfectivoRaw = (clone $queryVentas)->where('tipo_pago', 'efectivo')->get(['pago_cliente', 'cambio', 'total']);

        $ventasEfectivo = $ventasEfectivoRaw->sum(function ($v) {
            if (is_null($v->pago_cliente) || is_null($v->cambio)) {
                return $v->total;
            }
            return $v->pago_cliente - $v->cambio;
        });

        $totalCambio = $ventasEfectivoRaw->sum('cambio');

        $ventasTarjeta = (clone $queryVentas)->where('tipo_pago', 'tarjeta')->sum('total');
        $ventasTransferencia = (clone $queryVentas)->where('tipo_pago', 'transferencia')->sum('total');
        $ventasDelTurno = (clone $queryVentas)->sum('total');

        $ventasDetalle = (clone $queryVentas)
            ->with('detalles.producto')
            ->orderBy('fecha', 'desc')
            ->get();

        $comprasDelTurno = Compra::with('producto')
            ->where('created_at', '>=', $fechaApertura)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCompras = $comprasDelTurno->sum('costo_total');

        // Gastos manuales registrados durante este turno
        $gastosDelTurno = Gasto::where('created_at', '>=', $fechaApertura)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalGastos = $gastosDelTurno->sum('monto');

        $totalSistema = ($montoInicial + $ventasEfectivo) - $totalCompras - $totalGastos;

        return view('caja.corte', compact(
            'montoInicial',
            'ventasEfectivo',
            'ventasTarjeta',
            'ventasTransferencia',
            'ventasDelTurno',
            'ventasDetalle',
            'comprasDelTurno',
            'totalCompras',
            'gastosDelTurno',
            'totalGastos',
            'totalSistema',
            'totalCambio',
            'fechaApertura',
            'turno'
        ));
    }

    public function corteStore(Request $request)
{
    $request->validate([
        'efectivo_real' => 'required|numeric',
    ]);

    $turno = CorteCaja::turnoActivo(auth()->id());

    if (!$turno) {
        return redirect()->route('caja.apertura')->with('error', 'No tienes un turno abierto.');
    }

    try {
        DB::beginTransaction();

        $fechaApertura = $turno->fecha_apertura;

        $queryVentas = Venta::where('usuario_id', auth()->id())
            ->where('fecha', '>=', $fechaApertura)
            ->where('estado', '!=', 'cancelada');

        $ventasEfectivoRaw = (clone $queryVentas)->where('tipo_pago', 'efectivo')->get(['pago_cliente', 'cambio', 'total']);
        $ventasEfectivo = $ventasEfectivoRaw->sum(function ($v) {
            if (is_null($v->pago_cliente) || is_null($v->cambio)) {
                return $v->total;
            }
            return $v->pago_cliente - $v->cambio;
        });

        $ventasTarjeta = (clone $queryVentas)->where('tipo_pago', 'tarjeta')->sum('total');
        $ventasTransferencia = (clone $queryVentas)->where('tipo_pago', 'transferencia')->sum('total');

        $totalCompras = Compra::where('created_at', '>=', $fechaApertura)->sum('costo_total');
        $totalGastos = Gasto::where('created_at', '>=', $fechaApertura)->sum('monto');

        $ventasEsperadas = ($turno->monto_inicial + $ventasEfectivo) - $totalCompras - $totalGastos;

        $difference = $request->efectivo_real - $ventasEsperadas;

        // NUEVO: si hay faltante, exige autorización aprobada para este turno
        if ($difference < 0) {
            $autorizacion = \App\Models\AutorizacionCaja::aprobadaDe($turno->id);

            if (!$autorizacion) {
                DB::rollBack();
                return redirect()->back()
                    ->with('requiere_autorizacion', true)
                    ->with('faltante', $difference)
                    ->withInput();
            }
        }

        $turno->update([
            'fecha_cierre'          => now(),
            'total_ventas_efectivo' => $ventasEfectivo,
            'total_ventas_tarjeta'  => $ventasTarjeta,
            'total_transferencia'   => $ventasTransferencia,
            'total_esperado'        => $ventasEsperadas,
            'total_contado'         => $request->efectivo_real,
            'difference'            => $difference,
            'notas'                 => 'Corte de caja realizado',
        ]);

        DB::commit();

        return redirect()->route('caja.apertura')->with('success', 'Corte guardado. Caja cerrada.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error al guardar el corte: '.$e->getMessage());
    }
}
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Compra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventarioController extends Controller
{
    /**
     * Registra la entrada de mercancía y genera un gasto automático.
     */
    public function agregarStock(Request $request)
    {
        // 1. Validar los datos de entrada
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|numeric|min:0.001',
            'costo_total' => 'required|numeric|min:0',
            'proveedor'   => 'required|string|max:255'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                
                $producto = Producto::findOrFail($request->producto_id);

                // 2. Registrar la Compra (Historial de Inventario)
                $compra = Compra::create([
                    'producto_id' => $request->producto_id,
                    'proveedor'   => strtoupper($request->proveedor), 
                    'cantidad'    => $request->cantidad,
                    'costo_total' => $request->costo_total,
                    'metodo_pago' => $request->metodo_pago ?? 'efectivo', 
                ]);

                // 3. REGISTRO EN TABLA DE GASTOS (Flujo de Caja)
                // Usamos Query Builder para insertar directo en la tabla de gastos
                DB::table('gastos')->insert([
                    'descripcion' => "COMPRA: " . $producto->descripcion . " (PROV: " . strtoupper($request->proveedor) . ")",
                    'monto'       => $request->costo_total,
                    'categoria'   => 'INVENTARIO',
                    'usuario'     => auth()->user()->name ?? 'Admin', // Por si tienes login
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // 4. Actualizar el stock
                $producto->increment('stock_actual', $request->cantidad);

                return response()->json([
                    'status'      => 'success',
                    'mensaje'     => 'Stock actualizado y Gasto registrado: ' . $producto->descripcion,
                    'nuevo_stock' => $producto->stock_actual,
                    'compra_id'   => $compra->id,
                    'proveedor'   => $compra->proveedor
                ]);
            });

        } catch (\Exception $e) {
            Log::error("Error en agregarStock: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'mensaje' => 'Error técnico: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaEspera; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function index()
    {
        return view('ventas.index');
    }

    public function buscarProducto(Request $request)
    {
        $producto = Producto::where('codigo_barras', $request->codigo)
                            ->where('activo', true)
                            ->first();

        if (!$producto) {
            return response()->json(['mensaje' => 'Producto no encontrado'], 404);
        }
        
        return response()->json($producto);
    }

    public function buscarPorNombre(Request $request)
    {
        $q = $request->query('q');
        $productos = Producto::where('descripcion', 'LIKE', "%{$q}%")
                        ->where('activo', true)
                        ->limit(10)
                        ->get();

        return response()->json($productos);
    }

public function finalizarVenta(Request $request)
{
    // Validamos que el carrito no llegue vacío desde el JS
    if (!$request->productos || count($request->productos) == 0) {
        return response()->json(['status' => 'error', 'mensaje' => 'Carrito vacío'], 400);
    }

    try {
        // Usamos la transacción para asegurar que si falla el stock, no se cree la venta
        $ventaFinalizada = DB::transaction(function () use ($request) {
            
            // 1. Crear la venta con los NUEVOS nombres de tu modelo Venta.php
            $venta = Venta::create([
                'folio'           => 'V-' . strtoupper(uniqid()),
                'fecha'           => now(), 
                'usuario_id'      => Auth::id(),
                'cliente_id'      => $request->cliente_id ?? null,
                'subtotal'        => $request->total, 
                'descuento'       => 0,
                'total'           => $request->total,
                'tipo_pago'       => $request->metodo_pago ?? 'efectivo', // Usamos tipo_pago
                'referencia_pago' => $request->referencia_pago,        // El folio que mandamos del modal
                'pago_cliente'    => $request->monto_recibido,          // Usamos pago_cliente
                'cambio'          => $request->cambio,
                'estado'          => 'completada',
            ]);

            // 2. Registrar los detalles
            foreach ($request->productos as $item) {
                VentaDetalle::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $item['id'],
                    'descripcion'     => $item['descripcion'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $item['subtotal'] ?? ($item['precio'] * $item['cantidad'])
                ]);

                // 3. Descontar stock (asegúrate que en Producto sea stock_actual o stock)
                $prod = Producto::find($item['id']);
                if ($prod) {
                    $prod->decrement('stock_actual', $item['cantidad']);
                }
            }
            
            return $venta;
        });

        return response()->json([
            'status'   => 'success', 
            'venta_id' => $ventaFinalizada->id,
            'folio'    => $ventaFinalizada->folio
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error', 
            'mensaje' => 'Error técnico: ' . $e->getMessage()
        ], 500);
    }
}


    public function imprimirTicket($id)
    {
        // Intentamos cargar la venta con sus relaciones
        $venta = Venta::with(['detalles.producto', 'usuario'])->findOrFail($id);
        
        return view('ventas.ticket', compact('venta'));
    }

    public function pausarVenta(Request $request)
    {
        try {
            if (!$request->productos || count($request->productos) == 0) {
                return response()->json(['status' => 'error', 'mensaje' => 'No hay productos para pausar'], 400);
            }

            VentaEspera::create([
                'usuario_id'   => Auth::id(),
                'fecha_pausa'  => now(),
                'carrito_data' => $request->productos 
            ]);

            return response()->json(['status' => 'success', 'mensaje' => 'Venta pausada']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'mensaje' => $e->getMessage()], 500);
        }
    }

    public function recuperarVenta($id) {
        $venta = VentaEspera::findOrFail($id);
        $datos = $venta->carrito_data;
        
        $venta->delete();

        return response()->json([
            'status' => 'success',
            'carrito' => $datos
        ]);
    }

    public function cancelarVenta($id)
    {
        $venta = Venta::findOrFail($id);

        foreach ($venta->detalles as $detalle) {
            if ($detalle->producto) {
                $detalle->producto->increment('stock_actual', $detalle->cantidad);
            }
        }

        $venta->delete();

        return back()->with('success', 'Venta eliminada correctamente.');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
    public function abrirCajonManual() 
    {
        // Esta función solo sirve para cargar la "página fantasma" que imprime
        return view('admin.impresion.abrir_cajon');
    }
}
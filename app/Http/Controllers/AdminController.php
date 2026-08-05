<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CorteCaja;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use App\Models\VentaEspera;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $ventasHoy = Venta::whereDate('fecha', now())
            ->where('estado', '!=', 'cancelada')
            ->sum('total');

        $numVentas = Venta::whereDate('fecha', now())
            ->where('estado', '!=', 'cancelada')
            ->count();

        $productosBajoStock = Producto::where('stock_actual', '<=', 5)->count();

        $gastosHoy = Gasto::whereDate('created_at', now())->sum('monto');
        $comprasHoy = Compra::whereDate('created_at', now())->sum('costo_total');

        $ultimosCortes = CorteCaja::with('usuario')
            ->whereNotNull('fecha_cierre')
            ->orderBy('fecha_cierre', 'desc')
            ->limit(6)
            ->get();

        return view('admin.dashboard.index', compact(
            'ventasHoy',
            'numVentas',
            'productosBajoStock',
            'gastosHoy',
            'comprasHoy',
            'ultimosCortes'
        ));
    }

    public function productos()
    {
        $departamentos = DB::table('departamentos')->get();
        $productos = Producto::with('departamento')->get();

        return view('admin.productos.index', compact('productos', 'departamentos'));
    }

    public function inventarioCajero()
    {
        $productos = Producto::with('departamento')->get();
        $departamentos = DB::table('departamentos')->get();

        return view('ventas.inventario', compact('productos', 'departamentos'));
    }

    public function storeProducto(Request $request)
    {
        $request->validate([
            'descripcion' => 'required',
            'precio_costo' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock_actual' => 'required|numeric',
            'departamento_id' => 'required|exists:departamentos,id',
            'unidad_medida' => 'required',
        ]);

        try {
            $producto = new Producto;
            $producto->codigo_barras = $request->codigo_barras ?? 'INT'.date('ymd').rand(100, 999);
            $producto->descripcion = $request->descripcion;
            $producto->precio_costo = $request->precio_costo;
            $producto->precio_venta = $request->precio_venta;
            $producto->stock_actual = $request->stock_actual;
            $producto->stock_minimo = $request->stock_minimo ?? 0;
            $producto->departamento_id = $request->departamento_id;
            $producto->unidad_medida = $request->unidad_medida;
            $producto->es_granel = $request->has('es_granel');
            $producto->activo = true;
            $producto->save();

            return redirect()->back()->with('success', 'Producto registrado en inventario.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo guardar: '.$e->getMessage());
        }
    }

    public function updateProducto(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required',
            'precio_costo' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock_actual' => 'required|numeric',
            'departamento_id' => 'required|exists:departamentos,id',
            'unidad_medida' => 'required',
        ]);

        try {
            $producto = Producto::findOrFail($id);
            $producto->codigo_barras = $request->codigo_barras;
            $producto->descripcion = $request->descripcion;
            $producto->precio_costo = $request->precio_costo;
            $producto->precio_venta = $request->precio_venta;
            $producto->stock_actual = $request->stock_actual;
            $producto->stock_minimo = $request->stock_minimo ?? 0;
            $producto->departamento_id = $request->departamento_id;
            $producto->unidad_medida = $request->unidad_medida;
            $producto->es_granel = $request->has('es_granel');
            $producto->save();

            return redirect()->back()->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function destroyProducto($id)
    {
        Producto::destroy($id);

        return back()->with('success', 'Producto eliminado.');
    }

    /**
     * HISTORIAL DE CAJA (solo admin — turnos ya cerrados)
     */
    public function historialCajas()
    {
        $cortes = CorteCaja::with('usuario')
            ->whereNotNull('fecha_cierre')
            ->orderBy('fecha_cierre', 'desc')
            ->get();

        return view('admin.cajas.index', compact('cortes'));
    }

    public function detalleCaja($id)
    {
        $corte = CorteCaja::with('usuario')->findOrFail($id);

        $ventas = Venta::where('usuario_id', $corte->usuario_id)
            ->where('fecha', '>=', $corte->fecha_apertura)
            ->where('fecha', '<=', $corte->fecha_cierre)
            ->where('estado', '!=', 'cancelada')
            ->with(['detalles.producto'])
            ->orderBy('fecha', 'desc')
            ->get();

        $gastos = Gasto::where('created_at', '>=', $corte->fecha_apertura)
            ->where('created_at', '<=', $corte->fecha_cierre)
            ->orderBy('created_at', 'desc')
            ->get();

        $compras = Compra::with('producto')
            ->where('created_at', '>=', $corte->fecha_apertura)
            ->where('created_at', '<=', $corte->fecha_cierre)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.cajas.show', compact('corte', 'ventas', 'gastos', 'compras'));
    }

    public function usuariosIndex()
    {
        $usuarios = Usuario::all();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function usuariosStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'username' => 'required|string|unique:usuarios,username',
            'password' => 'required|min:4',
            'rol' => 'required',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'username' => $request->username,
            'password_hash' => Hash::make($request->password),
            'rol' => $request->rol,
            'activo' => 1,
        ]);

        return redirect()->back()->with('success', 'Nuevo usuario registrado.');
    }

    public function updateUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'username' => 'required|string|unique:usuarios,username,'.$id,
            'password' => 'nullable|min:4',
            'rol' => 'required',
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->username = $request->username;
        $usuario->rol = $request->rol;

        if ($request->filled('password')) {
            $usuario->password_hash = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->back()->with('success', 'Datos actualizados correctamente.');
    }

    public function destroy($id)
    {
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }

    public function reportes()
    {
        $reportes = Venta::with('usuario')->orderBy('fecha', 'desc')->get();

        return view('admin.reportes.index', compact('reportes'));
    }

    public function cancelarVenta($id)
    {
        try {
            $venta = Venta::findOrFail($id);
            $venta->delete();

            return redirect()->back()->with('success', 'La venta ha sido eliminada permanentemente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo eliminar la venta: '.$e->getMessage());
        }
    }

    public function historialCompras()
    {
        $compras = Compra::with('producto')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.compras.index', compact('compras'));
    }

    public function listarVentasEspera()
    {
        return response()->json(
            VentaEspera::where('usuario_id', auth()->id())
                ->orderBy('fecha_pausa', 'desc')
                ->get()
        );
    }
}
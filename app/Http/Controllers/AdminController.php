<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Usuario; 
use App\Models\CorteCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 

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

        $ultimosCortes = CorteCaja::with('usuario')
            ->orderBy('fecha_cierre', 'desc')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'ventasHoy', 
            'numVentas', 
            'productosBajoStock', 
            'ultimosCortes'
        ));
    }

    public function productos()
    {
        $departamentos = DB::table('departamentos')->get();
        $productos = Producto::with('departamento')->get();
        return view('admin.productos', compact('productos', 'departamentos'));
    }

    public function inventarioCajero() {
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
            'unitad_medida' => 'required',
        ]);

        try {
            $producto = new Producto();
            $producto->codigo_barras = $request->codigo_barras ?? 'INT' . date('ymd') . rand(100, 999);
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
             return redirect()->back()->with('error', 'No se pudo guardar: ' . $e->getMessage());
        }
    }

    public function destroyProducto($id)
    {
        Producto::destroy($id);
        return back()->with('success', 'Producto eliminado.');
    }

    /**
     * SISTEMA DE CORTE DE CAJA ACTUALIZADO
     */
public function corteCaja()
    {
        $montoInicial = session('monto_apertura', 0);
        $hoy = today();

        // 1. Sumamos Ventas
        $ventasDelTurno = Venta::where('usuario_id', Auth::id())
            ->whereDate('fecha', $hoy)
            ->where('estado', '!=', 'cancelada')
            ->sum('total');

        // 2. Sumamos Compras de Mercancía (Salidas)
        $totalCompras = \App\Models\Compra::whereDate('created_at', $hoy)
            ->sum('costo_total');

        // 3. Cálculo Final: (Fondo + Ventas) - Compras
        $totalSistema = ($montoInicial + $ventasDelTurno) - $totalCompras;

        return view('admin.corte', compact('ventasDelTurno', 'montoInicial', 'totalSistema', 'totalCompras'));
    }

    public function guardarCorte(Request $request)
    {
        $request->validate([
            'efectivo_real' => 'required|numeric',
            'ventas_esperadas' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $difference = $request->efectivo_real - $request->ventas_esperadas;

            // --- CORRECCIÓN DE FECHAS PARA MYSQL ---
            // Si la sesión trae un formato raro, Carbon lo limpia a YYYY-MM-DD HH:MM:SS
            $fechaAperturaRaw = session('hora_apertura') ?? now();
            $fechaApertura = \Carbon\Carbon::parse($fechaAperturaRaw)->format('Y-m-d H:i:s');
            $fechaCierre = now()->format('Y-m-d H:i:s');

            \App\Models\CorteCaja::create([
                'usuario_id'            => Auth::id(),
                'fecha_apertura'        => $fechaApertura, 
                'fecha_cierre'          => $fechaCierre,
                'monto_inicial'         => session('monto_apertura', 0),
                'total_ventas_efectivo' => $request->ventas_esperadas,
                'total_ventas_tarjeta'  => 0, 
                'total_esperado'        => $request->ventas_esperadas,
                'total_contado'         => $request->efectivo_real,
                'difference'            => $difference,
                'notas'                 => 'Corte de caja realizado'
            ]);

            DB::commit();

            // Mandamos a la ruta que limpia toda la sesión y cookies
            return redirect()->route('logout.especial');

        } catch (\Exception $e) {
            DB::rollBack();
            // Si vuelve a fallar, el dd() te dirá exactamente qué columna o dato es el pedo
            dd("Error al guardar el corte: " . $e->getMessage()); 
        }
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
            'activo' => 1
        ]);

        return redirect()->back()->with('success', 'Nuevo usuario registrado.');
    }

    public function updateUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'username' => 'required|string|unique:usuarios,username,' . $id,
            'password' => 'nullable|min:4', 
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->username = $request->username;

        if ($request->filled('password')) {
            $usuario->password_hash = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->back()->with('success', 'Datos de usuario actualizados.');
    }

    public function destroy($id)
    {
        if(Auth::id() == $id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }

    public function reportes()
    {
        $reportes = Venta::with('usuario')->orderBy('fecha', 'desc')->get();
        return view('admin.reportes', compact('reportes'));
    }

    public function historialCompras()
    {
        $compras = \App\Models\Compra::with('producto')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.compras.index', compact('compras'));
    }

    public function listarVentasEspera() {
        return response()->json(
            \App\Models\VentaEspera::where('usuario_id', auth()->id())
                ->orderBy('fecha_pausa', 'desc')
                ->get()
        );
    }

    /**
     * --- MÉTODOS DE CAJA (APERTURA Y CIERRE) ---
     */
    public function aperturaCaja(Request $request)
    {
        $request->validate(['monto_inicial' => 'required|numeric|min:0']);

        session([
            'turno_abierto' => true,
            'monto_apertura' => $request->monto_inicial,
            'hora_apertura' => now()
        ]);

        return redirect()->back()->with('success', 'Caja abierta.');
    }

    public function cierreSesion()
    {
        session()->forget(['turno_abierto', 'monto_apertura', 'hora_apertura']);
        return redirect()->route('admin.corte');
    }
}
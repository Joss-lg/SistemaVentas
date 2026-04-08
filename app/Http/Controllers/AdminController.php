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

    // --- VISTA PARA EL ADMINISTRADOR ---
    public function productos()
    {
        $departamentos = DB::table('departamentos')->get();
        $productos = Producto::with('departamento')->get();
        // Carga la vista de la carpeta admin
        return view('admin.productos', compact('productos', 'departamentos'));
    }

    // --- VISTA PARA EL CAJERO ---
    public function inventarioCajero() {
    $productos = Producto::with('departamento')->get();
    $departamentos = DB::table('departamentos')->get();
    return view('ventas.inventario', compact('productos', 'departamentos'));
}

    // --- MÉTODO DE GUARDADO CON ACTUALIZACIÓN DE STOCK ---
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
     * SISTEMA DE CORTE DE CAJA
     */
    public function corteCaja()
    {
        $ventasDelTurno = Venta::where('usuario_id', Auth::id())
            ->whereDate('fecha', today())
            ->sum('total');

        return view('admin.corte', compact('ventasDelTurno'));
    }

public function guardarCorte(Request $request)
    {
        // 1. Validación de lo que viene del formulario
        $request->validate([
            'efectivo_real' => 'required|numeric',
            'ventas_esperadas' => 'required|numeric',
        ]);
    
        try {
            // 2. Calculamos la diferencia
            $diferencia = $request->efectivo_real - $request->ventas_esperadas;

            // 3. Guardamos el registro en la base de datos
            CorteCaja::create([
                'usuario_id'            => Auth::id(),
                'fecha_apertura'        => now(), 
                'fecha_cierre'          => now(),
                'monto_inicial'         => $request->monto_inicial ?? 0,
                'total_ventas_efectivo' => $request->ventas_esperadas,
                'total_ventas_tarjeta'  => 0, 
                'total_esperado'        => $request->ventas_esperadas,
                'total_contado'         => $request->efectivo_real,
                'diferencia'            => $diferencia,
                'notas'                 => $request->notas ?? 'Corte de caja realizado'
            ]);

            // --- PROCESO DE CIERRE DE SESIÓN ---
            
            // Cerramos la sesión del cajero (tu modelo Usuario)
            Auth::logout();

            // Destruimos la sesión actual en el servidor por seguridad
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redireccionamos al login con el mensaje de éxito
            return redirect()->route('login')->with('success', '¡Corte guardado! Turno finalizado y sesión cerrada.');

        } catch (\Exception $e) {
            // Si hay un error al guardar, volvemos atrás para no cerrar la sesión por error
            return back()->withErrors(['error' => 'Error al guardar el corte: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * GESTIÓN DE USUARIOS (Cajeros)
     */
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

    public function usuariosDestroy($id)
    {
        if(Auth::id() == $id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * REPORTES
     */
    public function reportes()
    {
        $reportes = Venta::with('usuario')->orderBy('fecha', 'desc')->get();
        return view('admin.reportes', compact('reportes'));
    }
    public function historialCompras()
    {
        // Obtenemos las compras con la información del producto relacionado
        $compras = \App\Models\Compra::with('producto')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.compras.index', compact('compras'));
    }
    public function listarVentasEspera() {
    // Solo mostramos las del usuario actual para evitar confusiones
    return response()->json(
        \App\Models\VentaEspera::where('usuario_id', auth()->id())
            ->orderBy('fecha_pausa', 'desc')
            ->get()
    );
}
}
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Sin Login)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/logout-especial', [AuthController::class, 'logoutEspecial'])->name('logout.especial');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Login General)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- Configuración de Usuario (Tema Oscuro) ---
    Route::post('/user/theme', function (Request $request) {
        $usuario = Auth::user();
        $usuario->update(['tema' => $request->theme]);

        return response()->json(['res' => 'ok', 'nuevo_tema' => $request->theme]);
    })->name('user.theme');

    // --- Punto de Venta ---
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::post('/ventas/pausar', [VentaController::class, 'pausarVenta'])->name('ventas.pausar');
    Route::get('/admin/ventas-espera/listar', [AdminController::class, 'listarVentasEspera']);
    Route::get('/admin/ventas-espera/recuperar/{id}', [VentaController::class, 'recuperarVenta']);
    Route::get('/ventas/buscar-producto', [VentaController::class, 'buscarProducto'])->name('ventas.buscar');
    Route::get('/ventas/buscar-nombre', [VentaController::class, 'buscarPorNombre'])->name('ventas.buscarNombre');
    Route::post('/ventas/finalizar', [VentaController::class, 'finalizarVenta'])->name('ventas.finalizar');
    Route::get('/ventas/ticket/{id}', [VentaController::class, 'imprimirTicket'])->name('ventas.ticket');

    // --- Inventario Cajero ---
    Route::get('/ventas/inventario', [AdminController::class, 'inventarioCajero'])->name('ventas.inventario');
    Route::post('/inventario/agregar-stock', [InventarioController::class, 'agregarStock'])->name('inventario.agregar-stock');

    Route::get('/admin/impresion/abrir-cajon', function () {
        return view('admin.impresion.abrir_cajon');
    })->name('impresion.abrir-cajon');

    // --- Corte y Apertura de Caja ---
    Route::get('/admin/corte', [AdminController::class, 'corteCaja'])->name('admin.corte');
    Route::post('/admin/corte/guardar', [AdminController::class, 'guardarCorte'])->name('admin.corte.store');
    Route::post('/admin/caja/apertura', [AdminController::class, 'aperturaCaja'])->name('caja.apertura');

    /*
    |--------------------------------------------------------------------------
    | Rutas de Solo Administrador
    |--------------------------------------------------------------------------
    */
    Route::middleware(['soloAdmin'])->group(function () {

        // Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // CRUD de Productos
        Route::get('/admin/productos', [AdminController::class, 'productos'])->name('productos.index');
        Route::post('/admin/productos', [AdminController::class, 'storeProducto'])->name('productos.store');
        Route::put('/admin/productos/{id}', [AdminController::class, 'updateProducto'])->name('productos.update');
        Route::delete('/admin/productos/{id}', [AdminController::class, 'destroyProducto'])->name('productos.destroy');

        // CRUD de Usuarios / Cajeros
        Route::get('/admin/usuarios', [AdminController::class, 'usuariosIndex'])->name('admin.usuarios.index');
        Route::post('/admin/usuarios/guardar', [AdminController::class, 'usuariosStore'])->name('admin.usuarios.store');
        Route::put('/admin/usuarios/{id}', [AdminController::class, 'updateUsuario'])->name('admin.usuarios.update');
        Route::delete('/admin/usuarios/{id}', [AdminController::class, 'destroy'])->name('admin.usuarios.destroy');

        // Reportes, Compras y Flujo de caja
        Route::get('/admin/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
        Route::get('/admin/gastos', [GastoController::class, 'index'])->name('admin.gastos');
        Route::post('/admin/gastos', [GastoController::class, 'store'])->name('gastos.store');
        Route::get('/admin/compras', [AdminController::class, 'historialCompras'])->name('admin.compras.index');
        Route::get('/admin/reporte-excel-general', [GastoController::class, 'descargarReporte'])->name('admin.reporte.excel');

        // --- NUEVO: Historial de Caja ---
        Route::get('/admin/cajas', [AdminController::class, 'historialCajas'])->name('admin.cajas.index');
        Route::get('/admin/cajas/{id}', [AdminController::class, 'detalleCaja'])->name('admin.cajas.show');

        // Acciones de Control
        Route::delete('/admin/ventas/cancelar/{id}', [AdminController::class, 'cancelarVenta'])->name('ventas.cancelar');
        Route::get('/admin/abrir-cajon-manual', [VentaController::class, 'abrirCajonManual'])->name('admin.cajon.abrir');
        Route::post('/ventas/sincronizar-offline', [VentaController::class, 'sincronizar'])->name('admin.ventas.sincronizar');
    });
});
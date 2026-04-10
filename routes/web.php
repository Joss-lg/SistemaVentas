<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Importante para el tema
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\GastoController;
use App\Models\Usuario;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- RUTA PARA GUARDAR EL TEMA (MODO OSCURO/CLARO) ---
    // Esta es la que agregamos para que tu JS pueda guardar la elección del cajero/admin
    Route::post('/user/theme', function (Request $request) {
        $usuario = Auth::user();
        // Usamos update para guardar 'claro' u 'oscuro' en tu tabla usuarios
        $usuario->update([
            'tema' => $request->theme
        ]);
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

    // --- Inventario para el Cajero ---
    Route::get('/ventas/inventario', [AdminController::class, 'inventarioCajero'])->name('ventas.inventario');

    // --- Corte de Caja ---
    Route::get('/admin/corte', [AdminController::class, 'corteCaja'])->name('admin.corte');
    Route::post('/admin/corte/guardar', [AdminController::class, 'guardarCorte'])->name('admin.corte.store');
    
    Route::post('/inventario/agregar-stock', [InventarioController::class, 'agregarStock'])->name('inventario.agregar-stock');
    
    Route::get('/ventas/ticket/{id}', [VentaController::class, 'imprimirTicket'])->name('ventas.ticket');
    Route::get('/admin/impresion/abrir-cajon', function () {
        return view('admin.impresion.abrir_cajon');
    })->name('impresion.abrir-cajon');
    
    /*
    |--------------------------------------------------------------------------
    | Rutas de Solo Administrador
    |--------------------------------------------------------------------------
    */
    Route::middleware(['soloAdmin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/admin/productos', [AdminController::class, 'productos'])->name('productos.index');
        Route::post('/admin/productos', [AdminController::class, 'storeProducto'])->name('productos.store');
        Route::delete('/admin/productos/{id}', [AdminController::class, 'destroyProducto'])->name('productos.destroy');

        Route::get('/admin/usuarios', [AdminController::class, 'usuariosIndex'])->name('admin.usuarios.index');
        Route::post('/admin/usuarios/guardar', [AdminController::class, 'usuariosStore'])->name('admin.usuarios.store');
        Route::get('/admin/usuarios/{id}/edit', [AdminController::class, 'edit'])->name('usuarios.edit');
        Route::put('/admin/usuarios/{id}', [AdminController::class, 'updateUsuario'])->name('usuarios.update');
        Route::delete('/admin/usuarios/{id}', [AdminController::class, 'destroy'])->name('usuarios.destroy');

        Route::get('/admin/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
        Route::delete('/admin/ventas/cancelar/{id}', [VentaController::class, 'cancelarVenta'])->name('ventas.cancelar');
        Route::get('/admin/gastos', [GastoController::class, 'index'])->name('admin.gastos');
        Route::post('/admin/gastos', [GastoController::class, 'store'])->name('gastos.store');
        Route::delete('/admin/ventas/{id}/cancelar', [VentaController::class, 'cancelarRealizada'])->name('admin.ventas.cancelar');
        Route::get('/admin/compras', [AdminController::class, 'historialCompras'])->name('admin.compras.index');
        Route::get('/admin/abrir-cajon-manual', [VentaController::class, 'abrirCajonManual'])->name('admin.cajon.abrir');

        Route::get('/admin/reporte-excel-general', [GastoController::class, 'descargarReporte'])->name('admin.reporte.excel');
        Route::post('/ventas/sincronizar-offline', [VentaController::class, 'sincronizar'])->name('admin.ventas.sincronizar');
    });
});

Route::get('/', function () {
    return redirect()->route('login');
});
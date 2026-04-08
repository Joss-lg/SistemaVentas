<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\GastoController;
/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

// Mostrar formulario e inicio de sesión
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Ruta de Logout
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

    // --- Punto de Venta ---
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    // Rutas para pausar la compra
    // Estas deben estar bien escritas para que el JS las encuentre
    Route::post('/ventas/pausar', [VentaController::class, 'pausarVenta'])->name('ventas.pausar');
    Route::get('/admin/ventas-espera/listar', [AdminController::class, 'listarVentasEspera']);
    Route::get('/admin/ventas-espera/recuperar/{id}', [VentaController::class, 'recuperarVenta']);
    
    // Rutas de cobro
    Route::get('/ventas/buscar-producto', [VentaController::class, 'buscarProducto'])->name('ventas.buscar');
    Route::get('/ventas/buscar-nombre', [VentaController::class, 'buscarPorNombre'])->name('ventas.buscarNombre');
    Route::post('/ventas/finalizar', [VentaController::class, 'finalizarVenta'])->name('ventas.finalizar');

    // --- Inventario para el Cajero (Solo Consulta) ---
    Route::get('/ventas/inventario', [AdminController::class, 'inventarioCajero'])->name('ventas.inventario');

    // --- Corte de Caja (Accesible para Cajeros y Admin) ---
    Route::get('/admin/corte', [AdminController::class, 'corteCaja'])->name('admin.corte');
    Route::post('/admin/corte/guardar', [AdminController::class, 'guardarCorte'])->name('admin.corte.store');
    // Entrada de proveedores
    Route::post('/inventario/agregar-stock', [InventarioController::class, 'agregarStock'])->name('inventario.agregar-stock');
    // Impresion de tikects
    Route::get('/ventas/ticket/{id}', [VentaController::class, 'imprimirTicket'])->name('ventas.ticket');
    Route::get('/admin/impresion/abrir-cajon', function () {
    return view('admin.impresion.abrir_cajon');
    })->name('impresion.abrir-cajon');
    
    /*
    |--------------------------------------------------------------------------
    | Rutas de Solo Administrador (Middleware soloAdmin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['soloAdmin'])->group(function () {

        // Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Gestión de Productos (Admin)
        Route::get('/admin/productos', [AdminController::class, 'productos'])->name('productos.index');
        Route::post('/admin/productos', [AdminController::class, 'storeProducto'])->name('productos.store');
        Route::delete('/admin/productos/{id}', [AdminController::class, 'destroyProducto'])->name('productos.destroy');

        // Gestión de Usuarios/Cajeros
        Route::get('/admin/usuarios', [AdminController::class, 'usuariosIndex'])->name('admin.usuarios.index');
        Route::post('/admin/usuarios/guardar', [AdminController::class, 'usuariosStore'])->name('admin.usuarios.store');
        
        // Ruta para mostrar el formulario de edición
        Route::get('/admin/usuarios/{id}/edit', [AdminController::class, 'edit'])->name('usuarios.edit');

        // Rutas de Usuarios en AdminController
        Route::put('/admin/usuarios/{id}', [AdminController::class, 'updateUsuario'])->name('usuarios.update');
        Route::delete('/admin/usuarios/{id}', [AdminController::class, 'destroy'])->name('usuarios.destroy');

        // Reportes de Ventas
        Route::get('/admin/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
        Route::delete('/admin/ventas/cancelar/{id}', [VentaController::class, 'cancelarVenta'])->name('ventas.cancelar');
        Route::get('/admin/gastos', [App\Http\Controllers\GastoController::class, 'index'])->name('admin.gastos');
        Route::post('/admin/gastos', [App\Http\Controllers\GastoController::class, 'store'])->name('gastos.store');
        // Cancelacion de venta
        Route::delete('/admin/ventas/{id}/cancelar', [VentaController::class, 'cancelarRealizada'])->name('admin.ventas.cancelar');
        // Historial Proveedores
        Route::get('/admin/compras', [AdminController::class, 'historialCompras'])->name('admin.compras.index');
        // Abrir caja de dinero
        Route::get('/admin/abrir-cajon-manual', [App\Http\Controllers\VentaController::class, 'abrirCajonManual'])->name('admin.cajon.abrir');
        // Ruta para ver la vista de gastos
        Route::get('/admin/gastos', [App\Http\Controllers\GastoController::class, 'index'])->name('admin.gastos');

        // Ruta para procesar el formulario de nuevo gasto
        Route::post('/admin/gastos', [App\Http\Controllers\GastoController::class, 'store'])->name('gastos.store');

        // RUTA PARA EL EXCEL (La que pediste)
        Route::get('/admin/reporte-excel-general', [App\Http\Controllers\GastoController::class, 'descargarReporte'])->name('admin.reporte.excel');
        // Para que jale sin internet
        Route::post('/ventas/sincronizar-offline', [App\Http\Controllers\VentaController::class, 'sincronizar'])->name('admin.ventas.sincronizar');
    });

});

// Ruta por defecto
Route::get('/', function () {
    return redirect()->route('login');
});
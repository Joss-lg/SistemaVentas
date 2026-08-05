<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ConfiguracionHardwareController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentaController;
use App\Http\Middleware\VerificarCajaAbierta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas & Autenticación
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::get('/logout-especial', 'logoutEspecial')->name('logout.especial');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (General - Requiere Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Ajustes de Usuario
    Route::post('/user/theme', function (Request $request) {
        Auth::user()->update(['tema' => $request->theme]);
        return response()->json(['res' => 'ok', 'nuevo_tema' => $request->theme]);
    })->name('user.theme');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO DE CAJA (Apertura de Turno)
    |--------------------------------------------------------------------------
    | Sin middleware caja.abierta porque es justo lo que abre el turno.
    */
    Route::prefix('caja')->name('caja.')->controller(CajaController::class)->group(function () {
        Route::get('/apertura', 'aperturaIndex')->name('apertura');
        Route::post('/apertura', 'aperturaStore')->name('apertura.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas que requieren turno de caja abierto (cajeros; admins exentos)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['caja.abierta'])->group(function () {

        // Punto de Venta (POS)
        Route::prefix('ventas')->name('ventas.')->controller(VentaController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/pausar', 'pausarVenta')->name('pausar');
            Route::get('/buscar-producto', 'buscarProducto')->name('buscar');
            Route::get('/buscar-nombre', 'buscarPorNombre')->name('buscarNombre');
            Route::post('/finalizar', 'finalizarVenta')->name('finalizar');
            Route::get('/ticket/{id}', 'imprimirTicket')->name('ticket');
            Route::get('/recuperar/{id}', 'recuperarVenta')->name('recuperar');
        });

        // Inventario para Cajeros
        Route::get('/ventas/inventario', [AdminController::class, 'inventarioCajero'])->name('ventas.inventario');
        Route::post('/inventario/agregar-stock', [InventarioController::class, 'agregarStock'])->name('inventario.agregar-stock');

        // Impresión / Cajón
        Route::get('/admin/impresion/abrir-cajon', fn() => view('admin.impresion.abrir_cajon'))->name('impresion.abrir-cajon');

        // Corte de Caja (Cajero) — ahora vive en CajaController
        Route::get('/admin/corte', [CajaController::class, 'corteIndex'])->name('admin.corte');
        Route::post('/admin/corte/guardar', [CajaController::class, 'corteStore'])->name('admin.corte.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas Exclusivas de Administrador
    |--------------------------------------------------------------------------
    */
    Route::middleware(['soloAdmin'])->prefix('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // CRUD Productos
        Route::prefix('productos')->name('productos.')->controller(AdminController::class)->group(function () {
            Route::get('/', 'productos')->name('index');
            Route::post('/', 'storeProducto')->name('store');
            Route::put('/{id}', 'updateProducto')->name('update');
            Route::delete('/{id}', 'destroyProducto')->name('destroy');
        });

        // CRUD Departamentos
        Route::resource('departamentos', DepartamentoController::class)->except(['create', 'edit', 'show']);

        // CRUD Usuarios / Cajeros
        Route::prefix('usuarios')->name('admin.usuarios.')->controller(AdminController::class)->group(function () {
            Route::get('/', 'usuariosIndex')->name('index');
            Route::post('/guardar', 'usuariosStore')->name('store');
            Route::put('/{id}', 'updateUsuario')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Gastos y Compras

        Route::post('/gastos', [GastoController::class, 'store'])->name('gastos.store');
        Route::get('/compras', [AdminController::class, 'historialCompras'])->name('admin.compras.index');

        // Historial y Detalle de Cajas
        Route::prefix('cajas')->name('admin.cajas.')->controller(AdminController::class)->group(function () {
            Route::get('/', 'historialCajas')->name('index');
            Route::get('/{id}', 'detalleCaja')->name('show');
        });

        // Reportes
        Route::get('/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
        Route::get('/reporte-excel-general', [GastoController::class, 'descargarReporte'])->name('admin.reporte.excel');

        // Ventas en espera & Acciones de Control
        Route::get('/ventas-espera/listar', [AdminController::class, 'listarVentasEspera'])->name('admin.ventas.espera');
        Route::delete('/ventas/cancelar/{id}', [AdminController::class, 'cancelarVenta'])->name('ventas.cancelar');
        Route::get('/abrir-cajon-manual', [VentaController::class, 'abrirCajonManual'])->name('admin.cajon.abrir');
        Route::post('/ventas/sincronizar-offline', [VentaController::class, 'sincronizar'])->name('admin.ventas.sincronizar');

        // Configuración de Hardware (impresora, cajón, báscula)
        Route::get('/configuracion-hardware', [ConfiguracionHardwareController::class, 'edit'])->name('admin.hardware.edit');
        Route::put('/configuracion-hardware', [ConfiguracionHardwareController::class, 'update'])->name('admin.hardware.update');
    });
});
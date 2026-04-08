<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Producto;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas básicas para el administrador
        $hoy = Carbon::today();
        
        $ventasHoy = Venta::whereDate('fecha', $hoy)->where('estado', 'completada')->sum('total');
        $numVentas = Venta::whereDate('fecha', $hoy)->where('estado', 'completada')->count();
        $productosBajoStock = Producto::where('stock_actual', '<=', \DB::raw('stock_minimo'))->count();

        return view('admin.dashboard', compact('ventasHoy', 'numVentas', 'productosBajoStock'));
    }
}
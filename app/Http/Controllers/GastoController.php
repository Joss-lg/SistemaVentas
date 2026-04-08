<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use App\Exports\ReporteGeneralExport;
use Maatwebsite\Excel\Facades\Excel;

class GastoController extends Controller
{
    public function index()
    {
        // Traemos los gastos del día actual
        $gastos = Gasto::whereDate('created_at', today())->orderBy('id', 'desc')->get();
        $totalDia = $gastos->sum('monto');

        return view('admin.gastos', compact('gastos', 'totalDia'));
    }

    public function store(Request $request)
    {
        Gasto::create([
            'descripcion' => strtoupper($request->descripcion),
            'monto' => $request->monto,
            'categoria' => $request->categoria ?? 'GENERAL',
            'usuario' => auth()->user()->name ?? 'Admin'
        ]);

        return back()->with('success', 'Gasto registrado correctamente');
    }
    public function descargarReporte()
    {
        $fecha = now()->format('d-m-Y');
        return Excel::download(new ReporteGeneralExport, "Reporte_General_{$fecha}.xlsx");
    }
}
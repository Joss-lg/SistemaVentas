<?php

namespace App\Exports;

use App\Models\Venta;
use App\Models\Gasto;
use App\Models\Producto;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;

class ReporteGeneralExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new FlujoCajaSheet(),
            new InventarioSheet(),
        ];
    }
}

// PESTAÑA 1: FLUJO DE CAJA (Ventas y Gastos)
class FlujoCajaSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        // Usamos 'fecha' de tu tabla ventas
        $ventas = Venta::select('fecha as momento', 'total', DB::raw("'INGRESO (VENTA)' as tipo"), 'tipo_pago as detalle')->get();
        
        // Usamos 'created_at' de tu tabla gastos
        $gastos = Gasto::select('created_at as momento', 'monto as total', DB::raw("'EGRESO (GASTO)' as tipo"), 'descripcion as detalle')->get();

        return $ventas->concat($gastos)->sortByDesc('momento');
    }

    public function headings(): array {
        return ["Fecha/Hora", "Monto ($)", "Tipo", "Detalle/Método"];
    }

    public function title(): string { return 'Flujo de Caja'; }
}

// PESTAÑA 2: INVENTARIO ACTUAL
class InventarioSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        // Traemos datos reales de tu tabla 'productos'
        return Producto::select('codigo_barras', 'descripcion', 'precio_costo', 'precio_venta', 'stock_actual', 'stock_minimo')->get();
    }

    public function headings(): array {
        return ["Código", "Producto", "Costo ($)", "Venta ($)", "Stock Actual", "Mínimo"];
    }

    public function title(): string { return 'Inventario'; }
    
}
class CortesCajaSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        // Traemos los datos de tu tabla 'cortes_caja' (ajusta los nombres si varían)
        return DB::table('cortes_caja')
            ->select('id', 'fecha', 'hora_apertura', 'total_ventas', 'total_gastos', 'saldo_final', 'cajero')
            ->orderBy('fecha', 'desc')
            ->get();
    }

    public function headings(): array {
        return ["ID", "Fecha", "Apertura", "Ventas (+)", "Gastos (-)", "Saldo Final", "Cajero"];
    }

    public function title(): string {
        return 'Cortes de Caja';
    }


// Y no olvides agregarla al array de sheets arriba:
public function sheets(): array
{
    return [
        new FlujoCajaSheet(),
        new InventarioSheet(),
        new CortesCajaSheet(), // <--- Nueva pestaña
    ];
}
}

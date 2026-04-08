<?php

namespace App\Exports;

use App\Models\Venta;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\CorteCaja;
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
            new VentasSheet(),      // Pestaña 1: Solo Ventas
            new GastosSheet(),      // Pestaña 2: Solo Gastos (Egresos)
            new CortesCajaSheet(),  // Pestaña 3: Cortes de Caja
            new InventarioSheet(),  // Pestaña 4: Inventario
        ];
    }
}

// PESTAÑA 1: VENTAS (INGRESOS)
class VentasSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        // Quitamos 'status' porque no existe en tu tabla
        return Venta::select('fecha', 'total', 'tipo_pago')
                    ->orderBy('fecha', 'desc')
                    ->get();
    }
    public function headings(): array {
        return ["Fecha/Hora", "Monto ($)", "Método de Pago"];
    }
    public function title(): string { return 'Ventas'; }
}

// PESTAÑA 2: GASTOS (EGRESOS)
class GastosSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        // Ajustado a monto y descripcion segun tu Excel
        return Gasto::select('created_at', 'monto', 'descripcion')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
    public function headings(): array {
        return ["Fecha/Hora", "Monto ($)", "Descripción/Proveedor"];
    }
    public function title(): string { return 'Gastos - Egresos'; }
}

// PESTAÑA 3: CORTES DE CAJA
class CortesCajaSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        // Usamos los campos exactos de tu tabla: total_esperado, total_contado, difference
        return CorteCaja::select(
            'id', 
            'fecha_cierre', 
            'total_esperado', 
            'total_contado', 
            'difference', 
            'usuario_id'
        )->orderBy('fecha_cierre', 'desc')->get();
    }
    public function headings(): array {
        return ["ID Corte", "Fecha Cierre", "Esperado ($)", "Contado ($)", "Diferencia ($)", "Cajero ID"];
    }
    public function title(): string { return 'Cortes de Caja'; }
}

// PESTAÑA 4: INVENTARIO
class InventarioSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Producto::select('codigo_barras', 'descripcion', 'precio_costo', 'precio_venta', 'stock_actual', 'stock_minimo')->get();
    }
    public function headings(): array {
        return ["Código", "Producto", "Costo ($)", "Venta ($)", "Stock Actual", "Mínimo"];
    }
    public function title(): string { return 'Inventario'; }
}
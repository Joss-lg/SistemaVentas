<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['slug' => 'inventario.ver', 'nombre' => 'Ver Inventario', 'descripcion' => 'Ver inventario en modo solo lectura'],
            ['slug' => 'productos.gestionar', 'nombre' => 'Gestionar Productos', 'descripcion' => 'Agregar, editar y borrar productos — ve la vista admin completa en vez de la simple'],
            ['slug' => 'departamentos.gestionar', 'nombre' => 'Gestionar Categorías', 'descripcion' => 'Gestionar categorías/departamentos'],
            ['slug' => 'cajon.abrir', 'nombre' => 'Abrir Cajón', 'descripcion' => 'Abrir el cajón de dinero manualmente'],
            ['slug' => 'caja.historial', 'nombre' => 'Ver Historial de Cajas', 'descripcion' => 'Ver el historial de todos los cortes de caja'],
            ['slug' => 'caja.detalle', 'nombre' => 'Ver Detalle de Caja', 'descripcion' => 'Ver el detalle de un corte de caja específico'],
            ['slug' => 'compras.ver', 'nombre' => 'Ver Compras', 'descripcion' => 'Ver historial de compras'],
            ['slug' => 'reportes.ver', 'nombre' => 'Ver Reportes', 'descripcion' => 'Ver reportes generales'],
            ['slug' => 'reportes.descargar', 'nombre' => 'Descargar Reportes', 'descripcion' => 'Descargar reporte Excel'],
            ['slug' => 'dashboard.ver', 'nombre' => 'Ver Dashboard', 'descripcion' => 'Ver el dashboard administrativo'],
            ['slug' => 'usuarios.gestionar', 'nombre' => 'Gestionar Usuarios', 'descripcion' => 'Gestionar usuarios/cajeros, incluye asignar roles y permisos'],
            ['slug' => 'hardware.configurar', 'nombre' => 'Configurar Hardware', 'descripcion' => 'Configurar impresora, báscula y cajón'],
        ];

        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(['slug' => $permiso['slug']], $permiso);
        }
    }
}
@props(['permisosActuales' => []])

<div class="bg-zinc-100 dark:bg-black/50 p-6 rounded-[2rem] border border-zinc-200 dark:border-white/10 mt-4 space-y-5">
    <h3 class="text-[10px] font-black italic text-red-600 uppercase tracking-widest">Asignar Permisos</h3>

    @php
        $grupos = [
            'Inventario / Categorías' => [
                'inventario.ver' => 'Ver Inventario',
                'productos.gestionar' => 'Gestionar Productos',
                'departamentos.gestionar' => 'Gestionar Categorías',
            ],
            'Cajón' => [
                'cajon.abrir' => 'Abrir Cajón',
            ],
            'Caja' => [
                'caja.historial' => 'Ver Historial de Cajas',
                'caja.detalle' => 'Ver Detalle de Caja',
            ],
            'Compras' => [
                'compras.ver' => 'Ver Compras',
            ],
            'Reportes' => [
                'reportes.ver' => 'Ver Reportes',
                'reportes.descargar' => 'Descargar Reporte Excel',
            ],
            'Administración' => [
                'dashboard.ver' => 'Ver Dashboard',
                'usuarios.gestionar' => 'Gestionar Usuarios',
                'hardware.configurar' => 'Configurar Hardware',
            ],
        ];
    @endphp

    @foreach($grupos as $grupoNombre => $items)
        <div>
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">{{ $grupoNombre }}</p>
            <div class="space-y-2.5">
                @foreach($items as $slug => $label)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="permisos[]" value="{{ $slug }}"
                            {{ in_array($slug, $permisosActuales) ? 'checked' : '' }}
                            class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                        <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
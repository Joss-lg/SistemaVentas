@props(['permisosActuales' => []])

<div class="bg-zinc-100 dark:bg-black/50 p-6 rounded-[2rem] border border-zinc-200 dark:border-white/10 mt-4">
    <h3 class="text-[10px] font-black italic text-red-600 uppercase tracking-widest mb-4">Asignar Permisos</h3>
    <div class="space-y-3">
        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" name="permisos[]" value="ver_inventario"
                {{ in_array('ver_inventario', $permisosActuales) ? 'checked' : '' }}
                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-blue-600 focus:ring-blue-500">
            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Inventario</span>
        </label>
        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" name="permisos[]" value="ver_reportes"
                {{ in_array('ver_reportes', $permisosActuales) ? 'checked' : '' }}
                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-blue-600 focus:ring-blue-500">
            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Reportes</span>
        </label>
    </div>
</div>
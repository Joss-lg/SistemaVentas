<div x-show="openEdit" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-[#0d0d0d] w-full max-w-md max-h-[90vh] overflow-y-auto rounded-3xl p-6 md:p-8 shadow-2xl border border-zinc-200 dark:border-white/10" @click.away="openEdit = false">
        
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100 dark:border-white/5">
            <h2 class="text-2xl font-black italic uppercase text-zinc-900 dark:text-white">
                EDITAR <span class="text-red-600">OPERATIVO</span>
            </h2>
            <button type="button" @click="openEdit = false" class="text-zinc-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form :action="'/admin/usuarios/' + userEdit.id" method="POST" class="space-y-4">
            @csrf 
            @method('PUT')

            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Nombre Completo</label>
                <input type="text" name="nombre" x-model="userEdit.nombre" required 
                    class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-bold text-sm border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Usuario</label>
                    <input type="text" name="username" x-model="userEdit.username" required 
                        class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-mono font-bold text-sm border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Rango / Rol</label>
                    <select name="rol" x-model="userEdit.rol" 
                        class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-black text-sm uppercase border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors">
                        <option value="cajero">CAJERO</option>
                        <option value="admin">ADMIN</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Contraseña (Opcional)</label>
                <input type="password" name="password" placeholder="••••••••" 
                    class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-mono text-sm border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors">
            </div>
            <div class="bg-zinc-100 dark:bg-black/50 p-6 rounded-[2rem] border border-zinc-200 dark:border-white/10 mt-4 space-y-5">
                <h3 class="text-[10px] font-black italic text-red-600 uppercase tracking-widest">Asignar Permisos</h3>

                <div>
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Inventario / Categorías</p>
                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="inventario.ver" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Inventario</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="productos.gestionar" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Gestionar Productos</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="departamentos.gestionar" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Gestionar Categorías</span>
                        </label>
                    </div>
                </div>

                <div>
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Cajón</p>
                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="cajon.abrir" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Abrir Cajón</span>
                        </label>
                    </div>
                </div>

                <div>
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Caja</p>
                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="caja.historial" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Historial de Cajas</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="caja.detalle" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Detalle de Caja</span>
                        </label>
                    </div>
                </div>

                <div>
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Compras</p>
                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="compras.ver" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Compras</span>
                        </label>
                    </div>
                </div>

                <div>
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Reportes</p>
                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="reportes.ver" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Reportes</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="reportes.descargar" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Descargar Reporte Excel</span>
                        </label>
                    </div>
                </div>

                <div>
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Administración</p>
                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="dashboard.ver" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Ver Dashboard</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="usuarios.gestionar" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Gestionar Usuarios</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="permisos[]" value="hardware.configurar" x-model="userEdit.permisos"
                                class="w-5 h-5 rounded-lg border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-red-600 focus:ring-red-500">
                            <span class="text-xs font-black italic uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-red-600 transition-colors">Configurar Hardware</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="openEdit = false" 
                    class="w-1/2 py-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 font-black uppercase tracking-wider text-xs rounded-xl transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                    class="w-1/2 py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-black italic uppercase tracking-wider text-xs rounded-xl shadow-lg border-b-2 border-red-900 transition-all">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
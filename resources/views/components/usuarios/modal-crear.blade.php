<div x-show="openCreate" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-[#0d0d0d] w-full max-w-md rounded-3xl p-6 md:p-8 shadow-2xl border border-zinc-200 dark:border-white/10" @click.away="openCreate = false">
        
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100 dark:border-white/5">
            <h2 class="text-2xl font-black italic uppercase text-zinc-900 dark:text-white">
                NUEVO <span class="text-red-600">USUARIO</span>
            </h2>
            <button type="button" @click="openCreate = false" class="text-zinc-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Nombre Completo</label>
                <input type="text" name="nombre" placeholder="EJ. JUAN PÉREZ" required 
                    class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-bold text-sm border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors uppercase">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Usuario</label>
                    <input type="text" name="username" placeholder="USER" required 
                        class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-mono font-bold text-sm border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Rango / Rol</label>
                    <select name="rol" 
                        class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-black text-sm uppercase border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors">
                        <option value="cajero">CAJERO</option>
                        <option value="admin">ADMIN</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1.5">Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required 
                    class="w-full bg-zinc-50 dark:bg-black p-3.5 rounded-xl text-zinc-900 dark:text-white font-mono text-sm border border-zinc-200 dark:border-white/10 focus:border-red-600 dark:focus:border-red-600 outline-none transition-colors">
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="openCreate = false" 
                    class="w-1/2 py-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 font-black uppercase tracking-wider text-xs rounded-xl transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                    class="w-1/2 py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-black italic uppercase tracking-wider text-xs rounded-xl shadow-lg border-b-2 border-red-900 transition-all">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
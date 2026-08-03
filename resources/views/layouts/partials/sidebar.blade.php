@php
    $userRol = strtolower(Auth::user()->rol ?? '');
    $esAdmin = ($userRol === 'admin' || $userRol === 'administrador');
    $temaActual = Auth::user()->tema ?? 'claro';
    
    $permisosRaw = Auth::user()->permisos ?? [];
    $permisos = is_array($permisosRaw) ? $permisosRaw : (json_decode($permisosRaw, true) ?? []);

    $puedeVerInventario = $esAdmin || in_array('ver_inventario', $permisos) || $userRol === 'cajero';

    $active = "bg-red-600 text-white shadow-[0_6px_14px_rgba(220,38,38,0.3)]";
    $inactive = "text-zinc-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-white/5";
@endphp

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<aside
    x-data="{ colapsado: localStorage.getItem('sidebar_colapsado') === 'true' }"
    x-init="$watch('colapsado', v => localStorage.setItem('sidebar_colapsado', v))"
    :class="colapsado ? 'w-20' : 'w-64'"
    class="min-w-0 bg-white dark:bg-[#0d0d0d] flex flex-col border-r border-zinc-200 dark:border-white/5 transition-[width] duration-300 relative z-20 flex-shrink-0 h-screen overflow-hidden select-none"
>
    {{-- HEADER --}}
    <div class="p-4 flex items-center justify-between h-16 border-b border-zinc-100 dark:border-white/5 shrink-0">
        <h2 x-show="!colapsado" x-transition class="text-zinc-800 dark:text-white text-lg font-black italic uppercase tracking-tighter leading-none whitespace-nowrap pl-2">
            F1 <span class="text-red-600">{{ $esAdmin ? 'PANEL' : 'CAJERO' }}</span>
        </h2>
        <h2 x-show="colapsado" x-transition class="text-zinc-800 dark:text-white text-lg font-black italic uppercase tracking-tighter leading-none mx-auto">
            <span class="text-red-600">F1</span>
        </h2>

        <button @click="colapsado = !colapsado"
            class="p-2 text-zinc-500 hover:text-red-600 dark:text-zinc-400 dark:hover:text-white rounded-lg hover:bg-zinc-100 dark:hover:bg-white/5 transition-colors focus:outline-none cursor-pointer"
            :class="colapsado ? 'mx-auto mt-1' : ''"
            :title="colapsado ? 'Expandir menú' : 'Colapsar menú'">
            <i class="fas fa-bars text-base"></i>
        </button>
    </div>

    {{-- NAVEGACIÓN --}}
    <nav class="flex-1 px-3 space-y-1.5 overflow-y-auto overflow-x-hidden no-scrollbar pt-4">
        @if($esAdmin)
        <button id="btn-abrir-cajon"
            :title="colapsado ? 'Abrir Cajón' : ''"
            class="w-full flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all border border-yellow-600/30 text-yellow-600 hover:bg-yellow-600 hover:text-white group mb-3 cursor-pointer"
            :class="colapsado ? 'justify-center' : 'space-x-3'">
            <i class="fas fa-cash-register text-sm flex-shrink-0"></i>
            <span x-show="!colapsado" x-transition class="whitespace-nowrap">Abrir Cajón</span>
        </button>
        @endif

        <p x-show="!colapsado" x-transition class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-2 ml-1 border-b border-zinc-100 dark:border-white/5 pb-1 whitespace-nowrap">Operación</p>

        <a href="{{ route('ventas.index') }}"
            :title="colapsado ? 'Punto de Venta' : ''"
            :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('ventas.index') ? "'$active'" : "'$inactive'" }}]"
            class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
            <i class="fas fa-shopping-cart text-sm flex-shrink-0"></i>
            <span x-show="!colapsado" x-transition class="whitespace-nowrap">Punto de Venta</span>
        </a>

        @if(!$esAdmin && $puedeVerInventario)
            <a href="{{ route('ventas.inventario') }}"
                :title="colapsado ? 'Inventario' : ''"
                :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('ventas.inventario') ? "'$active'" : "'$inactive'" }}]"
                class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                <i class="fas fa-boxes-stacked text-sm flex-shrink-0"></i>
                <span x-show="!colapsado" x-transition class="whitespace-nowrap">Inventario</span>
            </a>
        @endif

        <a href="{{ route('admin.corte') }}"
            :title="colapsado ? 'Corte de Caja' : ''"
            :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.corte') ? "'$active'" : "'$inactive'" }}]"
            class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
            <i class="fas fa-file-invoice-dollar text-sm flex-shrink-0"></i>
            <span x-show="!colapsado" x-transition class="whitespace-nowrap">Corte de Caja</span>
        </a>

        @if(!$esAdmin && in_array('ver_reportes', $permisos))
            <a href="{{ route('admin.reportes') }}"
                :title="colapsado ? 'Reporte Ventas' : ''"
                :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.reportes') ? "'$active'" : "'$inactive'" }}]"
                class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                <i class="fas fa-chart-bar text-sm flex-shrink-0"></i>
                <span x-show="!colapsado" x-transition class="whitespace-nowrap">Reporte Ventas</span>
            </a>
        @endif

        @if($esAdmin)
            <div class="pt-3 mt-3 border-t border-zinc-100 dark:border-white/5 space-y-1.5">
                <p x-show="!colapsado" x-transition class="text-[9px] font-black text-red-600 uppercase tracking-[0.2em] mb-2 ml-1 whitespace-nowrap">Administración</p>

                <a href="{{ route('admin.dashboard') }}" :title="colapsado ? 'Dashboard' : ''" :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.dashboard') ? "'$active'" : "'$inactive'" }}]" class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                    <i class="fas fa-th-large text-sm flex-shrink-0"></i>
                    <span x-show="!colapsado" x-transition class="whitespace-nowrap">Dashboard</span>
                </a>

                <a href="{{ route('productos.index') }}" :title="colapsado ? 'Productos' : ''" :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('productos.*') ? "'$active'" : "'$inactive'" }}]" class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                    <i class="fas fa-box text-sm flex-shrink-0"></i>
                    <span x-show="!colapsado" x-transition class="whitespace-nowrap">Productos</span>
                </a>

                {{-- NUEVO: HISTORIAL DE CAJA --}}
                <a href="{{ route('admin.cajas.index') }}" :title="colapsado ? 'Historial de Caja' : ''" :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.cajas.*') ? "'$active'" : "'$inactive'" }}]" class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                    <i class="fas fa-clock-rotate-left text-sm flex-shrink-0"></i>
                    <span x-show="!colapsado" x-transition class="whitespace-nowrap">Historial de Caja</span>
                </a>

                <a href="{{ route('admin.reportes') }}" :title="colapsado ? 'Reportes General' : ''" :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.reportes') ? "'$active'" : "'$inactive'" }}]" class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                    <i class="fas fa-chart-bar text-sm flex-shrink-0"></i>
                    <span x-show="!colapsado" x-transition class="whitespace-nowrap">Reportes General</span>
                </a>

                <a href="{{ route('admin.compras.index') }}" :title="colapsado ? 'Historial Proveedores' : ''" :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.compras.index') ? "'$active'" : "'$inactive'" }}]" class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                    <i class="fas fa-history text-sm flex-shrink-0"></i>
                    <span x-show="!colapsado" x-transition class="whitespace-nowrap">Proveedores</span>
                </a>

                <a href="{{ route('admin.gastos') }}" :title="colapsado ? 'Flujo de caja' : ''" :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.gastos') ? "'$active'" : "'$inactive'" }}]" class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                    <i class="fas fa-hand-holding-dollar text-sm flex-shrink-0"></i>
                    <span x-show="!colapsado" x-transition class="whitespace-nowrap">Flujo de caja</span>
                </a>

                <a href="{{ route('admin.usuarios.index') }}" :title="colapsado ? 'Gestionar Cajeros' : ''" :class="[colapsado ? 'justify-center' : 'space-x-3', {{ request()->routeIs('admin.usuarios.*') ? "'$active'" : "'$inactive'" }}]" class="flex items-center p-3 rounded-xl font-bold italic uppercase text-xs tracking-wide transition-all">
                    <i class="fas fa-user-plus text-sm flex-shrink-0"></i>
                    <span x-show="!colapsado" x-transition class="whitespace-nowrap">Gestionar Cajeros</span>
                </a>
            </div>
        @endif

        <div class="pt-3" x-show="!colapsado" x-transition>
            <div class="p-3 bg-zinc-100 dark:bg-[#1a1a1a] border-l-4 border-red-600 rounded-r-xl">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-0.5 whitespace-nowrap">Terminal Activa</p>
                <span class="text-zinc-900 dark:text-white font-black italic uppercase text-xs whitespace-nowrap">{{ Auth::user()->username }}</span>
            </div>
        </div>
    </nav>

    <div class="p-3 border-t border-zinc-200 dark:border-white/5 space-y-3 shrink-0">
        <div class="flex items-center justify-between bg-zinc-100 dark:bg-white/5 p-2 rounded-xl border border-zinc-200 dark:border-white/5"
            :class="colapsado ? 'justify-center' : 'justify-between'">
            <span x-show="!colapsado" x-transition class="text-[9px] font-black text-zinc-400 uppercase tracking-widest whitespace-nowrap">Modo Oscuro</span>
            <button id="theme-toggle" class="relative inline-flex items-center h-5 w-10 rounded-full transition-colors focus:outline-none flex-shrink-0 cursor-pointer {{ $temaActual === 'oscuro' ? 'bg-red-600' : 'bg-zinc-300' }}">
                <span id="switch-dot" class="inline-block w-3.5 h-3.5 transform bg-white rounded-full transition-transform {{ $temaActual === 'oscuro' ? 'translate-x-[1.375rem]' : 'translate-x-1' }}"></span>
            </button>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                :title="colapsado ? 'Cerrar Sesión' : ''"
                :class="colapsado ? 'justify-center' : 'space-x-2 px-2'"
                class="w-full text-zinc-400 hover:text-red-600 text-[10px] font-black uppercase tracking-[0.15em] flex items-center transition-colors py-1 cursor-pointer">
                <i class="fas fa-power-off text-xs flex-shrink-0"></i>
                <span x-show="!colapsado" x-transition class="whitespace-nowrap">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>
@extends('layouts.admin')

@section('content')
{{-- 
    ELIMINAMOS 'sm:ml-64' porque eso es lo que te movía el diseño a la derecha. 
    Aseguramos 'w-full' para que use todo el espacio que liberamos en el layout.
--}}
<div class="p-6 bg-zinc-50 dark:bg-black min-h-screen text-zinc-900 dark:text-white transition-colors duration-300 w-full">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-zinc-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-wallet text-red-500"></i> Control de Gastos y Egresos
            </h1>
            <p class="text-zinc-500 dark:text-gray-400">Gestiona las salidas de dinero de la caja física.</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-gray-800 shadow-lg transition-colors">
            <span class="text-zinc-500 dark:text-gray-400 block text-sm uppercase font-bold">Salida Hoy</span>
            <span class="text-3xl font-mono text-red-500 font-bold">${{ number_format($totalDia, 2) }}</span>
        </div>
    </div>

    {{-- 
        MANTENEMOS TU GRID: 1 columna en móvil, 3 en pantallas grandes.
        Como ahora el contenedor es más ancho, la tabla (col-span-2) se verá mucho mejor.
    --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 w-full">
        
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-zinc-200 dark:border-gray-800 shadow-2xl transition-colors">
                <h3 class="text-xl font-bold mb-6 border-b border-zinc-100 dark:border-gray-800 pb-2 text-zinc-800 dark:text-white uppercase italic tracking-tighter">Nuevo Gasto Manual</h3>
                
                <form action="{{ route('gastos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-zinc-500 dark:text-gray-400 mb-1 font-bold uppercase tracking-widest text-[10px]">Descripción del Gasto</label>
                        <input type="text" name="descripcion" required placeholder="Ej. Pago de Luz" 
                            class="w-full bg-zinc-50 dark:bg-black border border-zinc-300 dark:border-gray-700 rounded-lg p-3 text-zinc-900 dark:text-white focus:border-red-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm text-zinc-500 dark:text-gray-400 mb-1 font-bold uppercase tracking-widest text-[10px]">Monto ($)</label>
                        <input type="number" step="0.01" name="monto" required placeholder="0.00"
                            class="w-full bg-zinc-50 dark:bg-black border border-zinc-300 dark:border-gray-700 rounded-lg p-3 text-zinc-900 dark:text-white focus:border-red-500 focus:outline-none transition-all font-mono text-lg">
                    </div>

                    <div>
                        <label class="block text-sm text-zinc-500 dark:text-gray-400 mb-1 font-bold uppercase tracking-widest text-[10px]">Categoría</label>
                        <select name="categoria" class="w-full bg-zinc-50 dark:bg-black border border-zinc-300 dark:border-gray-700 rounded-lg p-3 text-zinc-900 dark:text-white focus:border-red-500 focus:outline-none">
                            <option value="GENERAL">General</option>
                            <option value="INVENTARIO">Inventario / Mercancía</option>
                            <option value="SERVICIOS">Servicios (Luz/Agua/Net)</option>
                            <option value="PERSONAL">Pago Personal</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black italic uppercase tracking-widest py-4 rounded-xl shadow-lg shadow-red-900/20 transition-all transform hover:scale-[1.02]">
                        REGISTRAR SALIDA
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-gray-800 shadow-2xl overflow-hidden transition-colors">
                <div class="p-6 border-b border-zinc-200 dark:border-gray-800 bg-zinc-50 dark:bg-transparent">
                    <h3 class="text-xl font-black italic uppercase tracking-tighter text-zinc-800 dark:text-white">Movimientos <span class="text-red-600">Recientes</span></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-zinc-100 dark:bg-black/50 text-zinc-500 dark:text-gray-400 uppercase text-[10px] tracking-[0.2em]">
                            <tr>
                                <th class="p-4 font-black">Descripción</th>
                                <th class="p-4 font-black">Categoría</th>
                                <th class="p-4 font-black text-center">Hora</th>
                                <th class="p-4 font-black text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-gray-800">
                            @forelse($gastos as $g)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="p-4 font-bold italic text-zinc-800 dark:text-zinc-200">{{ $g->descripcion }}</td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $g->categoria == 'INVENTARIO' ? 'bg-blue-600/10 text-blue-500' : 'bg-zinc-100 dark:bg-gray-800 text-zinc-500 dark:text-gray-300' }}">
                                        {{ $g->categoria }}
                                    </span>
                                </td>
                                <td class="p-4 text-center text-zinc-400 dark:text-gray-500 text-xs font-bold">{{ $g->created_at->format('h:i A') }}</td>
                                <td class="p-4 text-right font-mono text-red-600 dark:text-red-400 font-black">-${{ number_format($g->monto, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-zinc-400 italic">No hay gastos registrados el día de hoy.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-green-500/20 dark:border-green-500/30 hover:border-green-500 transition-all shadow-lg group">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-500/10 p-3 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-excel text-3xl text-green-500"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black italic uppercase tracking-tighter text-zinc-800 dark:text-white">Reporte General</h3>
                            <p class="text-[10px] text-green-500 font-mono font-bold tracking-widest uppercase leading-none">Excel Completo</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reporte.excel') }}" 
                       class="flex items-center justify-center gap-2 w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-black italic uppercase py-4 px-10 rounded-xl transition-all shadow-lg shadow-green-900/40">
                        <i class="fas fa-download"></i> Descargar Reporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
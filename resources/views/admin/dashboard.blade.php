@extends('layouts.admin')

@section('title', 'Panel de Control')

@section('content')
<div class="space-y-8">
    {{-- Encabezado --}}
    <div class="flex items-center justify-between border-b border-white/5 pb-6">
        <div>
            <h2 class="text-4xl font-black italic tracking-tighter uppercase text-white">
                Resumen <span class="text-red-600">General</span>
            </h2>
            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.3em] mt-1">
                Bienvenido, {{ Auth::user()->username }}
            </p>
        </div>
        <div class="text-right">
            <span class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest">Estado del Sistema</span>
            <span class="inline-flex items-center gap-2 text-green-500 font-bold italic text-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                Online
            </span>
        </div>
    </div>

    {{-- Tarjetas de Estadísticas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#0d0d0d] border border-white/5 p-8 rounded-2xl relative overflow-hidden group hover:border-red-600/30 transition-all shadow-2xl">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-cash-register text-6xl text-red-600"></i>
            </div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Ventas Hoy (Completadas)</p>
            <h3 class="text-5xl font-black italic text-white tracking-tighter">
                ${{ number_format($ventasHoy, 2) }}
            </h3>
            <div class="mt-4 h-1 w-full bg-zinc-900 rounded-full overflow-hidden">
                <div class="h-full bg-red-600 w-2/3"></div>
            </div>
        </div>

        <div class="bg-[#0d0d0d] border border-white/5 p-8 rounded-2xl relative overflow-hidden group hover:border-blue-600/30 transition-all shadow-2xl">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-shopping-cart text-6xl text-blue-600"></i>
            </div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Número de Ventas</p>
            <h3 class="text-5xl font-black italic text-white tracking-tighter">
                {{ $numVentas }}
            </h3>
            <div class="mt-4 h-1 w-full bg-zinc-900 rounded-full overflow-hidden">
                <div class="h-full bg-blue-600 w-1/2"></div>
            </div>
        </div>

        <div class="bg-[#0d0d0d] border border-white/5 p-8 rounded-2xl relative overflow-hidden group hover:border-orange-600/30 transition-all shadow-2xl">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-exclamation-triangle text-6xl text-orange-600"></i>
            </div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Productos Bajo Stock</p>
            <h3 class="text-5xl font-black italic text-orange-600 tracking-tighter">
                {{ $productosBajoStock }}
            </h3>
            <div class="mt-4 h-1 w-full bg-zinc-900 rounded-full overflow-hidden">
                <div class="h-full bg-orange-600 w-1/4"></div>
            </div>
        </div>
    </div>

    {{-- NUEVA SECCIÓN: Últimos Cortes de Caja --}}
    <div class="bg-[#0d0d0d] border border-white/5 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <h3 class="text-xl font-black italic text-white uppercase tracking-tighter">
                Últimos <span class="text-red-600">Cortes de Caja</span>
            </h3>
            <i class="fas fa-history text-zinc-600"></i>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-900/50">
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Cajero</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Esperado</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Contado</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Diferencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($ultimosCortes as $corte)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4 text-sm text-zinc-400 font-medium">
                            {{ \Carbon\Carbon::parse($corte->fecha_cierre)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-white font-bold italic">
                            {{ $corte->usuario->username ?? 'Cajero' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-400">
                            ${{ number_format($corte->total_esperado, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            ${{ number_format($corte->total_contado, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-black italic uppercase {{ $corte->difference < 0 ? 'bg-red-600/10 text-red-500' : 'bg-green-600/10 text-green-500' }}">
                                ${{ number_format($corte->difference, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-600 italic text-sm">
                            No hay registros de cortes recientes en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
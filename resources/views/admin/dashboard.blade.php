@extends('layouts.admin')

@section('title', 'Panel de Control')

@section('content')
{{-- Nota: Para que los iconos funcionen, asegúrate de tener FontAwesome en tu layout --}}

<div class="max-w-[1600px] mx-auto space-y-10">
    
    {{-- Encabezado del Panel --}}
    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-white/10 pb-6">
        <div>
            <h2 class="text-4xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white leading-none">
                RESUMEN <span class="text-red-600">GENERAL</span>
            </h2>
            <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.4em] mt-2 italic">
                SISTEMA GESTIÓN ABARROTES - CONTROL DE OPERACIONES
            </p>
        </div>
        <div class="bg-zinc-100 dark:bg-white/5 px-4 py-2 rounded-lg border border-zinc-200 dark:border-white/5">
            <span class="text-green-500 font-black italic text-xs flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span> ESTADO: ONLINE
            </span>
        </div>
    </div>

    {{-- Grid de Indicadores Principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full">
        
        {{-- Indicador: Ventas del Día --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 border-b-8 border-b-red-600 p-8 rounded-t-3xl rounded-b-md shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative flex items-center min-h-[190px]">
            <div class="relative z-10 w-full">
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3 italic">Ventas de Hoy</p>
                <h3 class="text-6xl font-black italic text-zinc-900 dark:text-white tracking-tighter">
                    ${{ number_format($ventasHoy, 2) }}
                </h3>
            </div>
            <i class="fas fa-chart-line absolute -right-2 -bottom-4 text-9xl text-zinc-900 dark:text-white opacity-[0.05] group-hover:opacity-10 transition-all duration-500"></i>
        </div>

        {{-- Indicador: Volumen de Transacciones --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 border-b-8 border-b-blue-600 p-8 rounded-t-3xl rounded-b-md shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative flex items-center min-h-[190px]">
            <div class="relative z-10 w-full">
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3 italic">Ventas Realizadas</p>
                <h3 class="text-6xl font-black italic text-zinc-900 dark:text-white tracking-tighter">
                    {{ $numVentas }}
                </h3>
            </div>
            <i class="fas fa-tag absolute -right-2 -bottom-4 text-9xl text-zinc-900 dark:text-white opacity-[0.05] group-hover:opacity-10 transition-all duration-500"></i>
        </div>

        {{-- Indicador: Inventario Crítico --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 border-b-8 border-b-orange-600 p-8 rounded-t-3xl rounded-b-md shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative flex items-center min-h-[190px]">
            <div class="relative z-10 w-full">
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3 italic">Stock Crítico</p>
                <h3 class="text-6xl font-black italic text-orange-600 tracking-tighter">
                    {{ $productosBajoStock }}
                </h3>
            </div>
            <i class="fas fa-box-open absolute -right-2 -bottom-4 text-9xl text-zinc-900 dark:text-white opacity-[0.05] group-hover:opacity-10 transition-all duration-500"></i>
        </div>

    </div>

    {{-- Sección de Auditoría: Cortes de Caja --}}
    <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden mt-6">
        <div class="px-10 py-8 border-b border-zinc-100 dark:border-white/5 flex justify-between items-center bg-zinc-50/50 dark:bg-white/[0.02]">
            <h3 class="text-xl font-black italic text-zinc-900 dark:text-white uppercase tracking-tighter leading-none">
                ÚLTIMOS <span class="text-red-600">CORTES DE CAJA</span>
            </h3>
        </div>
        
    <div class="overflow-x-auto px-6 pb-6 mt-4">
        <table class="w-full text-left border-separate border-spacing-y-3">
            <thead>
                <tr class="text-zinc-500 dark:text-zinc-400 font-black uppercase italic text-[10px] tracking-[0.2em] px-6">
                    <th class="px-6 py-2">Fecha de Cierre</th>
                    <th class="px-6 py-2">Responsable</th>
                    <th class="px-6 py-2">Contado Real</th>
                    <th class="px-6 py-2 text-right">Diferencia</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($ultimosCortes as $corte)
                <tr class="bg-white dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all rounded-2xl shadow-sm dark:shadow-none">
                    <td class="px-6 py-5 font-mono text-zinc-500 dark:text-zinc-400 rounded-l-2xl border-l border-y border-zinc-200 dark:border-white/5">
                        {{ \Carbon\Carbon::parse($corte->fecha_cierre)->format('d/m/Y H:i') }}
                    </td>
                    
                    <td class="px-6 py-5 font-black italic text-zinc-800 dark:text-zinc-100 uppercase border-y border-zinc-200 dark:border-white/5">
                        <span class="text-orange-600 dark:text-orange-500">
                            {{ $corte->usuario->username ?? 'SISTEMA' }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-5 font-bold text-zinc-900 dark:text-white border-y border-zinc-200 dark:border-white/5">
                        ${{ number_format($corte->total_contado, 2) }}
                    </td>
                    
                    <td class="px-6 py-5 text-right rounded-r-2xl border-r border-y border-zinc-200 dark:border-white/5">
                        <span class="inline-block px-4 py-1.5 rounded-lg text-[10px] font-black italic uppercase {{ $corte->difference < 0 ? 'bg-red-500/10 text-red-500' : 'bg-emerald-500/10 text-emerald-400' }} border {{ $corte->difference < 0 ? 'border-red-500/20' : 'border-emerald-500/20' }}">
                            ${{ number_format($corte->difference, 2) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-zinc-400 dark:text-zinc-600 uppercase italic font-black tracking-widest">
                        No se encontraron registros de cortes recientes.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</div>
@endsection
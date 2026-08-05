@extends('layouts.cajero')

@section('title', 'Historial de Caja')

@section('content')
<div class="w-full space-y-8 p-4 md:p-8 transition-colors duration-300">

    {{-- Header a pantalla completa --}}
    <div class="w-full border-b border-zinc-200 dark:border-white/5 pb-6">
        <h2 class="text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
            HISTORIAL DE <span class="text-red-600">CAJA</span>
        </h2>
        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-1">
            REGISTRO DE TODOS LOS CORTES REALIZADOS
        </p>
    </div>

    {{-- Tabla Full Width --}}
    <div class="w-full bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
        <div class="h-1.5 w-full bg-gradient-to-r from-red-600 via-red-900 to-black"></div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-[#0d0d0d] text-zinc-400 dark:text-zinc-500 uppercase text-[9px] tracking-[0.2em] border-b border-zinc-100 dark:border-white/5">
                        <th class="p-5 pl-8 font-black">Cajero</th>
                        <th class="p-5 text-center font-black">Cierre</th>
                        <th class="p-5 text-right font-black">Esperado</th>
                        <th class="p-5 text-right font-black">Contado</th>
                        <th class="p-5 text-right font-black">Diferencia</th>
                        <th class="p-5 pr-8 text-right font-black w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse($cortes as $corte)
                    <tr class="group hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-5 pl-8 font-black italic text-xl uppercase text-zinc-900 dark:text-white group-hover:text-red-600 transition-colors">
                            {{ $corte->usuario->nombre ?? 'N/A' }}
                        </td>
                        <td class="p-5 text-center text-xs font-mono font-bold tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($corte->fecha_cierre)->format('d/m/Y') }}
                        </td>
                        <td class="p-5 text-right font-black italic text-lg text-zinc-900 dark:text-white tabular-nums">
                            ${{ number_format($corte->total_esperado, 2) }}
                        </td>
                        <td class="p-5 text-right font-black italic text-lg text-zinc-900 dark:text-white tabular-nums">
                            ${{ number_format($corte->total_contado, 2) }}
                        </td>
                        <td class="p-5 text-right font-black italic text-lg tabular-nums {{ $corte->difference < 0 ? 'text-red-600 dark:text-red-500' : 'text-emerald-600 dark:text-emerald-500' }}">
                            {{ $corte->difference >= 0 ? '+' : '' }}${{ number_format($corte->difference, 2) }}
                        </td>
                        <td class="p-5 pr-8 text-right">
                            <a href="{{ route('admin.cajas.show', $corte->id) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-zinc-100 dark:bg-zinc-800/80 text-zinc-400 dark:text-zinc-300 group-hover:bg-red-600 group-hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Ver <i class="fas fa-chevron-right text-[8px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-zinc-400 dark:text-zinc-600 font-black italic uppercase text-xs tracking-wider">
                            Aún no hay cortes registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
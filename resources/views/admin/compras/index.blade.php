@extends('layouts.admin')

@section('title', 'Historial de Compras')

@section('content')
<div class="space-y-8 p-4 md:p-0 transition-colors duration-300">
    {{-- Cabecera --}}
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between border-b border-zinc-200 dark:border-white/5 pb-6 gap-4">
        <div>
            <h2 class="text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
                ENTRADAS DE <span class="text-red-600">PROVEEDORES</span>
            </h2>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-2 ml-1">Auditoría de abastecimiento y costos de adquisición</p>
        </div>
        
        <div class="flex gap-2">
            <button class="bg-zinc-100 dark:bg-white/5 hover:bg-zinc-200 dark:hover:bg-white/10 text-zinc-600 dark:text-white p-4 rounded-xl transition-all shadow-lg border border-zinc-200 dark:border-white/10">
                <i class="fas fa-file-download"></i>
            </button>
        </div>
    </div>

    {{-- Tabla de Registros --}}
    <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden transition-colors">
        <div class="h-1.5 w-full bg-gradient-to-r from-blue-600 via-red-600 to-zinc-900"></div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-white/[0.02]">
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.2em]">Fecha y Hora</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.2em]">Producto / Insumo</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.2em] text-center">Cantidad</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.2em] text-right">Inversión Total</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.2em] text-center">Método</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse($compras as $compra)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-white/[0.02] transition-all group">
                        <td class="px-6 py-5">
                            <div class="text-zinc-900 dark:text-white font-bold text-xs">
                                {{ $compra->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-zinc-400 dark:text-zinc-600 font-black italic">
                                {{ $compra->created_at->format('H:i A') }}
                            </div>
                        </td>
                        
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-zinc-900 dark:text-white font-black italic uppercase text-sm group-hover:text-red-600 transition-colors">
                                    {{ $compra->producto->descripcion ?? 'PRODUCTO ELIMINADO' }}
                                </span>
                                <span class="text-[9px] text-zinc-400 dark:text-zinc-600 font-bold uppercase tracking-tighter">
                                    ID: {{ $compra->producto_id }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-block px-3 py-1 bg-zinc-100 dark:bg-white/5 rounded-lg text-zinc-900 dark:text-white font-black italic">
                                +{{ number_format($compra->cantidad, 0) }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-right">
                            <span class="text-lg font-black text-blue-600 dark:text-blue-500 italic tracking-tighter">
                                ${{ number_format($compra->costo_total, 2) }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black italic uppercase bg-blue-600/10 text-blue-600 dark:text-blue-500 border border-blue-600/20 shadow-sm">
                                {{ $compra->metodo_pago }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-truck-loading text-5xl text-zinc-200 dark:text-white/5 mb-4"></i>
                                <span class="text-zinc-400 dark:text-zinc-600 italic font-black uppercase text-xs tracking-widest">
                                    No hay registros de entradas, wey.
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Resumen de Gastos --}}
    <div class="flex justify-end">
        <div class="bg-zinc-900 dark:bg-blue-600 p-8 rounded-3xl shadow-2xl text-right min-w-[300px] transform hover:rotate-1 transition-transform border border-white/10">
            <span class="block text-[10px] font-black text-white/50 uppercase mb-1 tracking-widest">Inversión Total en Almacén</span>
            <span class="text-4xl font-black text-white italic uppercase tracking-tighter">
                ${{ number_format($compras->sum('costo_total'), 2) }}
            </span>
        </div>
    </div>
</div>
@endsection
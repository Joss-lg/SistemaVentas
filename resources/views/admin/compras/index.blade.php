@extends('layouts.admin')

@section('title', 'Historial de Compras')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between border-b border-white/5 pb-6">
        <h2 class="text-4xl font-black italic tracking-tighter uppercase text-white">
            Entradas de <span class="text-red-600">Proveedores</span>
        </h2>
    </div>

    <div class="bg-[#0d0d0d] border border-white/5 rounded-2xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-900/50">
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Producto</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Cantidad</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Costo Total (Pago)</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Método</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($compras as $compra)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4 text-sm text-zinc-400">
                            {{ $compra->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-white font-bold italic">
                            {{ $compra->producto->descripcion ?? 'Producto eliminado' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-400">
                            {{ $compra->cantidad }} unidades
                        </td>
                        <td class="px-6 py-4 text-sm text-blue-500 font-black">
                            ${{ number_format($compra->costo_total, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black italic uppercase bg-blue-600/10 text-blue-500 border border-blue-600/20">
                                {{ $compra->metodo_pago }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-600 italic">
                            No hay registros de entradas de proveedores.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
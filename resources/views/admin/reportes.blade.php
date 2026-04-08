@extends('layouts.admin')

@section('title', 'Reporte de Ventas | Admin')

@section('content')
<div class="p-8 bg-[#0a0a0a] min-h-screen">
    
    <header class="mb-10">
        <div class="flex items-center space-x-4">
            <h1 class="text-4xl font-black italic text-white uppercase tracking-tighter">REPORTES DE</h1>
            <h1 class="text-4xl font-black italic text-red-600 uppercase tracking-tighter">VENTAS</h1>
        </div>
        <p class="text-gray-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-1 ml-1">Historial detallado y auditoría de transacciones</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-[#111] p-4 rounded-xl border border-white/5 shadow-xl">
            <span class="block text-[9px] font-black text-gray-500 uppercase mb-1">Filtrar por Fecha</span>
            <input type="date" class="bg-transparent text-white font-bold text-xs outline-none w-full">
        </div>
        <div class="bg-[#111] p-4 rounded-xl border border-white/5 shadow-xl">
            <span class="block text-[9px] font-black text-gray-500 uppercase mb-1">Cajero</span>
            <select class="bg-transparent text-white font-bold text-xs outline-none w-full cursor-pointer">
                <option value="">Todos los cajeros</option>
            </select>
        </div>
    </div>

    <div class="bg-[#0d0d0d] rounded-2xl border border-white/5 shadow-2xl relative overflow-hidden">
        <div class="h-1 w-full bg-gradient-to-r from-red-600 to-blue-600"></div>
        
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
    <thead>
        <tr class="text-[10px] text-gray-600 uppercase font-black tracking-widest">
            <th class="px-4 py-3">Folio / ID</th>
            <th class="px-4 py-3">Fecha y Hora</th>
            <th class="px-4 py-3 text-center">Cajero Responsable</th>
            <th class="px-4 py-3 text-center">Método</th>
            <th class="px-4 py-3 text-right">Total Venta</th>
            <th class="p-4 text-right italic text-red-600">Zona de Peligro</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-white/5">
        @foreach($reportes as $venta)
        <tr class="hover:bg-white/[0.03] transition-all group border-b border-white/5">
            <td class="px-4 py-5 font-mono text-blue-500 text-xs font-bold italic">
                #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
            </td>
            
            <td class="px-4 py-5">
                <div class="text-white font-black text-sm uppercase tracking-tighter">
                    {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                </div>
                <div class="text-[10px] text-gray-500 font-bold italic">
                    {{ \Carbon\Carbon::parse($venta->fecha)->format('H:i A') }}
                </div>
            </td>

            <td class="px-4 py-5 text-center">
                <span class="inline-flex items-center space-x-2 px-3 py-1 bg-white/5 rounded-lg border border-white/10">
                    <i class="fas fa-user text-[10px] text-red-500"></i>
                    <span class="text-white font-black text-[10px] uppercase">
                        {{ $venta->usuario->nombre ?? 'N/A' }}
                    </span>
                </span>
            </td>

            <td class="px-4 py-5 text-center">
                <span class="text-[10px] text-gray-400 font-black uppercase border-b border-gray-800 pb-1">
                    Efectivo
                </span>
            </td>

            <td class="px-4 py-5 text-right">
                <span class="text-xl font-black text-white italic tracking-tighter group-hover:text-green-500 transition-colors">
                    ${{ number_format($venta->total, 2) }}
                </span>
            </td>
            
            <td class="p-4 text-right">
                {{-- Validamos que solo el admin vea y use el botón --}}
                @if(auth()->user()->username == 'admin')
                <form action="{{ route('ventas.cancelar', $venta->id) }}" method="POST" onsubmit="return confirm('¿Borrar esta venta para siempre?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-rojo">
                        Anular y Borrar
                    </button>
                </form>
                @else
                    <span class="text-[9px] font-black text-zinc-700 italic uppercase">Bloqueado</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <div class="bg-red-600 p-6 rounded-2xl shadow-lg shadow-red-900/20 text-right min-w-[250px]">
            <span class="block text-[10px] font-black text-white/60 uppercase mb-1">Venta Total Acumulada</span>
            <span class="text-3xl font-black text-white italic uppercase tracking-tighter">
                ${{ number_format($reportes->sum('total'), 2) }}
            </span>
        </div>
    </div>

</div>
@endsection
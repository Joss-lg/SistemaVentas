@extends('layouts.cajero')

@section('title', 'Consulta de Inventario')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-white/5 pb-6">
        <div>
            <h2 class="text-4xl font-black italic tracking-tighter uppercase text-white">
                Consulta de <span class="text-red-600">Inventario</span>
            </h2>
            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.3em] mt-1">
                Listado de productos en existencia
            </p>
        </div>
        <a href="{{ route('ventas.index') }}" 
           class="bg-white/5 hover:bg-white/10 text-white px-6 py-3 rounded-xl border border-white/10 transition-all font-black italic text-xs uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Volver a Ventas
        </a>
    </div>

    <div class="bg-[#0d0d0d] border border-white/5 p-4 rounded-2xl shadow-2xl">
        <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="text" id="busqueda-inventario" placeholder="BUSCAR POR NOMBRE O CÓDIGO..." 
                   class="w-full bg-black border border-white/5 rounded-xl py-4 pl-12 pr-4 text-white font-bold italic focus:outline-none focus:border-red-600/50 transition-all uppercase text-sm">
        </div>
    </div>

    <div class="bg-[#0d0d0d] border border-white/5 rounded-2xl overflow-hidden shadow-2xl">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-black text-[10px] text-zinc-500 uppercase font-black tracking-[0.2em] border-b border-white/5">
                    <th class="p-6">Código de Barras</th>
                    <th class="p-6">Descripción</th>
                    <th class="p-6 text-center">Precio Venta</th>
                    <th class="p-6 text-center">Precio costo</th>
                    <th class="p-6 text-right">Existencia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($productos as $producto)
                <tr class="hover:bg-white/[0.02] transition-all group">
                    {{-- Código de Barras --}}
                    <td class="p-6 font-mono text-red-500 text-sm font-bold italic">
                        {{ $producto->codigo_barras }}
                    </td>

                    {{-- Descripción y Departamento (Corregido) --}}
                    <td class="p-6">
                        <div class="text-lg font-black text-white uppercase italic tracking-tighter">
                            {{ $producto->descripcion }}
                        </div>
                        <div class="text-[9px] text-red-600 font-bold uppercase tracking-widest mt-1">
                            {{-- Aquí corregimos para que no salga el JSON --}}
                            Depto: {{ $producto->departamento->nombre ?? 'General' }}
                        </div>
                    </td>

                    {{-- Precio Venta --}}
                    <td class="p-6 text-center">
                        <span class="text-xl font-black text-zinc-300 italic tracking-tighter">
                            ${{ number_format($producto->precio_venta, 2) }}
                        </span>
                    </td>
                    {{-- Precio costo --}} 
                        <td class="p-6 text-center">
                            <span class="text-gray-400 font-black italic text-lg">${{ number_format($producto->precio_costo, 2) }}</span>
                        </td>

                    {{-- Existencia (Usando stock_actual que es tu campo real) --}}
                    <td class="p-6 text-right">
                        <div class="inline-block px-4 py-2 rounded-lg {{ $producto->stock_actual <= 5 ? 'bg-red-600/10 text-red-500 border border-red-600/20' : 'bg-green-600/10 text-green-500 border border-green-600/20' }} font-black text-2xl italic tracking-tighter">
                            {{ number_format($producto->stock_actual, 2) }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
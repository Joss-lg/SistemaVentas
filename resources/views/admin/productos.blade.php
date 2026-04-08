@extends('layouts.admin')

@section('content')
<div x-data="{ openModal: false }" class="w-full">
    
    {{-- Notificaciones de Éxito/Error --}}
    @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded-xl mb-6 font-bold italic uppercase tracking-widest text-xs">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="text-white text-5xl font-black italic uppercase tracking-tighter leading-none">
                CONTROL DE <br> <span class="text-red-600">INVENTARIO</span>
            </h1>
        </div>
        <button @click="openModal = true" class="bg-red-600 hover:bg-red-700 text-white font-black italic uppercase px-12 py-6 rounded-2xl shadow-[0_15px_40px_rgba(220,38,38,0.3)] transition-all text-base tracking-widest">
            + NUEVO PRODUCTO
        </button>
    </div>

    {{-- MODAL DE REGISTRO --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/90 backdrop-blur-md" x-cloak>
        <div class="bg-[#0d0d0d] w-full max-w-3xl rounded-2xl overflow-hidden border border-white/10 shadow-2xl transform transition-all" @click.away="openModal = false">
            
            <div class="p-8 border-b border-white/5 flex justify-between items-center bg-[#0a0a0a]">
                <h2 class="text-white text-2xl font-black italic uppercase tracking-tighter">
                    NUEVO REGISTRO <span class="text-red-600">DE PRODUCTO</span>
                </h2>
                <button @click="openModal = false" class="text-gray-500 hover:text-white transition-colors text-2xl">&times;</button>
            </div>

            <form action="{{ route('productos.store') }}" method="POST" class="p-10 space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-8">
                    
                    <div>
                        <label class="block text-gray-600 text-[10px] font-black uppercase mb-3">
                            Código de Barras <span class="text-gray-400 font-normal">(Opcional)</span>
                        </label>
                        <input type="text" name="codigo_barras" placeholder="Escanear o dejar vacío" 
                            class="w-full bg-black border border-white/10 p-5 rounded-xl text-white font-bold outline-none focus:border-red-600 transition-all text-sm">
                    </div>

                    <div>
                        <label class="block text-gray-600 text-[10px] font-black uppercase mb-3">Departamento</label>
                        <select name="departamento_id" required class="w-full bg-black border border-white/10 p-5 rounded-xl text-white font-bold outline-none cursor-pointer appearance-none focus:border-red-600">
                            <option value="">-- Seleccionar --</option>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-500 text-[10px] font-black uppercase mb-2 tracking-widest">Descripción del Producto *</label>
                        <input type="text" name="descripcion" required class="w-full bg-black border border-white/10 p-4 rounded-xl text-white font-black italic uppercase outline-none focus:border-red-600 transition-all">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-500 text-[10px] font-black uppercase mb-2 tracking-widest">Precio Costo ($) *</label>
                        <input type="number" step="0.01" name="precio_costo" value="0" required class="w-full bg-black border border-white/10 p-4 rounded-xl text-blue-500 font-black text-xl outline-none focus:border-blue-600 transition-all">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-500 text-[10px] font-black uppercase mb-2 tracking-widest">Precio Venta ($) *</label>
                        <input type="number" step="0.01" name="precio_venta" value="0" required class="w-full bg-black border border-white/10 p-4 rounded-xl text-green-500 font-black text-xl outline-none focus:border-green-600 transition-all">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-500 text-[10px] font-black uppercase mb-2 tracking-widest">Stock Actual</label>
                        <input type="number" step="0.001" name="stock_actual" value="0" class="w-full bg-black border border-white/10 p-4 rounded-xl text-white font-black text-xl outline-none focus:border-red-600 transition-all">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-500 text-[10px] font-black uppercase mb-2 tracking-widest">Stock Mínimo</label>
                        <input type="number" step="0.001" name="stock_minimo" value="0" class="w-full bg-black border border-white/10 p-4 rounded-xl text-orange-500 font-black text-xl outline-none focus:border-orange-600 transition-all">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-500 text-[10px] font-black uppercase mb-2 tracking-widest">Unidad de Medida</label>
                        <select name="unidad_medida" class="w-full bg-black border border-white/10 p-4 rounded-xl text-white font-bold outline-none cursor-pointer">
                            <option value="pieza">Pieza</option>
                            <option value="kg">Kilogramo (Kg)</option>
                            <option value="litro">Litro</option>
                            <option value="gramo">Gramo</option>
                        </select>
                    </div>

                    <div class="col-span-1 flex items-center pt-6">
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <input type="checkbox" name="es_granel" value="1" class="w-6 h-6 bg-black border-white/10 rounded border-2 checked:bg-red-600 transition-all">
                            <span class="text-gray-400 font-black uppercase text-[10px] tracking-widest group-hover:text-white transition-colors">Producto a Granel</span>
                        </label>
                    </div>
                </div>

                <div class="flex space-x-4 pt-8">
                    <button type="button" @click="openModal = false" class="flex-1 bg-white/5 p-5 rounded-xl text-gray-500 font-black uppercase text-xs tracking-widest hover:bg-white/10 transition-all">CANCELAR</button>
                    <button type="submit" class="flex-1 bg-red-600 p-5 rounded-xl text-white font-black italic uppercase text-xs tracking-[0.2em] hover:bg-red-700 shadow-[0_10px_30px_rgba(220,38,38,0.3)] transition-all">GUARDAR PRODUCTO</button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="bg-[#0d0d0d] rounded-2xl border border-white/5 overflow-hidden shadow-2xl">
        <div class="h-1.5 w-full bg-gradient-to-r from-red-600 via-red-900 to-black"></div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] bg-white/[0.02]">
                        <th class="p-6">Producto / Descripción</th>
                        <th class="p-6 text-center">Código</th>
                        <th class="p-6 text-center">Costo</th>
                        <th class="p-6 text-center">Venta</th>
                        <th class="p-6 text-center">Stock</th>
                        <th class="p-6 text-center">Unidad</th>
                        <th class="p-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($productos as $producto)
                    <tr class="group hover:bg-white/[0.03] transition-all">
                        <td class="p-6">
                            <div class="flex flex-col">
                                <span class="text-white font-black italic uppercase text-lg group-hover:text-red-500 transition-colors">
                                    {{ $producto->descripcion }} {{-- Cambiado de 'nombre' a 'descripcion' --}}
                                </span>
                                <span class="text-gray-600 text-[10px] font-bold uppercase">
                                    {{ $producto->departamento->nombre ?? 'Sin Depto' }}
                                </span>
                            </div>
                        </td>

                        <td class="p-6 text-center text-blue-500 font-mono font-bold text-sm">
                            {{ $producto->codigo_barras }}
                        </td>

                        <td class="p-6 text-center">
                            <span class="text-gray-400 font-black italic text-lg">${{ number_format($producto->precio_costo, 2) }}</span>
                        </td>

                        <td class="p-6 text-center">
                            <span class="text-green-500 font-black italic text-xl">${{ number_format($producto->precio_venta, 2) }}</span>
                        </td>

                        <td class="p-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="font-black text-2xl italic {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-red-600 animate-pulse' : 'text-white' }}">
                                    {{ $producto->stock_actual }}
                                </span>
                                <span class="text-[9px] text-gray-600 font-black uppercase">Mín: {{ $producto->stock_minimo }}</span>
                            </div>
                        </td>

                        <td class="p-6 text-center">
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-gray-400 text-[10px] font-black uppercase">
                                {{ $producto->unidad_medida }}
                                @if($producto->es_granel) <span class="text-orange-500 ml-1">⚖</span> @endif
                            </span>
                        </td>

                        <td class="p-6 text-right space-x-2">
                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-600 hover:text-red-600 transition-colors p-2" onclick="return confirm('¿Eliminar producto?')">
                                    ELIMINAR
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
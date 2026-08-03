@extends('layouts.cajero')

@section('content')
<div class="p-6">
    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black italic text-zinc-800 dark:text-white uppercase tracking-tighter">
                Inventario <span class="text-orange-500">Admin</span>
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium uppercase tracking-widest italic">
                Control total por departamentos
            </p>
        </div>
        
        <button onclick="document.getElementById('modalProducto').classList.remove('hidden')" 
            class="bg-orange-600 hover:bg-orange-500 text-white font-black italic px-6 py-3 rounded-2xl transition-all shadow-[0_0_20px_rgba(234,88,12,0.3)] uppercase text-xs">
            + Nuevo Producto
        </button>
    </div>

    {{-- Listado por Departamentos --}}
    @foreach($productos->groupBy('departamento_id') as $deptoId => $productosDepto)
    <div class="mb-10">
        <h2 class="flex items-center gap-2 mb-4 text-orange-500 font-black italic uppercase tracking-widest text-sm">
            <span class="h-[2px] w-8 bg-orange-500"></span>
            Departamento: {{ $productosDepto->first()->departamento->nombre ?? 'Sin Clasificar' }} 
            <span class="text-zinc-500 text-[10px]">({{ $productosDepto->count() }} items)</span>
        </h2>

        <div class="bg-white dark:bg-zinc-900/50 rounded-3xl border border-zinc-200 dark:border-white/5 overflow-hidden shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-zinc-400 dark:text-zinc-500 font-black uppercase italic text-[10px] tracking-widest border-b border-zinc-200 dark:border-white/5">
                        <th class="px-6 py-4">Descripción / Código</th>
                        <th class="px-6 py-4 text-center">Costo</th>
                        <th class="px-6 py-4 text-center">Venta</th>
                        <th class="px-6 py-4 text-center">Stock Actual</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($productosDepto as $producto)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-white/[0.02] transition-all border-b border-zinc-100 dark:border-white/5">
                        <td class="px-6 py-4">
                            <div class="font-black italic text-zinc-800 dark:text-white uppercase leading-tight">
                                {{ $producto->descripcion }}
                            </div>
                            <div class="text-[10px] text-zinc-500 font-mono">{{ $producto->codigo_barras }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-zinc-500 italic">
                            ${{ number_format($producto->precio_costo, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-zinc-700 dark:text-zinc-300">
                            ${{ number_format($producto->precio_venta, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="px-3 py-1 rounded-full font-bold {{ $producto->stock_actual <= $producto->stock_minimo ? 'bg-red-500/10 text-red-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                                    {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}
                                </span>
                                @if($producto->es_granel)
                                    <span class="text-[9px] text-orange-500 font-black italic uppercase mt-1 tracking-tighter">Granel</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 text-zinc-400">
                                <button onclick='abrirModalEditar(@json($producto))' 
                                    class="hover:text-orange-500 transition-colors font-black italic uppercase text-[10px]">Editar</button>
                                <span class="opacity-20 font-light">|</span>
                                <button onclick="confirmarBaja({{ $producto->id }}, '{{ $producto->descripcion }}')" 
                                    class="hover:text-red-500 transition-colors font-black italic uppercase text-[10px]">Baja</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

{{-- MODAL NUEVO PRODUCTO --}}
<div id="modalProducto" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 p-8 rounded-3xl w-full max-w-2xl shadow-2xl overflow-y-auto max-h-[95vh]">
        <h2 class="text-2xl font-black italic text-zinc-800 dark:text-white uppercase tracking-tighter mb-6">Nuevo <span class="text-orange-500">Producto</span></h2>
        
        <form action="{{ route('productos.store') }}" method="POST" class="grid grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Código de Barras (Opcional)</label>
                <input type="text" name="codigo_barras" class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Departamento</label>
                <select name="departamento_id" required class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
                    @foreach($departamentos ?? [] as $depto)
                        <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Descripción</label>
                <input type="text" name="descripcion" required class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Precio Costo</label>
                <input type="number" step="0.01" name="precio_costo" required class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Precio Venta</label>
                <input type="number" step="0.01" name="precio_venta" required class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Stock Actual</label>
                <input type="number" step="0.01" name="stock_actual" required class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Stock Mínimo</label>
                <input type="number" step="0.01" name="stock_minimo" value="0" class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Unidad de Medida</label>
                <select name="unidad_medida" required class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
                    <option value="pieza">Pieza</option>
                    <option value="kg">Kilogramo (kg)</option>
                    <option value="litro">Litro (L)</option>
                </select>
            </div>
            <div class="flex items-center gap-3 ml-2 mt-6">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="es_granel" value="1" class="sr-only peer">
                    <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none dark:bg-zinc-700 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500 rounded-full"></div>
                    <span class="ml-3 text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">¿Venta a Granel?</span>
                </label>
            </div>
            <div class="col-span-2 pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modalProducto').classList.add('hidden')" class="flex-1 px-6 py-3 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-white font-black italic rounded-xl hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-all uppercase text-xs">Cerrar</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-orange-600 text-white font-black italic rounded-xl hover:bg-orange-500 transition-all uppercase text-xs shadow-[0_0_20px_rgba(234,88,12,0.3)]">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR PRODUCTO --}}
<div id="modalEditar" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 p-8 rounded-3xl w-full max-w-2xl shadow-2xl overflow-y-auto max-h-[95vh]">
        <h2 class="text-2xl font-black italic text-zinc-800 dark:text-white uppercase tracking-tighter mb-6">Editar <span class="text-orange-500">Producto</span></h2>
        <form id="formEditar" method="POST" class="grid grid-cols-2 gap-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Código de Barras</label>
                <input type="text" name="codigo_barras" id="edit_codigo" class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Departamento</label>
                <select name="departamento_id" id="edit_depto" class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none focus:border-orange-500">
                    @foreach($departamentos ?? [] as $depto)
                        <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Descripción</label>
                <input type="text" name="descripcion" id="edit_descripcion" required class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Precio Costo</label>
                <input type="number" step="0.01" name="precio_costo" id="edit_costo" class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Precio Venta</label>
                <input type="number" step="0.01" name="precio_venta" id="edit_venta" class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Stock Actual</label>
                <input type="number" step="0.01" name="stock_actual" id="edit_stock_actual" class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Stock Mínimo</label>
                <input type="number" step="0.01" name="stock_minimo" id="edit_stock_minimo" class="w-full bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest ml-2">Unidad de Medida</label>
                <select name="unidad_medida" id="edit_unidad" class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 rounded-xl px-4 py-3 text-zinc-900 dark:text-white outline-none">
                    <option value="pieza">Pieza</option>
                    <option value="kg">Kilogramo (kg)</option>
                    <option value="litro">Litro (L)</option>
                </select>
            </div>
            <div class="flex items-center gap-3 ml-2 mt-6">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="es_granel" value="1" id="edit_es_granel" class="sr-only peer">
                    <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none dark:bg-zinc-700 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500 rounded-full"></div>
                    <span class="ml-3 text-[10px] font-black italic text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">¿Venta a Granel?</span>
                </label>
            </div>
            <div class="col-span-2 pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modalEditar').classList.add('hidden')" class="flex-1 px-6 py-3 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-white font-black italic rounded-xl hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-all uppercase text-xs">Cancelar</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-orange-600 text-white font-black italic rounded-xl hover:bg-orange-500 transition-all uppercase text-xs shadow-[0_0_20px_rgba(234,88,12,0.3)]">Actualizar</button>
            </div>
        </form>
    </div>
</div>

{{-- FORMULARIO OCULTO PARA BAJAS --}}
<form id="formBaja" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- 1. DETECTAR MENSAJES DEL SISTEMA ---
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡LOGRADO!',
            text: "{{ session('success') }}",
            background: '#18181b',
            color: '#fff',
            confirmButtonColor: '#f97316',
            customClass: { popup: 'rounded-3xl border border-white/10 italic font-black uppercase' }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡ERROR!',
            text: "{{ session('error') }}",
            background: '#18181b',
            color: '#fff',
            confirmButtonColor: '#dc2626',
            customClass: { popup: 'rounded-3xl border border-white/10 italic font-black uppercase' }
        });
    @endif

    // --- 2. FUNCIONES DE MODALES ---
function abrirModalEditar(producto) {
    const form = document.getElementById('formEditar');
    form.action = `/admin/productos/${producto.id}`;
        
        document.getElementById('edit_descripcion').value = producto.descripcion;
        document.getElementById('edit_codigo').value = producto.codigo_barras;
        document.getElementById('edit_depto').value = producto.departamento_id;
        document.getElementById('edit_costo').value = producto.precio_costo;
        document.getElementById('edit_venta').value = producto.precio_venta;
        document.getElementById('edit_stock_actual').value = producto.stock_actual;
        document.getElementById('edit_stock_minimo').value = producto.stock_minimo;
        document.getElementById('edit_unidad').value = producto.unidad_medida;
        document.getElementById('edit_es_granel').checked = (producto.es_granel == 1);
        
        document.getElementById('modalEditar').classList.remove('hidden');
    }

    function confirmarBaja(id, nombre) {
        Swal.fire({
            title: '¿DAR DE BAJA?',
            text: `Confirmar baja definitiva de: ${nombre}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'SÍ, ELIMINAR',
            cancelButtonText: 'CANCELAR',
            background: '#18181b',
            color: '#ffffff',
            customClass: { popup: 'rounded-3xl border border-white/10 italic font-black uppercase' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formBaja');
                // Ruta que ya tienes en web.php para delete
                form.action = `/admin/productos/${id}`;
                form.submit();
            }
        });
    }
</script>
@endsection
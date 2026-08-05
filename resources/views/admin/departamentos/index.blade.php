@extends('layouts.cajero')

@section('content')
<div x-data="{ modalCrear: false, modalEditar: null }" class="w-full h-full flex flex-col space-y-6">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200 dark:border-white/5 pb-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black italic uppercase tracking-wider text-zinc-900 dark:text-white">
                Gestión de <span class="text-red-600">Categorías</span>
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-1">
                Categoriza tus productos para un mejor control de inventario.
            </p>
        </div>
        <button @click="modalCrear = true" 
            class="px-5 py-3 bg-red-600 hover:bg-red-700 text-white font-bold italic uppercase text-xs tracking-wider rounded-xl shadow-lg shadow-red-600/30 transition-all flex items-center justify-center space-x-2 cursor-pointer shrink-0">
            <i class="fas fa-plus text-xs"></i>
            <span>Nueva Categoría</span>
        </button>
    </div>

    {{-- TABLA DE CATEGORÍAS (EXPANDIDA A 100%) --}}
    <div class="flex-1 w-full bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm flex flex-col">
        <div class="overflow-x-auto w-full flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-100 dark:border-white/5 bg-zinc-50 dark:bg-white/[0.02] text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="p-4 pl-6 w-20">ID</th>
                        <th class="p-4 w-1/4">Nombre</th>
                        <th class="p-4">Descripción</th>
                        <th class="p-4 w-32">Estado</th>
                        <th class="p-4 pr-6 text-right w-48">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5 text-xs font-bold text-zinc-800 dark:text-zinc-200">
                    @forelse($departamentos as $dep)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="p-4 pl-6 font-mono text-zinc-400">#{{ $dep->id }}</td>
                            <td class="p-4 uppercase italic font-black text-sm text-zinc-900 dark:text-white">{{ $dep->nombre }}</td>
                            <td class="p-4 text-zinc-500 dark:text-zinc-400 font-normal">{{ $dep->descripcion ?? 'Sin descripción' }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider {{ $dep->activo ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20' }}">
                                    {{ $dep->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="p-4 pr-6 text-right space-x-2 whitespace-nowrap">
                                {{-- BOTÓN EDITAR --}}
                                <button @click="modalEditar = {{ $dep->id }}" class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-500 text-xs font-bold transition-colors cursor-pointer">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </button>

                                {{-- BOTÓN ELIMINAR --}}
                                <form id="form-eliminar-{{ $dep->id }}" action="{{ route('departamentos.destroy', $dep->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmarAccion('form-eliminar-{{ $dep->id }}', '¿Eliminar categoría?', 'Esta acción no se podrá deshacer.')"
                                        class="px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-500 text-xs font-bold transition-colors cursor-pointer">
                                        <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-zinc-500 dark:text-zinc-400 italic">
                                <i class="fas fa-tag text-4xl mb-3 block opacity-20"></i>
                                No hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREAR --}}
    <div x-show="modalCrear" 
         x-transition
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        
        <div @click.away="modalCrear = false" class="bg-white dark:bg-[#121212] border border-zinc-200 dark:border-white/10 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            <form action="{{ route('departamentos.store') }}" method="POST">
                @csrf
                
                <div class="p-4 border-b border-zinc-100 dark:border-white/5 flex justify-between items-center">
                    <h3 class="text-sm font-black italic uppercase tracking-wider text-zinc-900 dark:text-white">
                        Nueva <span class="text-red-600">Categoría</span>
                    </h3>
                    <button type="button" @click="modalCrear = false" class="text-zinc-400 hover:text-white text-base cursor-pointer">&times;</button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Nombre</label>
                        <input type="text" name="nombre" placeholder="Ej: Frutas y Verduras, Abarrotes" required
                            class="w-full px-4 py-2.5 rounded-xl bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-900 dark:text-white text-xs font-medium focus:outline-none focus:border-red-600 transition-colors">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3" placeholder="Opcional..."
                            class="w-full px-4 py-2.5 rounded-xl bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-900 dark:text-white text-xs font-medium focus:outline-none focus:border-red-600 transition-colors resize-none"></textarea>
                    </div>
                </div>

                <div class="p-4 border-t border-zinc-100 dark:border-white/5 flex justify-end space-x-3 bg-zinc-50 dark:bg-white/[0.01]">
                    <button type="button" @click="modalCrear = false" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-zinc-500 hover:text-zinc-700 dark:hover:text-white transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold italic uppercase text-xs tracking-wider rounded-xl transition-all shadow-md shadow-red-600/20 cursor-pointer">
                        Crear
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODALES EDITAR --}}
    @foreach($departamentos as $dep)
        <div x-show="modalEditar === {{ $dep->id }}" 
             x-transition
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            
            <div @click.away="modalEditar = null" class="bg-white dark:bg-[#121212] border border-zinc-200 dark:border-white/10 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
                <form action="{{ route('departamentos.update', $dep->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-4 border-b border-zinc-100 dark:border-white/5 flex justify-between items-center">
                        <h3 class="text-sm font-black italic uppercase tracking-wider text-zinc-900 dark:text-white">
                            Editar <span class="text-red-600">Categoría</span>
                        </h3>
                        <button type="button" @click="modalEditar = null" class="text-zinc-400 hover:text-white text-base cursor-pointer">&times;</button>
                    </div>

                    <div class="p-5 space-y-4 text-left">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Nombre</label>
                            <input type="text" name="nombre" value="{{ $dep->nombre }}" required
                                class="w-full px-4 py-2.5 rounded-xl bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-900 dark:text-white text-xs font-medium focus:outline-none focus:border-red-600 transition-colors">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Descripción</label>
                            <textarea name="descripcion" rows="3"
                                class="w-full px-4 py-2.5 rounded-xl bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-900 dark:text-white text-xs font-medium focus:outline-none focus:border-red-600 transition-colors resize-none">{{ $dep->descripcion }}</textarea>
                        </div>

                        <div class="flex items-center space-x-3 pt-2">
                            <input type="checkbox" name="activo" value="1" id="activo{{ $dep->id }}" {{ $dep->activo ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-zinc-300 dark:border-white/10 text-red-600 focus:ring-red-600 bg-zinc-100 dark:bg-white/5 cursor-pointer">
                            <label for="activo{{ $dep->id }}" class="text-xs font-bold text-zinc-700 dark:text-zinc-300 select-none cursor-pointer">
                                Categoría Activa
                            </label>
                        </div>
                    </div>

                    <div class="p-4 border-t border-zinc-100 dark:border-white/5 flex justify-end space-x-3 bg-zinc-50 dark:bg-white/[0.01]">
                        <button type="button" @click="modalEditar = null" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-zinc-500 hover:text-zinc-700 dark:hover:text-white transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold italic uppercase text-xs tracking-wider rounded-xl transition-all shadow-md shadow-red-600/20 cursor-pointer">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

</div>
@endsection
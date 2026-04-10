@extends('layouts.admin')

@section('title', 'Reporte de Ventas | Admin')

@section('content')
<div class="p-8 bg-zinc-50 dark:bg-[#0a0a0a] min-h-screen transition-colors duration-300">
    
    <header class="mb-10">
        <div class="flex items-center space-x-4">
            <h1 class="text-4xl font-black italic text-zinc-900 dark:text-white uppercase tracking-tighter">REPORTES DE</h1>
            <h1 class="text-4xl font-black italic text-red-600 uppercase tracking-tighter">VENTAS</h1>
        </div>
        <p class="text-zinc-500 dark:text-gray-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-1 ml-1">Historial detallado y auditoría de transacciones</p>
    </header>

    {{-- Filtros --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-[#111] p-4 rounded-xl border border-zinc-200 dark:border-white/5 shadow-xl transition-colors">
            <span class="block text-[9px] font-black text-zinc-400 dark:text-gray-500 uppercase mb-1">Filtrar por Fecha</span>
            <input type="date" class="bg-transparent text-zinc-800 dark:text-white font-bold text-xs outline-none w-full cursor-pointer">
        </div>
        <div class="bg-white dark:bg-[#111] p-4 rounded-xl border border-zinc-200 dark:border-white/5 shadow-xl transition-colors">
            <span class="block text-[9px] font-black text-zinc-400 dark:text-gray-500 uppercase mb-1">Cajero</span>
            <select class="bg-transparent text-zinc-800 dark:text-white font-bold text-xs outline-none w-full cursor-pointer">
                <option value="" class="bg-white dark:bg-[#111]">Todos los cajeros</option>
            </select>
        </div>
    </div>

    <div class="bg-white dark:bg-[#0d0d0d] rounded-2xl border border-zinc-200 dark:border-white/5 shadow-2xl relative overflow-hidden transition-colors">
        <div class="h-1 w-full bg-gradient-to-r from-red-600 to-blue-600"></div>
        
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="text-[10px] text-zinc-400 dark:text-gray-600 uppercase font-black tracking-widest">
                        <th class="px-4 py-3">Folio / ID</th>
                        <th class="px-4 py-3">Fecha y Hora</th>
                        <th class="px-4 py-3 text-center">Cajero Responsable</th>
                        <th class="px-4 py-3 text-center">Método</th>
                        <th class="px-4 py-3 text-right">Total Venta</th>
                        <th class="p-4 text-right italic text-red-600">Zona de Peligro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse($reportes as $venta)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-white/[0.03] transition-all group">
                        <td class="px-4 py-5 font-mono text-blue-600 dark:text-blue-500 text-xs font-bold italic">
                            #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        
                        <td class="px-4 py-5">
                            <div class="text-zinc-900 dark:text-white font-black text-sm uppercase tracking-tighter">
                                {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-zinc-400 dark:text-gray-500 font-bold italic">
                                {{ \Carbon\Carbon::parse($venta->fecha)->format('H:i A') }}
                            </div>
                        </td>

                        <td class="px-4 py-5 text-center">
                            <span class="inline-flex items-center space-x-2 px-3 py-1 bg-zinc-100 dark:bg-white/5 rounded-lg border border-zinc-200 dark:border-white/10">
                                <i class="fas fa-user text-[10px] text-red-500"></i>
                                <span class="text-zinc-800 dark:text-white font-black text-[10px] uppercase">
                                    {{ $venta->usuario->nombre ?? 'N/A' }}
                                </span>
                            </span>
                        </td>

                        <td class="px-4 py-5 text-center">
                            <span class="text-[10px] text-zinc-500 dark:text-gray-400 font-black uppercase border-b border-zinc-200 dark:border-gray-800 pb-1">
                                Efectivo
                            </span>
                        </td>

                        <td class="px-4 py-5 text-right">
                            <span class="text-xl font-black text-zinc-900 dark:text-white italic tracking-tighter group-hover:text-green-600 transition-colors">
                                ${{ number_format($venta->total, 2) }}
                            </span>
                        </td>
                        
                        <td class="p-4 text-right">
                            @if(auth()->user()->username == 'admin')
                                <form id="form-eliminar-{{ $venta->id }}" action="{{ route('ventas.cancelar', $venta->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmarCancelacion({{ $venta->id }})" 
                                            class="bg-zinc-100 dark:bg-white/5 hover:bg-red-600 text-zinc-400 dark:text-gray-600 hover:text-white p-3 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest">
                                        Anular
                                    </button>
                                </form>
                            @else
                                <span class="text-[9px] font-black text-zinc-300 dark:text-zinc-700 italic uppercase">Bloqueado</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-20 text-center text-zinc-400 italic font-bold">No hay ventas registradas en este periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <div class="bg-red-600 p-8 rounded-3xl shadow-2xl shadow-red-900/40 text-right min-w-[300px] transform hover:scale-105 transition-transform">
            <span class="block text-[10px] font-black text-white/60 uppercase mb-1 tracking-[0.2em]">Venta Total Acumulada</span>
            <span class="text-4xl font-black text-white italic uppercase tracking-tighter">
                ${{ number_format($reportes->sum('total'), 2) }}
            </span>
        </div>
    </div>
</div>

<script>
    function confirmarCancelacion(id) {
        const isDark = document.documentElement.classList.contains('dark');
        
        Swal.fire({
            title: '¿ANULAR VENTA?',
            text: "Esta acción borrará el registro para siempre y no hay marcha atrás, pendejo.",
            icon: 'warning',
            showCancelButton: true,
            background: isDark ? '#0d0d0d' : '#ffffff',
            color: isDark ? '#ffffff' : '#18181b',
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: isDark ? '#27272a' : '#e4e4e7',
            confirmButtonText: 'SÍ, BORRAR',
            cancelButtonText: 'MEJOR NO',
            heightAuto: false,
            customClass: {
                popup: isDark ? 'border border-white/10 rounded-3xl' : 'border border-zinc-200 rounded-3xl',
                title: 'font-black italic uppercase tracking-tighter text-2xl',
                confirmButton: 'font-black uppercase tracking-widest text-xs py-3 px-6 rounded-xl',
                cancelButton: 'font-black uppercase tracking-widest text-xs py-3 px-6 rounded-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-eliminar-' + id).submit();
            }
        });
    }
</script>
@endsection
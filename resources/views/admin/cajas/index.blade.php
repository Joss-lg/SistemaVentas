@extends('layouts.cajero')

@section('title', 'Historial de Cajas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 p-4 md:p-0 transition-colors duration-300">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200 dark:border-white/5 pb-6">
        <div>
            <h2 class="text-3xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
                HISTORIAL DE <span class="text-red-600">CAJAS</span>
            </h2>
            <p class="text-[10px] md:text-xs font-black text-zinc-500 uppercase tracking-[0.25em] mt-1">
                Aprobación de cortes y registro general de turnos
            </p>
        </div>

        {{-- BOTÓN PARA ABRIR MODAL DE SOLICITUDES PENDIENTES --}}
        @if(isset($solicitudesPendientes) && $solicitudesPendientes->count() > 0)
            <button onclick="abrirModalSolicitudes()" 
                class="relative inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-500 text-white font-black italic px-5 py-3 rounded-2xl uppercase text-xs tracking-wider transition-all shadow-lg shadow-red-600/30 cursor-pointer animate-pulse">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Solicitudes Pendientes</span>
                <span class="bg-white text-red-600 font-extrabold text-[10px] px-2 py-0.5 rounded-full ml-1">
                    {{ $solicitudesPendientes->count() }}
                </span>
            </button>
        @endif
    </div>

    {{-- TABLA GENERAL DE HISTORIAL DE CORTES --}}
    <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-zinc-100 dark:border-white/5 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">
                Cortes de Caja Anteriores
            </h3>
            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                Total Registros: {{ method_exists($cortesHistorial, 'total') ? $cortesHistorial->total() : $cortesHistorial->count() }}
            </span>
        </div>

        {{-- VISTA ESCRITORIO / TABLET (TABLA) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 dark:bg-black text-zinc-400 dark:text-zinc-500 uppercase text-[9px] tracking-[0.2em] border-b border-zinc-100 dark:border-white/5">
                    <tr>
                        <th class="p-4 pl-6 font-black">Fecha Apertura</th>
                        <th class="p-4 font-black">Cajero</th>
                        <th class="p-4 font-black text-center">Fondo Inicial</th>
                        <th class="p-4 font-black text-center">Efectivo Real</th>
                        <th class="p-4 font-black text-center">Estado Turno</th>
                        <th class="p-4 pr-6 font-black text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5 text-xs font-bold">
                    @forelse($cortesHistorial as $corte)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="p-4 pl-6 font-mono text-zinc-800 dark:text-zinc-200 font-bold">
                                {{ \Carbon\Carbon::parse($corte->fecha_apertura)->format('d/m/Y') }}
                            </td>
                            <td class="p-4 text-zinc-800 dark:text-zinc-200 uppercase font-black">
                                {{ $corte->usuario->nombre ?? $corte->usuario->username ?? $corte->usuario->name ?? 'Sistema' }}
                            </td>
                            <td class="p-4 text-center text-zinc-500 font-mono italic">
                                ${{ number_format($corte->monto_inicial ?? 0, 2) }}
                            </td>
                            <td class="p-4 text-center font-black italic text-emerald-600 dark:text-emerald-500 tabular-nums">
                                ${{ number_format($corte->monto_final ?? $corte->efectivo_real ?? 0, 2) }}
                            </td>
                            <td class="p-4 text-center">
                                @if(is_null($corte->fecha_cierre))
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                        Abierto
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-zinc-500/10 text-zinc-400">
                                        Cerrado
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 pr-6 text-right">
                                <a href="{{ route('admin.cajas.show', $corte->id) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-white/5 dark:hover:bg-white/10 text-zinc-800 dark:text-white rounded-xl text-[10px] font-black uppercase italic tracking-wider transition-all">
                                    <i class="fas fa-eye text-xs"></i> Detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-zinc-400 dark:text-zinc-600 font-bold italic uppercase text-xs">
                                <i class="fas fa-vault text-3xl mb-3 block opacity-30"></i>
                                No hay registros de cortes pasados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- VISTA MÓVIL (TARJETAS EN CELULARES) --}}
        <div class="block md:hidden divide-y divide-zinc-100 dark:divide-white/5">
            @forelse($cortesHistorial as $corte)
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest block">Apertura</span>
                            <span class="text-xs font-black text-zinc-800 dark:text-zinc-200 font-mono">
                                {{ \Carbon\Carbon::parse($corte->fecha_apertura)->format('d/m/Y') }}
                            </span>
                        </div>
                        @if(is_null($corte->fecha_cierre))
                            <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                Abierto
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase bg-zinc-500/10 text-zinc-400">
                                Cerrado
                            </span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-zinc-500 uppercase font-black text-[10px]">Cajero:</span>
                        <span class="font-black text-zinc-900 dark:text-white uppercase">
                            {{ $corte->usuario->nombre ?? $corte->usuario->username ?? $corte->usuario->name ?? 'Sistema' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                        <div class="bg-zinc-50 dark:bg-white/[0.02] p-2 rounded-xl border border-zinc-100 dark:border-white/5">
                            <span class="text-[8px] font-black text-zinc-400 uppercase block">Inicial</span>
                            <span class="font-black italic text-zinc-700 dark:text-zinc-300">
                                ${{ number_format($corte->monto_inicial ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="bg-zinc-50 dark:bg-white/[0.02] p-2 rounded-xl border border-zinc-100 dark:border-white/5">
                            <span class="text-[8px] font-black text-zinc-400 uppercase block">Real Cierre</span>
                            <span class="font-black italic text-emerald-500">
                                ${{ number_format($corte->monto_final ?? $corte->efectivo_real ?? 0, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-1">
                        <a href="{{ route('admin.cajas.show', $corte->id) }}" 
                           class="w-full flex items-center justify-center gap-2 py-2 bg-zinc-900 dark:bg-white/10 text-white font-black italic uppercase text-[10px] tracking-wider rounded-xl transition-all">
                            <i class="fas fa-eye"></i> Ver Detalle Completo
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-zinc-400 dark:text-zinc-600 font-bold italic uppercase text-xs">
                    Sin historial de cortes.
                </div>
            @endforelse
        </div>

        {{-- PAGINACIÓN --}}
        @if(method_exists($cortesHistorial, 'hasPages') && $cortesHistorial->hasPages())
            <div class="p-4 border-t border-zinc-100 dark:border-white/5">
                {{ $cortesHistorial->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL DE AUTORIZACIONES PENDIENTES --}}
@if(isset($solicitudesPendientes) && $solicitudesPendientes->count() > 0)
    <div id="modalSolicitudes" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 transition-all duration-300">
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/10 rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
            
            {{-- HEADER CABECERA DEL MODAL --}}
            <div class="p-6 border-b border-zinc-100 dark:border-white/5 flex items-center justify-between bg-zinc-50 dark:bg-black/40">
                <div class="flex items-center gap-3 text-red-600 dark:text-red-500">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                    <div>
                        <h3 class="text-sm md:text-base font-black italic uppercase tracking-wider">
                            Solicitudes Pendientes ({{ $solicitudesPendientes->count() }})
                        </h3>
                        <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase">
                            Autorizaciones requeridas por faltante de efectivo
                        </p>
                    </div>
                </div>
                <button onclick="cerrarModalSolicitudes()" class="text-zinc-400 hover:text-white text-xl font-bold p-2 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- CUERPO DEL MODAL CON SCROLL --}}
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($solicitudesPendientes as $solicitud)
                        @php 
                            $efectivoReal = (float) ($solicitud->efectivo_real ?? 0);
                            $faltante = abs((float) ($solicitud->faltante ?? 0));
                            
                            if ($solicitud->corte) {
                                $esperado = (float) ($solicitud->corte->monto_inicial ?? 0) + (float) ($solicitud->corte->ventas_efectivo ?? 0) - (float) ($solicitud->corte->gastos ?? 0);
                            } else {
                                $esperado = $efectivoReal + $faltante;
                            }
                        @endphp
                        <div class="bg-zinc-50 dark:bg-black/60 border border-zinc-200 dark:border-white/10 p-5 rounded-2xl flex flex-col justify-between space-y-4 shadow-md">
                            <div class="space-y-3">
                                <div class="border-b border-zinc-200 dark:border-white/5 pb-2">
                                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest block">Solicitante</span>
                                    <span class="text-xs font-black text-zinc-900 dark:text-white uppercase">
                                        {{ $solicitud->solicitante->nombre ?? $solicitud->solicitante->username ?? $solicitud->solicitante->name ?? 'Cajero' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="bg-white dark:bg-black/40 p-2.5 rounded-xl border border-zinc-200 dark:border-white/5">
                                        <p class="text-[8px] font-black text-zinc-400 uppercase">Esperado</p>
                                        <p class="font-black italic text-zinc-800 dark:text-zinc-200">${{ number_format($esperado, 2) }}</p>
                                    </div>
                                    <div class="bg-white dark:bg-black/40 p-2.5 rounded-xl border border-zinc-200 dark:border-white/5">
                                        <p class="text-[8px] font-black text-zinc-400 uppercase">Declarado</p>
                                        <p class="font-black italic text-emerald-600 dark:text-emerald-500">${{ number_format($efectivoReal, 2) }}</p>
                                    </div>
                                </div>

                                <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center justify-between text-red-600 dark:text-red-500">
                                    <span class="text-[10px] font-black uppercase">Faltante:</span>
                                    <span class="text-sm font-black italic">-${{ number_format($faltante, 2) }}</span>
                                </div>
                            </div>

                            {{-- BOTONES DE ACCIÓN --}}
                            <div class="flex items-center gap-2 pt-2">
                                <form action="{{ route('admin.autorizaciones.aprobar', $solicitud->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black italic py-2.5 rounded-xl uppercase text-[10px] tracking-wider transition-all shadow-md shadow-emerald-600/20 cursor-pointer flex items-center justify-center gap-1">
                                        <i class="fas fa-check"></i> Autorizar
                                    </button>
                                </form>

                                <form action="{{ route('admin.autorizaciones.rechazar', $solicitud->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full bg-zinc-800 hover:bg-zinc-700 text-red-400 font-black italic py-2.5 rounded-xl uppercase text-[10px] tracking-wider transition-all cursor-pointer flex items-center justify-center gap-1">
                                        <i class="fas fa-times"></i> Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FOOTER DEL MODAL --}}
            <div class="p-4 border-t border-zinc-100 dark:border-white/5 bg-zinc-50 dark:bg-black/40 text-right">
                <button onclick="cerrarModalSolicitudes()" class="px-5 py-2 bg-zinc-800 hover:bg-zinc-700 text-white font-black uppercase text-xs rounded-xl italic">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
@endif

{{-- SCRIPT CONTROLADOR DEL MODAL --}}
<script>
    function abrirModalSolicitudes() {
        const modal = document.getElementById('modalSolicitudes');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function cerrarModalSolicitudes() {
        const modal = document.getElementById('modalSolicitudes');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
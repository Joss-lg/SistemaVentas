@extends('layouts.cajero')

@section('title', 'Flujo de Caja')

@section('content')
<div id="gastos-app" class="max-w-6xl mx-auto space-y-8 p-4 md:p-0 transition-colors duration-300"
    @if(session('gasto_registrado'))
        data-gasto-registrado="true"
        data-gasto-descripcion="{{ session('gasto_registrado')['descripcion'] ?? '' }}"
        data-gasto-monto="{{ session('gasto_registrado')['monto'] ?? '' }}"
    @endif
>

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-zinc-200 dark:border-white/5 pb-6">
        <div>
            <h2 class="text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
                FLUJO DE <span class="text-red-600">CAJA</span>
            </h2>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-1 ml-1">
                Control de gastos y egresos del negocio
            </p>
        </div>

        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-5 rounded-2xl shadow-2xl w-full md:w-auto">
            <span class="text-[9px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest block">Salida Hoy</span>
            <span class="text-4xl font-black italic text-red-600 dark:text-red-500 tabular-nums">${{ number_format($totalDia, 2) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- FORMULARIO NUEVO GASTO --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl p-8 shadow-2xl">
                <h3 class="text-lg font-black italic uppercase tracking-tighter text-zinc-900 dark:text-white mb-6 border-b border-zinc-100 dark:border-white/5 pb-4">
                    Nuevo <span class="text-red-600">Gasto Manual</span>
                </h3>

                <form action="{{ route('gastos.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">
                            Descripción del Gasto
                        </label>
                        <input type="text" name="descripcion" required placeholder="EJ. PAGO DE LUZ"
                            class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-zinc-900 dark:text-white font-bold uppercase italic focus:outline-none focus:border-red-500 transition-all placeholder-zinc-300 dark:placeholder-zinc-800">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">
                            Monto ($)
                        </label>
                        <input type="number" step="0.01" name="monto" required placeholder="0.00"
                            class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-2xl font-black italic text-red-600 dark:text-red-500 focus:outline-none focus:border-red-500 transition-all placeholder-zinc-300 dark:placeholder-zinc-800">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">
                            Categoría
                        </label>
                        <select name="categoria"
                            class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-zinc-900 dark:text-white font-bold uppercase italic focus:outline-none focus:border-red-500 transition-all">
                            <option value="GENERAL">General</option>
                            <option value="INVENTARIO">Inventario / Mercancía</option>
                            <option value="SERVICIOS">Servicios (Luz/Agua/Net)</option>
                            <option value="PERSONAL">Pago Personal</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-500 text-white font-black italic py-5 rounded-2xl transition-all uppercase text-xs tracking-[0.2em] shadow-xl shadow-red-900/30 transform hover:scale-[1.02] active:scale-95">
                        REGISTRAR SALIDA
                    </button>
                </form>
            </div>
        </div>

        {{-- MOVIMIENTOS + REPORTE --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- TABLA MOVIMIENTOS --}}
            <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-white/5 bg-zinc-50/50 dark:bg-white/[0.02]">
                    <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">
                        Movimientos <span class="text-red-600">Recientes</span>
                    </h3>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-50 dark:bg-white/[0.02] text-zinc-400 dark:text-zinc-500 uppercase text-[9px] tracking-[0.2em] border-b border-zinc-100 dark:border-white/5">
                            <tr>
                                <th class="p-4 pl-6 font-black">Descripción</th>
                                <th class="p-4 font-black">Categoría</th>
                                <th class="p-4 font-black text-center">Fecha</th>
                                <th class="p-4 pr-6 font-black text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                            @forelse($gastos as $g)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                                <td class="p-4 pl-6 font-bold italic text-zinc-800 dark:text-zinc-200">{{ $g->descripcion }}</td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $g->categoria == 'INVENTARIO' ? 'bg-blue-600/10 text-blue-500 border border-blue-600/20' : 'bg-zinc-100 dark:bg-white/5 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-white/5' }}">
                                        {{ $g->categoria }}
                                    </span>
                                </td>
                                <td class="p-4 text-center text-zinc-400 dark:text-zinc-500 text-[10px] font-black uppercase">{{ $g->created_at->format('d/m/Y') }}</td>
                                <td class="p-4 pr-6 text-right font-black italic text-red-600 dark:text-red-500 tabular-nums">-${{ number_format($g->monto, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-zinc-400 dark:text-zinc-600 font-bold italic uppercase text-xs">No hay gastos registrados el día de hoy.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- REPORTE GENERAL --}}
            <div class="bg-white dark:bg-[#0d0d0d] border border-emerald-500/20 hover:border-emerald-500/50 p-6 rounded-3xl shadow-2xl transition-all group">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-emerald-500/10 p-4 rounded-2xl group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-excel text-3xl text-emerald-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black italic uppercase tracking-tighter text-zinc-900 dark:text-white">Reporte General</h3>
                            <p class="text-[9px] text-emerald-500 font-black tracking-widest uppercase leading-none">Excel Completo</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reporte.excel') }}"
                       class="flex items-center justify-center gap-2 w-full md:w-auto bg-emerald-600 hover:bg-emerald-500 text-white font-black italic uppercase py-4 px-10 rounded-2xl transition-all shadow-xl shadow-emerald-900/30 transform hover:scale-[1.02] active:scale-95 text-xs tracking-widest">
                        <i class="fas fa-download"></i> Descargar Reporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
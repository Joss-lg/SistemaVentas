@extends('layouts.cajero')

@section('title', 'Finalizar Turno - Corte')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 p-4 md:p-0 transition-colors duration-300">
    {{-- Encabezado con Estilo --}}
    <div class="border-b border-zinc-200 dark:border-white/5 pb-6">
        <h2 class="text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
            CERRAR <span class="text-orange-500">CAJA</span>
        </h2>
        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-1 ml-1">
            Resumen de ventas del turno: {{ now()->format('d/m/Y H:i A') }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Card de Ventas Esperadas --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-8 rounded-3xl shadow-2xl relative overflow-hidden transition-all group">
            <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase mb-4 tracking-widest">Ventas Esperadas (Sistema)</p>
            <div class="text-6xl md:text-7xl font-black italic text-zinc-900 dark:text-white tracking-tighter transition-colors group-hover:text-orange-500">
                ${{ number_format($ventasDelTurno, 2) }}
            </div>
            <i class="fas fa-calculator absolute -right-6 -bottom-6 text-9xl text-zinc-100 dark:text-white/5 -rotate-12 transition-transform group-hover:rotate-0"></i>
        </div>

        {{-- Formulario de Captura --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-8 rounded-3xl shadow-2xl transition-colors">
            <form action="{{ route('admin.corte.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="ventas_esperadas" value="{{ $ventasDelTurno }}">

                <div>
                    <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">
                        Efectivo Real en Caja ($)
                    </label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl font-black text-zinc-300 dark:text-zinc-700">$</span>
                        <input type="number" step="0.01" name="efectivo_real" required autofocus
                               class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl py-6 pl-12 pr-6 text-4xl font-black text-green-600 dark:text-green-500 italic focus:outline-none focus:border-orange-500 dark:focus:border-orange-500/50 transition-all placeholder-zinc-300 dark:placeholder-zinc-800"
                               placeholder="0.00">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-orange-600 hover:bg-orange-500 text-white font-black italic py-5 rounded-2xl transition-all uppercase text-xs tracking-[0.2em] shadow-xl shadow-orange-900/30 transform hover:scale-[1.02] active:scale-95">
                    GUARDAR Y FINALIZAR TURNO
                </button>
                
                <input type="hidden" name="monto_inicial" value="0"> 
                <input type="hidden" name="total_ventas_tarjeta" value="0">
            </form>
        </div>
    </div>

    {{-- Alerta de Precaución --}}
    <div class="bg-orange-500/10 border border-orange-500/20 p-6 rounded-2xl flex flex-col md:flex-row items-center gap-4 transition-all hover:bg-orange-500/20">
        <div class="bg-orange-600 p-3 rounded-xl shadow-lg shadow-orange-900/20">
            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
        </div>
        <div>
            <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-black uppercase leading-relaxed tracking-wider">
                ¡Trucha con el conteo! <span class="text-orange-600 italic">No la vayas a cagar, wey.</span>
            </p>
            <p class="text-[10px] text-zinc-500 dark:text-zinc-500 font-bold uppercase mt-1">
                Asegúrate de contar bien el efectivo. Una vez guardado el corte, los datos se enviarán al administrador para su revisión inmediata.
            </p>
        </div>
    </div>
</div>
@endsection
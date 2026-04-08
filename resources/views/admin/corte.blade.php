@extends('layouts.cajero')

@section('title', 'Finalizar Turno - Corte')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="border-b border-white/5 pb-6">
        <h2 class="text-4xl font-black italic tracking-tighter uppercase text-white">
            Cerrar <span class="text-orange-500">Caja</span>
        </h2>
        <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.3em] mt-1">
            Resumen de ventas del turno: {{ now()->format('d/m/Y H:i A') }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-[#0d0d0d] border border-white/5 p-8 rounded-2xl shadow-2xl relative overflow-hidden">
            <p class="text-[10px] font-black text-zinc-500 uppercase mb-4">Ventas Esperadas (Sistema)</p>
            <div class="text-6xl font-black italic text-white tracking-tighter font-digital">
                ${{ number_format($ventasDelTurno, 2) }}
            </div>
            <i class="fas fa-calculator absolute -right-4 -bottom-4 text-8xl text-white/5 -rotate-12"></i>
        </div>

        <div class="bg-[#0d0d0d] border border-white/5 p-8 rounded-2xl shadow-2xl">
            <form action="{{ route('admin.corte.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="ventas_esperadas" value="{{ $ventasDelTurno }}">

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3">
                        Efectivo Real en Caja ($)
                    </label>
                    <input type="number" step="0.01" name="efectivo_real" required autofocus
                           class="w-full bg-black border border-white/10 rounded-xl py-5 px-6 text-3xl font-black text-green-500 italic focus:outline-none focus:border-green-500/50 transition-all font-digital"
                           placeholder="0.00">
                </div>

                <button type="submit" 
                        class="w-full bg-orange-600 hover:bg-orange-500 text-white font-black italic py-4 rounded-xl transition-all uppercase text-xs tracking-[0.2em] shadow-lg shadow-orange-900/20">
                    Guardar y Finalizar Turno
                </button>
                <input type="hidden" name="monto_inicial" value="0"> 
                <input type="hidden" name="total_ventas_tarjeta" value="0">
            </form>
        </div>
    </div>

    <div class="bg-red-600/10 border border-red-600/20 p-4 rounded-xl flex items-center gap-4">
        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
        <p class="text-[10px] text-zinc-400 font-bold uppercase leading-relaxed">
            Asegúrate de contar bien el efectivo. Una vez guardado el corte, los datos se enviarán al administrador para su revisión.
        </p>
    </div>
</div>
@endsection
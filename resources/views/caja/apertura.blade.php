@extends('layouts.cajero')
@section('title', 'Apertura de Caja')

@section('content')
<div class="flex-1 flex items-center justify-center py-12 px-4">
    <div class="max-w-lg w-full">

        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl relative overflow-hidden p-8">

            {{-- Ícono decorativo --}}
            <i class="fas fa-cash-register absolute -right-6 -bottom-6 text-9xl text-zinc-100 dark:text-white/[0.03] -rotate-12 pointer-events-none select-none"></i>

            <div class="relative z-10 space-y-6">

                {{-- ENCABEZADO --}}
                <div class="text-center">
                    <span class="inline-block px-3 py-1 bg-red-600/10 text-red-600 font-black italic uppercase text-[10px] tracking-widest rounded-full border border-red-600/20">
                        Control de Turno
                    </span>
                    <h1 class="text-3xl md:text-4xl font-black italic uppercase tracking-tighter text-zinc-900 dark:text-white mt-3">
                        Apertura de <span class="text-red-600">Caja</span>
                    </h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-1">
                        Hola, {{ Auth::user()->nombre }}. Ingresa el fondo inicial para comenzar tu turno.
                    </p>
                </div>

                {{-- MENSAJES DE SESIÓN --}}
                @if(session('error'))
                    <div class="bg-red-500/10 border border-red-500/20 text-red-600 text-xs font-bold rounded-xl p-3 text-center">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-500/10 border border-blue-500/20 text-blue-600 text-xs font-bold rounded-xl p-3 text-center">
                        {{ session('info') }}
                    </div>
                @endif

                {{-- FORMULARIO --}}
                <form action="{{ route('caja.apertura.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">
                            Monto Inicial (Fondo de Caja)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-black text-zinc-300 dark:text-zinc-700">$</span>
                            <input type="number" step="0.01" min="0" name="monto_inicial" id="monto_inicial"
                                required autofocus
                                value="{{ old('monto_inicial') }}"
                                class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl py-4 pl-9 pr-4 text-2xl font-black text-emerald-600 dark:text-emerald-500 italic focus:outline-none focus:border-red-500 dark:focus:border-red-500/50 transition-all placeholder-zinc-300 dark:placeholder-zinc-800"
                                placeholder="0.00">
                        </div>
                        @error('monto_inicial')
                            <p class="text-red-600 text-[10px] font-bold uppercase mt-2 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-black italic py-4 rounded-2xl transition-all uppercase text-xs tracking-[0.2em] shadow-xl shadow-red-900/30 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-door-open mr-2"></i> ABRIR CAJA E INICIAR TURNO
                    </button>
                </form>

                <p class="text-center text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                    Turno de {{ now()->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
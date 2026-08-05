@extends('layouts.cajero')

@section('title', 'Configuración de Hardware')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 p-4 md:p-0 transition-colors duration-300">

    <div class="border-b border-zinc-200 dark:border-white/5 pb-6">
        <h2 class="text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
            CONFIG. <span class="text-red-600">HARDWARE</span>
        </h2>
        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-1 ml-1">
            Impresora, cajón de dinero y báscula de esta tienda
        </p>
    </div>

    <form action="{{ route('admin.hardware.update') }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Modo simulado --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl p-8 shadow-2xl">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="modo_simulado" value="1"
                    {{ old('modo_simulado', $config->modo_simulado) ? 'checked' : '' }}
                    class="w-5 h-5 rounded accent-red-600">
                <span class="text-sm font-black italic uppercase text-zinc-900 dark:text-white">Modo simulado (sin hardware físico conectado)</span>
            </label>
            <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mt-3 ml-8">
                Actívalo mientras no tengan impresora/báscula reales conectadas.
            </p>
        </div>

        {{-- Impresora / Cajón --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl p-8 shadow-2xl space-y-5">
            <h3 class="text-lg font-black italic uppercase tracking-tighter text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-white/5 pb-4">
                Impresora <span class="text-red-600">/ Cajón</span>
            </h3>

            <div>
                <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">Tipo de conexión</label>
                <select name="impresora_tipo"
                    class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-zinc-900 dark:text-white font-bold uppercase italic focus:outline-none focus:border-red-500 transition-all">
                    <option value="usb" {{ old('impresora_tipo', $config->impresora_tipo) == 'usb' ? 'selected' : '' }}>USB (conectada a esta PC)</option>
                    <option value="red" {{ old('impresora_tipo', $config->impresora_tipo) == 'red' ? 'selected' : '' }}>Red (tiene IP propia)</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">Nombre exacto en Windows (solo si es USB)</label>
                <input type="text" name="impresora_nombre" value="{{ old('impresora_nombre', $config->impresora_nombre) }}"
                    placeholder="EJ. EPSON TM-T20III RECEIPT"
                    class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-zinc-900 dark:text-white font-bold uppercase italic focus:outline-none focus:border-red-500 transition-all placeholder-zinc-300 dark:placeholder-zinc-800">
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mt-2">Panel de Control → Dispositivos e impresoras, copia el nombre tal cual.</p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">IP de la impresora (solo si es de red)</label>
                <input type="text" name="impresora_ip" value="{{ old('impresora_ip', $config->impresora_ip) }}"
                    placeholder="192.168.1.50"
                    class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-zinc-900 dark:text-white font-bold italic focus:outline-none focus:border-red-500 transition-all placeholder-zinc-300 dark:placeholder-zinc-800">
            </div>

            <div>
                <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">Comando de apertura de cajón (bytes separados por coma)</label>
                <input type="text" name="cajon_comando_apertura" value="{{ old('cajon_comando_apertura', $config->cajon_comando_apertura) }}"
                    placeholder="27,112,0,25,250"
                    class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-red-600 dark:text-red-500 font-mono font-bold focus:outline-none focus:border-red-500 transition-all placeholder-zinc-300 dark:placeholder-zinc-800">
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mt-2">Default estándar: 27,112,0,25,250. Solo cambiar si la impresora final no abre el cajón con ese código.</p>
            </div>
        </div>

        {{-- Báscula --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl p-8 shadow-2xl space-y-5">
            <h3 class="text-lg font-black italic uppercase tracking-tighter text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-white/5 pb-4">
                Báscula <span class="text-red-600">(Granel)</span>
            </h3>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="bascula_activada" value="1"
                    {{ old('bascula_activada', $config->bascula_activada) ? 'checked' : '' }}
                    class="w-5 h-5 rounded accent-red-600">
                <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200">Báscula conectada en esta tienda</span>
            </label>

            <div>
                <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-2">Baud Rate</label>
                <input type="number" name="bascula_baud_rate" value="{{ old('bascula_baud_rate', $config->bascula_baud_rate) }}"
                    class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl p-4 text-zinc-900 dark:text-white font-bold focus:outline-none focus:border-red-500 transition-all">
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mt-2">Viene en el manual de la báscula. Default más común: 9600.</p>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-red-600 hover:bg-red-500 text-white font-black italic py-5 rounded-2xl transition-all uppercase text-xs tracking-[0.2em] shadow-xl shadow-red-900/30 transform hover:scale-[1.02] active:scale-95">
            GUARDAR CONFIGURACIÓN
        </button>
    </form>
</div>
@endsection
<div x-data="{ openApertura: true }" x-show="openApertura" x-cloak class="fixed inset-0 z-[3000] flex items-center justify-center bg-black/90 backdrop-blur-xl p-6">
    <div class="bg-white dark:bg-[#0d0d0d] p-10 rounded-[3rem] w-full max-w-md border border-zinc-200 dark:border-white/10 shadow-2xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black italic uppercase leading-none">FONDO DE <br><span class="text-green-600">INICIO</span></h2>
            <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-[0.2em] mt-3">Ingresa el efectivo disponible en caja</p>
        </div>
        <form action="{{ route('caja.apertura') }}" method="POST" class="space-y-6">
            @csrf
            <div class="relative">
                <span class="absolute left-6 top-1/2 -translate-y-1/2 font-black text-3xl opacity-30">$</span>
                <input type="number" name="monto_inicial" step="0.01" required autofocus placeholder="0.00"
                    class="w-full bg-zinc-100 dark:bg-black p-8 pl-16 rounded-3xl text-5xl font-black outline-none border-2 border-transparent focus:border-green-600 transition-all text-center">
            </div>
            <button type="submit" class="w-full bg-green-600 text-white font-black italic uppercase py-6 rounded-2xl shadow-xl border-b-4 border-green-900 active:scale-95 transition-all">
                ABRIR CAJA AHORA
            </button>
        </form>
    </div>
</div>
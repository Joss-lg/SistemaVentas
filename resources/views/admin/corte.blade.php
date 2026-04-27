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
            <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase mb-4 tracking-widest">Total Esperado en Sistema</p>
            <div class="text-6xl md:text-7xl font-black italic text-zinc-900 dark:text-white tracking-tighter transition-colors group-hover:text-orange-500">
                ${{ number_format($totalSistema, 2) }}
            </div>
            <p class="text-[9px] font-bold text-zinc-400 uppercase mt-2 italic">Fondo + Ventas - Compras</p>
            <i class="fas fa-calculator absolute -right-6 -bottom-6 text-9xl text-zinc-100 dark:text-white/5 -rotate-12 transition-transform group-hover:rotate-0"></i>
        </div>

        {{-- Formulario de Captura --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-8 rounded-3xl shadow-2xl transition-colors">
            <form action="{{ route('admin.corte.store') }}" method="POST" id="formCorte" class="space-y-6">
                @csrf
                {{-- Enviamos los valores reales al controlador --}}
                <input type="hidden" name="ventas_esperadas" value="{{ $totalSistema }}">
                <input type="hidden" name="monto_inicial" value="{{ $montoInicial }}">

                <div>
                    <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">
                        Efectivo Real en Caja ($)
                    </label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl font-black text-zinc-300 dark:text-zinc-700">$</span>
                        <input type="number" step="0.01" name="efectivo_real" id="efectivo_real" required autofocus
                               class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl py-6 pl-12 pr-6 text-4xl font-black text-green-600 dark:text-green-500 italic focus:outline-none focus:border-orange-500 dark:focus:border-orange-500/50 transition-all placeholder-zinc-300 dark:placeholder-zinc-800"
                               placeholder="0.00">
                    </div>
                </div>

                <button type="button" onclick="confirmarCorte()"
                        class="w-full bg-orange-600 hover:bg-orange-500 text-white font-black italic py-5 rounded-2xl transition-all uppercase text-xs tracking-[0.2em] shadow-xl shadow-orange-900/30 transform hover:scale-[1.02] active:scale-95">
                    GUARDAR Y FINALIZAR TURNO
                </button>
                
                <input type="hidden" name="total_ventas_tarjeta" value="0">
            </form>
        </div>
    </div>

    {{-- FLUJO DE CAJA (DETALLE) --}}
    <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-zinc-100 dark:border-white/5 bg-zinc-50/50 dark:bg-white/5">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Flujo de Efectivo del Turno</h3>
        </div>
        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[9px] font-black uppercase tracking-widest text-zinc-400 border-b border-zinc-100 dark:border-white/5">
                        <th class="px-6 py-4">Concepto</th>
                        <th class="px-6 py-4 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    {{-- Fondo Inicial --}}
                    <tr class="group hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Fondo de Inicio</p>
                            <p class="text-[9px] text-zinc-400 uppercase">Dinero con el que abriste</p>
                        </td>
                        <td class="px-6 py-4 text-right font-black italic text-zinc-900 dark:text-white">
                            +${{ number_format($montoInicial, 2) }}
                        </td>
                    </tr>
                    {{-- Ventas --}}
                    <tr class="group hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Ventas Totales</p>
                            <p class="text-[9px] text-zinc-400 uppercase">Efectivo ingresado por ventas</p>
                        </td>
                        <td class="px-6 py-4 text-right font-black italic text-green-600 dark:text-green-500">
                            +${{ number_format($ventasDelTurno, 2) }}
                        </td>
                    </tr>
                    {{-- Entrada de Mercancía (Compras/Salidas) --}}
                    @if(isset($totalCompras) && $totalCompras > 0)
                    <tr class="group hover:bg-red-500/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-red-600 dark:text-red-500 uppercase">Entrada de Mercancía</p>
                            <p class="text-[9px] text-zinc-400 uppercase">Salidas de efectivo para compras</p>
                        </td>
                        <td class="px-6 py-4 text-right font-black italic text-red-600 dark:text-red-500">
                            -${{ number_format($totalCompras, 2) }}
                        </td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-zinc-900 dark:bg-white text-white dark:text-black">
                        <td class="px-6 py-4 text-[10px] font-black uppercase tracking-widest">Total en Sistema</td>
                        <td class="px-6 py-4 text-right font-black italic text-lg">
                            ${{ number_format($totalSistema, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Alerta de Precaución --}}
    <div class="bg-orange-500/10 border border-orange-500/20 p-6 rounded-2xl flex flex-col md:flex-row items-center gap-4 transition-all hover:bg-orange-500/20">
        <div class="bg-orange-600 p-3 rounded-xl shadow-lg shadow-orange-900/20">
            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
        </div>
        <div>
            <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-black uppercase leading-relaxed tracking-wider">
                ¡Alerta con el conteo! <span class="text-orange-600 italic">No vayas a sumar mal.</span>
            </p>
            <p class="text-[10px] text-zinc-500 dark:text-zinc-500 font-bold uppercase mt-1">
                Asegúrate de contar bien el efectivo. Una vez guardado el corte, los datos se enviarán al administrador para su revisión inmediata.
            </p>
        </div>
    </div>
</div>

{{-- Scripts de Alerta --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarCorte() {
        const efectivo = document.getElementById('efectivo_real').value;
        
        if(!efectivo || efectivo <= 0) {
            Swal.fire({
                title: '¡Eit, pendejo!',
                text: 'Pon cuánto dinero hay en la caja primero.',
                icon: 'error',
                confirmButtonColor: '#ea580c',
                background: '#0d0d0d',
                color: '#ffffff'
            });
            return;
        }

        Swal.fire({
            title: '¿Confirmar cierre?',
            text: "Se guardará el corte y se cerrará tu sesión. ¡Revisa bien la lana!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#3f3f46',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'No, deja checo',
            background: '#0d0d0d',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formCorte').submit();
            }
        });
    }
</script>
@endsection
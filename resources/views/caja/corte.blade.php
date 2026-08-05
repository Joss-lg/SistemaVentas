@extends('layouts.cajero')

@section('title', 'Movimientos de Caja')

@section('content')
<div x-data="{ tab: 'ventas', ventaAbierta: null }" class="max-w-7xl mx-auto space-y-6 p-4 md:p-0 transition-colors duration-300">

    {{-- ENCABEZADO --}}
    <div class="border-b border-zinc-200 dark:border-white/5 pb-6">
        <h2 class="text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
            MOVIMIENTOS DE <span class="text-red-600">CAJA</span>
        </h2>
        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-1 ml-1">
            Turno abierto &mdash; {{ $fechaApertura->format('d/m/Y') }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===================== COLUMNA IZQUIERDA: RESUMEN FIJO ===================== --}}
        <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-6 self-start">

            {{-- EFECTIVO ESPERADO --}}
            <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-7 rounded-3xl shadow-2xl relative overflow-hidden group">
                {{-- Ícono decorativo: detrás de todo --}}
                <i class="fas fa-vault absolute -right-4 -top-4 text-8xl text-zinc-100 dark:text-white/5 -rotate-12 transition-transform group-hover:rotate-0 -z-0 pointer-events-none select-none"></i>

                <div class="relative z-10">
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase mb-3 tracking-widest">Efectivo Esperado en Cajón</p>
                    <div class="text-5xl font-black italic text-zinc-900 dark:text-white tracking-tighter transition-colors group-hover:text-orange-500">
                        ${{ number_format($totalSistema, 2) }}
                    </div>

                    {{-- Barra de composición --}}
                    @php
                        $baseTotal = max($montoInicial + $ventasEfectivo, 1);
                        $pctFondo = min(100, ($montoInicial / $baseTotal) * 100);
                        $pctVentas = min(100 - $pctFondo, ($ventasEfectivo / $baseTotal) * 100);
                    @endphp
                    <div class="mt-5 h-2 w-full rounded-full bg-zinc-100 dark:bg-white/5 overflow-hidden flex">
                        <div class="h-full bg-zinc-400 dark:bg-zinc-600" style="width: {{ $pctFondo }}%"></div>
                        <div class="h-full bg-emerald-500" style="width: {{ $pctVentas }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-[9px] font-black uppercase tracking-wider text-zinc-400">
                        <span><span class="inline-block w-2 h-2 rounded-full bg-zinc-400 dark:bg-zinc-600 mr-1"></span>Fondo ${{ number_format($montoInicial, 2) }}</span>
                        <span><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span>Efectivo ${{ number_format($ventasEfectivo, 2) }}</span>
                    </div>
                    @if($totalCompras > 0)
                        <p class="text-[9px] font-bold text-red-500 uppercase mt-3 italic">- ${{ number_format($totalCompras, 2) }} en salidas a proveedores</p>
                    @endif
                    @if(isset($totalCambio) && $totalCambio > 0)
                        <p class="text-[9px] font-bold text-zinc-400 uppercase mt-1 italic">Ya se descontaron ${{ number_format($totalCambio, 2) }} en cambios entregados</p>
                    @endif
                </div>
            </div>

            {{-- MÉTODOS DE PAGO --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-4 rounded-2xl text-center">
                    <i class="fas fa-money-bill-wave text-emerald-500 mb-2"></i>
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Efectivo</p>
                    <p class="text-sm font-black italic text-emerald-600 dark:text-emerald-500 tabular-nums">${{ number_format($ventasEfectivo, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-4 rounded-2xl text-center">
                    <i class="fas fa-credit-card text-blue-500 mb-2"></i>
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Tarjeta</p>
                    <p class="text-sm font-black italic text-blue-600 dark:text-blue-500 tabular-nums">${{ number_format($ventasTarjeta, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-4 rounded-2xl text-center">
                    <i class="fas fa-mobile-alt text-purple-500 mb-2"></i>
                    <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Transf.</p>
                    <p class="text-sm font-black italic text-purple-600 dark:text-purple-500 tabular-nums">${{ number_format($ventasTransferencia, 2) }}</p>
                </div>
            </div>

            {{-- TOTAL DEL TURNO --}}
            <div class="bg-zinc-900 dark:bg-white/5 border border-zinc-800 dark:border-white/10 p-5 rounded-2xl flex items-center justify-between">
                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Total Vendido en Turno</span>
                <span class="text-xl font-black italic text-white">${{ number_format($ventasDelTurno, 2) }}</span>
            </div>

            {{-- FORMULARIO DE CIERRE --}}
            <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-7 rounded-3xl shadow-2xl">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500 mb-5">Finalizar Turno</h3>
                <form action="{{ route('admin.corte.store') }}" method="POST" id="formCorte" class="space-y-5">
                    @csrf
                    <input type="hidden" name="ventas_esperadas" id="ventas_esperadas" value="{{ $totalSistema }}">
                    <input type="hidden" name="monto_inicial" value="{{ $montoInicial }}">

                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3">
                            Efectivo Real en Caja
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-black text-zinc-300 dark:text-zinc-700">$</span>
                            <input type="number" step="0.01" name="efectivo_real" id="efectivo_real" required autofocus
                                   class="w-full bg-zinc-50 dark:bg-black border-2 border-zinc-100 dark:border-white/10 rounded-2xl py-4 pl-9 pr-4 text-2xl font-black text-green-600 dark:text-green-500 italic focus:outline-none focus:border-orange-500 dark:focus:border-orange-500/50 transition-all placeholder-zinc-300 dark:placeholder-zinc-800"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-orange-600 hover:bg-orange-500 text-white font-black italic py-4 rounded-2xl transition-all uppercase text-xs tracking-[0.2em] shadow-xl shadow-orange-900/30 transform hover:scale-[1.02] active:scale-95">
                        GUARDAR Y CERRAR CAJA
                    </button>
                </form>
            </div>
        </div>

        {{-- ===================== COLUMNA DERECHA: PESTAÑAS ===================== --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- SELECTOR DE PESTAÑAS --}}
            <div class="flex items-center gap-2 bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-2xl p-1.5 shadow-xl">
                <button @click="tab = 'ventas'"
                        :class="tab === 'ventas' ? 'bg-red-600 text-white shadow-lg' : 'text-zinc-500 hover:text-red-600'"
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-black italic uppercase text-[11px] tracking-widest transition-all">
                    <i class="fas fa-receipt"></i> Ventas ({{ $ventasDetalle->count() }})
                </button>
                <button @click="tab = 'proveedores'"
                        :class="tab === 'proveedores' ? 'bg-red-600 text-white shadow-lg' : 'text-zinc-500 hover:text-red-600'"
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-black italic uppercase text-[11px] tracking-widest transition-all">
                    <i class="fas fa-truck-loading"></i> Proveedores ({{ $comprasDelTurno->count() }})
                </button>
            </div>

            {{-- TAB: VENTAS DEL TURNO --}}
            <div x-show="tab === 'ventas'" x-cloak
                 class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
                <div class="divide-y divide-zinc-100 dark:divide-white/5 max-h-[640px] overflow-y-auto custom-scrollbar">
                    @forelse($ventasDetalle as $v)
                        <div>
                            <button type="button" @click="ventaAbierta = (ventaAbierta === {{ $v->id }}) ? null : {{ $v->id }}"
                                    class="w-full flex items-center justify-between p-4 text-xs font-bold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-white/[0.02] transition-colors">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-chevron-right text-red-600 text-[10px] transition-transform"
                                       :class="ventaAbierta === {{ $v->id }} ? 'rotate-90' : ''"></i>
                                    <span class="font-mono text-zinc-400">#{{ $v->folio ?? $v->id }}</span>
                                </div>

                                <div class="flex items-center space-x-4">
                                    @php $metodo = strtolower($v->tipo_pago ?? 'efectivo'); @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase
                                        {{ $metodo == 'tarjeta' ? 'bg-blue-500/10 text-blue-500' : ($metodo == 'transferencia' ? 'bg-purple-500/10 text-purple-500' : 'bg-emerald-500/10 text-emerald-500') }}">
                                        {{ $metodo }}
                                    </span>

                                    {{-- Monto recibido y cambio en el encabezado (solo si es efectivo) --}}
                                    @if($metodo == 'efectivo' && isset($v->pago_cliente))
                                        <div class="hidden sm:flex items-center space-x-2 text-[10px] font-mono text-zinc-400">
                                            <span>Recibido: <strong class="text-zinc-700 dark:text-zinc-300">${{ number_format($v->pago_cliente, 2) }}</strong></span>
                                            <span>&bull;</span>
                                            <span>Cambio: <strong class="text-amber-500">${{ number_format($v->cambio ?? 0, 2) }}</strong></span>
                                        </div>
                                    @endif

                                    <span class="text-emerald-600 dark:text-emerald-500 font-black italic w-16 text-right">${{ number_format($v->total, 2) }}</span>
                                </div>
                            </button>

                            <div x-show="ventaAbierta === {{ $v->id }}" x-collapse x-cloak class="px-4 pb-4 bg-zinc-50 dark:bg-white/[0.02]">
                                
                                {{-- Desglose de Pago --}}
                                @if($metodo == 'efectivo')
                                    <div class="flex items-center justify-between my-2 p-2 bg-zinc-100 dark:bg-white/5 rounded-xl text-[10px] font-bold text-zinc-600 dark:text-zinc-300">
                                        <div>
                                            <span class="text-zinc-400 uppercase">Recibido:</span>
                                            <span class="font-black font-mono ml-1 text-zinc-800 dark:text-white">${{ number_format($v->pago_cliente ?? $v->total, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-400 uppercase">Cambio:</span>
                                            <span class="font-black font-mono ml-1 text-amber-500">${{ number_format($v->cambio ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-400 uppercase">Total Neto:</span>
                                            <span class="font-black font-mono ml-1 text-emerald-500">${{ number_format($v->total, 2) }}</span>
                                        </div>
                                    </div>
                                @endif

                                <table class="w-full text-[11px] mt-2">
                                    <thead>
                                        <tr class="text-zinc-400 uppercase text-[9px] font-black tracking-wider border-b border-zinc-200 dark:border-white/5 pb-1">
                                            <th class="text-left py-1">Producto</th>
                                            <th class="text-center py-1">Cant.</th>
                                            <th class="text-right py-1">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 dark:divide-white/5">
                                        @foreach($v->detalles as $detalle)
                                            <tr>
                                                <td class="py-1.5 font-bold text-zinc-700 dark:text-zinc-200 uppercase">
                                                    {{ $detalle->producto?->descripcion ?? $detalle->descripcion ?? 'Producto' }}
                                                </td>
                                                <td class="py-1.5 text-center">{{ $detalle->cantidad }}</td>
                                                <td class="py-1.5 text-right font-black">${{ number_format($detalle->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-zinc-400 dark:text-zinc-600 italic text-xs font-bold uppercase">
                            <i class="fas fa-receipt text-3xl mb-3 block opacity-30"></i>
                            Aún no hay ventas en este turno.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB: PROVEEDORES / SALIDAS --}}
            <div x-show="tab === 'proveedores'" x-cloak
                 class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar max-h-[640px]">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-zinc-50 dark:bg-[#0d0d0d] text-zinc-400 dark:text-zinc-500 uppercase text-[9px] tracking-[0.2em] border-b border-zinc-100 dark:border-white/5">
                            <tr>
                                <th class="p-4 pl-6 font-black">Producto</th>
                                <th class="p-4 font-black text-center">Cantidad</th>
                                <th class="p-4 pr-6 font-black text-right">Costo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                            @forelse($comprasDelTurno as $c)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                                <td class="p-4 pl-6 font-bold italic text-zinc-800 dark:text-zinc-200 uppercase">{{ $c->producto?->descripcion ?? 'N/A' }}</td>
                                <td class="p-4 text-center text-zinc-500 dark:text-zinc-400 font-bold">{{ $c->cantidad }}</td>
                                <td class="p-4 pr-6 text-right font-black italic text-red-600 dark:text-red-500 tabular-nums">-${{ number_format($c->costo_total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-12 text-center text-zinc-400 dark:text-zinc-600 font-bold italic uppercase text-xs">
                                    <i class="fas fa-truck-loading text-3xl mb-3 block opacity-30"></i>
                                    Sin entradas de mercancía en este turno.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
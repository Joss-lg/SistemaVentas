@extends('layouts.cajero')

@section('title', 'Detalle de Corte')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-6 md:space-y-8 p-4 md:p-6 transition-colors duration-300">

    {{-- ENCABEZADO CON FECHAS SIN HORA --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-zinc-200 dark:border-white/5 pb-6 gap-4">
        <div>
            <a href="{{ route('admin.cajas.index') }}" class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-red-600 transition-colors mb-2 inline-flex items-center gap-1.5">
                <i class="fas fa-chevron-left text-[8px]"></i> Volver al Historial
            </a>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white leading-tight">
                CORTE DE <span class="text-red-600">{{ $corte->usuario->nombre ?? 'N/A' }}</span>
            </h2>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-1">
                {{ \Carbon\Carbon::parse($corte->fecha_apertura)->format('d/m/Y') }}
                &mdash;
                {{ \Carbon\Carbon::parse($corte->fecha_cierre)->format('d/m/Y') }}
            </p>
        </div>
    </div>

    {{-- RESUMEN GENERAL --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-5 rounded-2xl shadow-xl flex flex-col justify-center">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Fondo Inicial</p>
            <p class="text-2xl font-black italic text-zinc-900 dark:text-white tabular-nums">${{ number_format($corte->monto_inicial, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-5 rounded-2xl shadow-xl flex flex-col justify-center">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Esperado (Cajón)</p>
            <p class="text-2xl font-black italic text-zinc-900 dark:text-white tabular-nums">${{ number_format($corte->total_esperado, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-5 rounded-2xl shadow-xl flex flex-col justify-center">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Contado</p>
            <p class="text-2xl font-black italic text-emerald-600 dark:text-emerald-500 tabular-nums">${{ number_format($corte->total_contado, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-[#0d0d0d] border {{ $corte->difference < 0 ? 'border-red-500/30' : 'border-emerald-500/30' }} p-5 rounded-2xl shadow-xl flex flex-col justify-center">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Diferencia</p>
            <p class="text-2xl font-black italic tabular-nums {{ $corte->difference < 0 ? 'text-red-600 dark:text-red-500' : 'text-emerald-600 dark:text-emerald-500' }}">
                {{ $corte->difference >= 0 ? '+' : '' }}${{ number_format($corte->difference, 2) }}
            </p>
        </div>
    </div>

    {{-- DESGLOSE DE MÉTODOS DE PAGO DEL TURNO --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-5 rounded-2xl shadow-xl flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Efectivo</p>
                <p class="text-2xl font-black italic text-emerald-600 dark:text-emerald-500 mt-1">
                    ${{ number_format($corte->total_ventas_efectivo ?? $ventas->where('tipo_pago', 'efectivo')->sum('total'), 2) }}
                </p>
            </div>
            <i class="fas fa-money-bill-wave text-3xl text-emerald-500/20"></i>
        </div>

        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-5 rounded-2xl shadow-xl flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Tarjeta</p>
                <p class="text-2xl font-black italic text-blue-600 dark:text-blue-500 mt-1">
                    ${{ number_format($corte->total_ventas_tarjeta ?? $ventas->where('tipo_pago', 'tarjeta')->sum('total'), 2) }}
                </p>
            </div>
            <i class="fas fa-credit-card text-3xl text-blue-500/20"></i>
        </div>

        <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 p-5 rounded-2xl shadow-xl flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Transferencia</p>
                <p class="text-2xl font-black italic text-purple-600 dark:text-purple-500 mt-1">
                    ${{ number_format($corte->total_transferencia ?? $ventas->where('tipo_pago', 'transferencia')->sum('total'), 2) }}
                </p>
            </div>
            <i class="fas fa-mobile-alt text-3xl text-purple-500/20"></i>
        </div>
    </div>

    {{-- VENTAS DEL TURNO --}}
    <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-white/5 bg-zinc-50/50 dark:bg-white/[0.02]">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">
                Ventas del <span class="text-red-600">Turno</span> <span class="text-zinc-400">({{ $ventas->count() }})</span>
            </h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar max-h-96">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead class="sticky top-0 bg-zinc-50 dark:bg-[#0d0d0d] text-zinc-400 dark:text-zinc-500 uppercase text-[9px] tracking-[0.2em] border-b border-zinc-100 dark:border-white/5 z-10">
                    <tr>
                        <th class="p-4 pl-6 font-black">Folio</th>
                        <th class="p-4 font-black">Fecha</th>
                        <th class="p-4 font-black">Método</th>
                        <th class="p-4 font-black">Productos Vendidos</th>
                        <th class="p-4 pr-6 font-black text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse($ventas as $v)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-4 pl-6 font-bold italic text-zinc-800 dark:text-zinc-200 whitespace-nowrap">#{{ $v->folio ?? $v->id }}</td>
                        <td class="p-4 text-zinc-500 dark:text-zinc-400 text-xs font-bold whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-xs font-bold uppercase whitespace-nowrap">
                            @php $metodo = strtolower($v->tipo_pago ?? 'efectivo'); @endphp
                            @if($metodo == 'tarjeta')
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-blue-500/10 text-blue-500 border border-blue-500/20">Tarjeta</span>
                            @elseif($metodo == 'transferencia')
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-purple-500/10 text-purple-500 border border-purple-500/20">Transferencia</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">Efectivo</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-zinc-700 dark:text-zinc-300 font-medium">
                            @if($v->detalles && $v->detalles->count() > 0)
                                <div class="flex flex-col gap-1">
                                    @foreach($v->detalles as $detalle)
                                        <span class="inline-flex items-center gap-1.5">
                                            <b class="text-red-500 font-black">{{ $detalle->cantidad ?? 1 }}x</b> 
                                            <span class="uppercase">
                                                {{ $detalle->producto->descripcion ?? $detalle->descripcion ?? 'Producto' }}
                                            </span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-zinc-400 italic text-[11px]">Sin detalles</span>
                            @endif
                        </td>
                        <td class="p-4 pr-6 text-right font-black italic text-emerald-600 dark:text-emerald-500 tabular-nums whitespace-nowrap">${{ number_format($v->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-zinc-400 dark:text-zinc-600 font-bold italic uppercase text-xs">Sin ventas en este turno.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ENTRADAS DE MERCANCÍA DEL TURNO --}}
    <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-white/5 bg-zinc-50/50 dark:bg-white/[0.02]">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">
                Entradas de <span class="text-red-600">Mercancía</span> <span class="text-zinc-400">({{ $compras->count() }})</span>
            </h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar max-h-72">
            <table class="w-full text-left border-collapse min-w-[450px]">
                <thead class="sticky top-0 bg-zinc-50 dark:bg-[#0d0d0d] text-zinc-400 dark:text-zinc-500 uppercase text-[9px] tracking-[0.2em] border-b border-zinc-100 dark:border-white/5 z-10">
                    <tr>
                        <th class="p-4 pl-6 font-black">Producto</th>
                        <th class="p-4 font-black text-center">Cantidad</th>
                        <th class="p-4 pr-6 font-black text-right">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse($compras as $c)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-4 pl-6 font-bold italic text-zinc-800 dark:text-zinc-200 uppercase">{{ $c->producto->descripcion ?? 'N/A' }}</td>
                        <td class="p-4 text-center text-zinc-500 dark:text-zinc-400 font-bold whitespace-nowrap">{{ $c->cantidad }}</td>
                        <td class="p-4 pr-6 text-right font-black italic text-blue-600 dark:text-blue-500 tabular-nums whitespace-nowrap">-${{ number_format($c->costo_total, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-8 text-center text-zinc-400 dark:text-zinc-600 font-bold italic uppercase text-xs">Sin entradas de mercancía en este turno.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
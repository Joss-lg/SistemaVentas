@extends('layouts.admin')

@section('content')
<div class="p-6 bg-black min-h-screen text-white sm:ml-64">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-wallet text-red-500"></i> Control de Gastos y Egresos
            </h1>
            <p class="text-gray-400">Gestiona las salidas de dinero de la caja física.</p>
        </div>
        <div class="bg-gray-900 p-4 rounded-xl border border-gray-800 shadow-lg">
            <span class="text-gray-400 block text-sm uppercase font-bold">Salida Hoy</span>
            <span class="text-3xl font-mono text-red-500 font-bold">${{ number_format($totalDia, 2) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-gray-900 rounded-2xl p-6 border border-gray-800 shadow-2xl">
                <h3 class="text-xl font-bold mb-6 border-b border-gray-800 pb-2">Nuevo Gasto Manual</h3>
                
                <form action="{{ route('gastos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Descripción del Gasto</label>
                        <input type="text" name="descripcion" required placeholder="Ej. Pago de Luz" 
                            class="w-full bg-black border border-gray-700 rounded-lg p-3 text-white focus:border-red-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Monto ($)</label>
                        <input type="number" step="0.01" name="monto" required placeholder="0.00"
                            class="w-full bg-black border border-gray-700 rounded-lg p-3 text-white focus:border-red-500 focus:outline-none transition-all font-mono text-lg">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Categoría</label>
                        <select name="categoria" class="w-full bg-black border border-gray-700 rounded-lg p-3 text-white focus:border-red-500 focus:outline-none">
                            <option value="GENERAL">General</option>
                            <option value="INVENTARIO">Inventario / Mercancía</option>
                            <option value="SERVICIOS">Servicios (Luz/Agua/Net)</option>
                            <option value="PERSONAL">Pago Personal</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-red-900/20 transition-all transform hover:scale-[1.02]">
                        REGISTRAR SALIDA DE DINERO
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-gray-900 rounded-2xl border border-gray-800 shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-800">
                    <h3 class="text-xl font-bold">Movimientos Recientes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/50 text-gray-400 uppercase text-xs">
                            <tr>
                                <th class="p-4 font-bold">Descripción</th>
                                <th class="p-4 font-bold">Categoría</th>
                                <th class="p-4 font-bold text-center">Hora</th>
                                <th class="p-4 font-bold text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse($gastos as $g)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="p-4 font-semibold">{{ $g->descripcion }}</td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $g->categoria == 'INVENTARIO' ? 'bg-blue-900/50 text-blue-400' : 'bg-gray-800 text-gray-300' }}">
                                        {{ $g->categoria }}
                                    </span>
                                </td>
                                <td class="p-4 text-center text-gray-500 text-sm">{{ $g->created_at->format('h:i A') }}</td>
                                <td class="p-4 text-right font-mono text-red-400 font-bold">-${{ number_format($g->monto, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-gray-600 italic">No hay gastos registrados el día de hoy.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-900 p-6 rounded-2xl border border-green-500/30 hover:border-green-500 transition-all shadow-lg">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="bg-green-500/10 p-3 rounded-lg">
                            <i class="fas fa-file-excel text-3xl text-green-500"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Reporte de Movimientos</h3>
                            <p class="text-xs text-green-500 font-mono">EXCEL GENERAL</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 text-sm">Ventas, Cortes de caja, Gastos e Inventario en un solo archivo.</p>
                    <a href="{{ route('admin.reporte.excel') }}" 
                       class="flex items-center justify-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-green-900/40">
                        <i class="fas fa-download"></i> DESCARGAR
                    </a>
                </div>

                
            </div>
        </div>
    </div>
</div>
@endsection
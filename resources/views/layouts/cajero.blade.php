<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap');
        .font-digital { font-family: 'Orbitron', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #dc2626; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    </style>
</head>
<body class="bg-black text-white font-sans antialiased overflow-hidden">

    <div class="flex h-screen">
        <aside class="w-72 bg-[#0d0d0d] border-r border-white/5 flex flex-col shadow-2xl">
            <div class="p-6 border-b border-white/5">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-red-600 text-white px-2 py-0.5 font-black text-sm italic tracking-tighter">F1 VENTAS</span>
                    <h1 class="text-xl font-black tracking-widest text-zinc-200 uppercase">Abarrotes</h1>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-3">
                <a href="{{ route('ventas.index') }}" 
                   class="flex items-center space-x-3 p-4 rounded-xl transition-all {{ request()->routeIs('ventas.index') ? 'bg-red-600 text-white shadow-lg shadow-red-900/40' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}">
                    <i class="fas fa-cash-register"></i>
                    <span class="font-black italic uppercase text-xs tracking-widest">Punto de Venta</span>
                </a>

                <a href="{{ route('ventas.inventario') }}" 
                   class="flex items-center space-x-3 p-4 rounded-xl text-zinc-500 hover:bg-white/5 hover:text-white transition-all">
                    <i class="fas fa-boxes-stacked"></i>
                    <span class="font-black italic uppercase text-xs tracking-widest">Ver Inventario</span>
                </a>

                <a href="{{ route('admin.corte') }}" 
                   class="flex items-center space-x-3 p-4 rounded-xl text-orange-500 hover:bg-orange-600/10 transition-all border border-orange-600/10">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span class="font-black italic uppercase text-xs tracking-widest">Corte del Día</span>
                </a>

                @if(Auth::user()->username == 'admin')
                <div class="pt-4 mt-4 border-t border-white/5">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center space-x-3 p-4 rounded-xl bg-zinc-900 text-zinc-400 hover:text-white transition-all border border-zinc-800">
                        <i class="fas fa-user-shield"></i>
                        <span class="font-black italic uppercase text-[10px] tracking-widest">Panel Admin</span>
                    </a>
                </div>
                @endif
            </nav>

            <div class="p-6 bg-black/40 border-t border-white/5">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center font-black italic shadow-lg shadow-red-900/20">
                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 leading-none mb-1">Cajero Activo</p>
                        <p class="text-sm font-bold text-white uppercase italic leading-none">{{ Auth::user()->username }}</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full group flex items-center justify-between bg-white/5 hover:bg-red-600/10 p-3 rounded-lg transition-all border border-white/5">
                        <span class="text-[10px] font-black text-zinc-500 group-hover:text-red-500 tracking-[0.2em]">CERRAR SESIÓN</span>
                        <i class="fas fa-power-off text-zinc-600 group-hover:text-red-600"></i>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 bg-black relative overflow-hidden">
            <div class="p-8 h-full overflow-y-auto custom-scrollbar">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        window.addEventListener('keydown', function(e) {
            // F1 - Ir a Ventas
            if (e.key === 'F1') {
                e.preventDefault();
                window.location.href = "{{ route('ventas.index') }}";
            }
        });
        if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('SW de Abarrotes registrado', reg))
                .catch(err => console.log('Fallo al registrar SW', err));
        });
    }
    </script>

    @stack('scripts')
</body>
</html>
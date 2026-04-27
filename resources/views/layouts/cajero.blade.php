<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 SISTEMA (CAJERO) - @yield('title')</title>

    {{-- Script de detección inmediata para evitar el flash blanco --}}
    <script>
        const temaUsuario = "{{ Auth::user()->tema ?? 'claro' }}";
        if (temaUsuario === 'oscuro') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    {{-- Cargamos SweetAlert y los recursos de Vite --}}
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    {{-- AlpineJS para los modales de apertura/cierre --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- CDN de Tailwind 3.4 para soporte híbrido --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- CONFIGURACIÓN CRÍTICA PARA TAILWIND 3.4 --}}
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>

    <script src="{{ asset('/service-worker.js') }}"></script>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;700;900&display=swap');
        
        body { font-family: 'Inter', sans-serif; }
        .font-digital { font-family: 'Orbitron', sans-serif; }
        [x-cloak] { display: none !important; }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #dc2626; border-radius: 10px; }
        
        .translate-x-full-custom { transform: translateX(1.375rem); }

        input:focus, button:focus, a:focus { outline: none !important; border: none !important; ring: 0 !important; --tw-ring-shadow: none !important; }
    </style>
</head>
<body class="bg-zinc-100 dark:bg-black text-zinc-900 dark:text-white flex min-h-screen transition-colors duration-300">

    <aside class="w-80 min-w-[320px] bg-white dark:bg-[#0d0d0d] flex flex-col border-r border-zinc-200 dark:border-white/5 transition-colors duration-300">
        
        <div class="p-10">
            <h2 class="text-zinc-800 dark:text-white text-3xl font-black italic uppercase tracking-tighter leading-none">
                F1 <br> <span class="text-red-600">CAJERO</span>
            </h2>
        </div>

        <nav class="flex-1 px-6 space-y-4 overflow-y-auto custom-scrollbar">
            @php
                $active = "bg-red-600 text-white shadow-[0_10px_20px_rgba(220,38,38,0.3)]";
                $inactive = "text-zinc-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-white/5";
                $base = "flex items-center space-x-4 p-5 rounded-xl font-black italic uppercase text-sm tracking-wide transition-all";
            @endphp

            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-4 ml-2 border-b border-zinc-100 dark:border-white/5 pb-2">Menú Principal</p>

            <a href="{{ route('ventas.index') }}" class="{{ $base }} {{ request()->routeIs('ventas.index') ? $active : $inactive }}">
                <i class="fas fa-cash-register text-lg"></i>
                <span>Punto de Venta</span>
            </a>

            <a href="{{ route('ventas.inventario') }}" class="{{ $base }} {{ request()->routeIs('ventas.inventario') ? $active : $inactive }}">
                <i class="fas fa-boxes-stacked text-lg"></i>
                <span>Inventario</span>
            </a>

            <a href="{{ route('admin.corte') }}" class="{{ $base }} {{ request()->routeIs('admin.corte') ? $active : $inactive }}">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
                <span>Corte de Caja</span>
            </a>

            @if(Auth::check() && Auth::user()->esAdmin())
                <div class="pt-6 mt-6 border-t-2 border-zinc-100 dark:border-white/5">
                    <p class="text-[10px] font-black text-red-600 uppercase tracking-[0.3em] mb-4 ml-2">Administración</p>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-4 p-5 rounded-2xl font-black italic uppercase text-sm tracking-wide transition-all bg-zinc-950 dark:bg-white text-white dark:text-black shadow-xl hover:bg-red-600 dark:hover:bg-red-600 dark:hover:text-white group">
                        <i class="fas fa-user-shield text-lg group-hover:rotate-12 transition-transform"></i>
                        <span>Panel del Admin</span>
                    </a>
                </div>
            @endif

            <div class="pt-6">
                <div class="p-5 bg-zinc-100 dark:bg-[#1a1a1a] border-l-4 border-red-600 rounded-r-xl">
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Terminal Activa</p>
                    <span class="text-zinc-900 dark:text-white font-black italic uppercase text-xs">{{ Auth::user()->username }}</span>
                </div>
            </div>
        </nav>

        <div class="p-10 border-t border-zinc-200 dark:border-white/5 space-y-6">
            <div class="flex items-center justify-between bg-zinc-100 dark:bg-white/5 p-3 rounded-lg border border-zinc-200 dark:border-white/5">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Modo Oscuro</span>
                <button id="theme-toggle" class="relative inline-flex items-center h-5 w-10 rounded-full bg-zinc-300 dark:bg-red-600 transition-colors focus:outline-none">
                    <span id="switch-dot" class="inline-block w-3.5 h-3.5 transform bg-white rounded-full transition-transform {{ Auth::user()->tema == 'oscuro' ? 'translate-x-[1.375rem]' : 'translate-x-1' }}"></span>
                </button>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-zinc-400 hover:text-red-500 text-xs font-black uppercase tracking-[0.2em] flex items-center space-x-3 transition-colors">
                    <i class="fas fa-power-off"></i>
                    <span>Desconectar</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 bg-zinc-50 dark:bg-black p-8 md:p-12 overflow-y-auto transition-colors duration-300 relative shadow-inner">
        <div class="absolute top-0 right-0 p-20 opacity-[0.02] dark:opacity-[0.05] pointer-events-none">
            <i class="fas fa-bolt text-[400px] -rotate-12"></i>
        </div>

        <div class="relative z-10">
            @yield('content')
        </div>
    </main>

    {{-- MODAL DE APERTURA DE CAJA OBLIGATORIO --}}
    @if(!session('turno_abierto'))
    <div x-data="{ openApertura: true }" x-show="openApertura" x-cloak class="fixed inset-0 z-[3000] flex items-center justify-center bg-black/90 backdrop-blur-xl p-6">
        <div class="bg-white dark:bg-[#0d0d0d] p-10 rounded-[3rem] w-full max-w-md border border-zinc-200 dark:border-white/10 shadow-2xl">
            <div class="text-center mb-8">
                <h2 class="text-hibrido text-3xl font-black italic uppercase leading-none">FONDO DE <br><span class="text-green-600">INICIO</span></h2>
                <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-[0.2em] mt-3">Ingresa el efectivo disponible en caja</p>
            </div>

            <form action="{{ route('caja.apertura') }}" method="POST" class="space-y-6">
                @csrf
                <div class="relative">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-hibrido font-black text-3xl opacity-30">$</span>
                    <input type="number" name="monto_inicial" step="0.01" required autofocus placeholder="0.00"
                        class="w-full bg-zinc-100 dark:bg-black p-8 pl-16 rounded-3xl text-hibrido text-5xl font-black outline-none border-2 border-transparent focus:border-green-600 transition-all text-center">
                </div>
                <button type="submit" class="w-full bg-green-600 text-white font-black italic uppercase py-6 rounded-2xl shadow-xl border-b-4 border-green-900 active:scale-95 transition-all">
                    ABRIR CAJA AHORA
                </button>
            </form>
        </div>
    </div>
    @endif

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const dot = document.getElementById('switch-dot');
        const html = document.documentElement;

        themeToggleBtn.addEventListener('click', () => {
            let nuevoTema = 'claro';
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                dot.style.transform = 'translateX(0.25rem)';
                nuevoTema = 'claro';
            } else {
                html.classList.add('dark');
                dot.style.transform = 'translateX(1.375rem)';
                nuevoTema = 'oscuro';
            }

            fetch("{{ route('user.theme') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ theme: nuevoTema })
            });
        });

        window.addEventListener('keydown', function(e) {
            if (e.key === 'F1') {
                e.preventDefault();
                window.location.href = "{{ route('ventas.index') }}";
            }
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => console.log('%c🚀 ¡TODO AL CHINGO!', 'color: #dc2626; font-weight: 900;'))
                    .catch(err => console.log('SW fallo', err));
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
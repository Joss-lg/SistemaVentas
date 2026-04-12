<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>

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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Estilos de scrollbar nativos (no uses @apply aquí) */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #dc2626; border-radius: 10px; }
        
        /* Ajuste para el punto del switch */
        .translate-x-full-custom { transform: translateX(1.375rem); }
    </style>
</head>
<body class="bg-zinc-100 dark:bg-black text-zinc-900 dark:text-white flex min-h-screen transition-colors duration-300">

    <aside class="w-80 min-w-[320px] bg-white dark:bg-[#0d0d0d] flex flex-col border-r border-zinc-200 dark:border-white/5 transition-colors duration-300">
        
        <div class="p-10">
            <h2 class="text-zinc-800 dark:text-white text-3xl font-black italic uppercase tracking-tighter leading-none">
                ADMIN <br> <span class="text-red-600">PANEL</span>
            </h2>
        </div>

        <nav class="flex-1 px-6 space-y-4 overflow-y-auto custom-scrollbar">
            @php
                $active = "bg-red-600 text-white shadow-[0_10px_20px_rgba(220,38,38,0.3)]";
                $inactive = "text-zinc-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-white/5";
                $base = "flex items-center space-x-4 p-5 rounded-xl font-black italic uppercase text-sm tracking-wide transition-all";
            @endphp

            <button onclick="abrirCajonManual()" 
                class="w-full flex items-center space-x-4 p-5 rounded-xl font-black italic uppercase text-sm tracking-wide transition-all border-2 border-yellow-600/20 text-yellow-600 hover:bg-yellow-600 hover:text-white group mb-6">
                <i class="fas fa-cash-register text-lg group-hover:scale-110 transition-transform"></i>
                <span>Abrir Cajón</span>
            </button>

            <div class="separator border-t border-zinc-200 dark:border-white/5 my-4"></div>

            <a href="{{ route('admin.dashboard') }}" class="{{ $base }} {{ request()->routeIs('admin.dashboard') ? $active : $inactive }}">
                <i class="fas fa-th-large text-lg"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('productos.index') }}" class="{{ $base }} {{ request()->routeIs('productos.*') ? $active : $inactive }}">
                <i class="fas fa-box text-lg"></i>
                <span>Inventario</span>
            </a>

            <a href="{{ route('admin.reportes') }}" class="{{ $base }} {{ request()->routeIs('admin.reportes') ? $active : $inactive }}">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
                <span>Reporte Ventas</span>
            </a>

            <a href="{{ route('admin.compras.index') }}" class="{{ $base }} {{ request()->routeIs('admin.compras.index') ? $active : $inactive }}">
                <i class="fas fa-history text-lg"></i>
                <span>Historial Proveedores</span>
            </a>
            
            <a href="{{ route('admin.gastos') }}" class="{{ $base }} {{ request()->routeIs('admin.gastos') ? $active : $inactive }}">
                <i class="fas fa-hand-holding-dollar text-lg"></i>
                <span>Flujo de caja</span>
            </a>

            <div class="pt-6">
                <a href="{{ route('ventas.index') }}" class="flex items-center space-x-4 p-5 bg-zinc-100 dark:bg-[#1a1a1a] border-l-4 border-green-500 rounded-r-xl group">
                    <i class="fas fa-shopping-cart text-green-500 text-lg"></i>
                    <span class="text-green-600 dark:text-green-500 font-black uppercase text-xs tracking-tighter">Ir a Punto de Venta (F1)</span>
                </a>
            </div>

            <a href="{{ route('admin.usuarios.index') }}" class="{{ $base }} {{ request()->routeIs('admin.usuarios.*') ? $active : $inactive }}">
                <i class="fas fa-user-plus text-lg"></i>
                <span>Gestionar Cajeros</span>
            </a>
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
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 bg-zinc-50 dark:bg-black p-8 md:p-12 overflow-y-auto transition-colors duration-300">
        @yield('content')
    </main>

    <script>
    const themeToggleBtn = document.getElementById('theme-toggle');
    const dot = document.getElementById('switch-dot');
    const html = document.documentElement;

    themeToggleBtn.addEventListener('click', () => {
        let nuevoTema = 'claro';
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            dot.style.transform = 'translateX(0.25rem)'; // 1 en rem aprox
            nuevoTema = 'claro';
        } else {
            html.classList.add('dark');
            dot.style.transform = 'translateX(1.375rem)'; // equivalente a translate-x-5.5
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

    // FUNCIÓN DE APERTURA DE CAJÓN OPTIMIZADA CON SWEETALERT2
    function abrirCajonManual() {
        Swal.fire({
            title: 'SISTEMA F1 - CAJÓN',
            text: '¿Confirmas el envío de señal para abrir el cajón de dinero?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Rojo F1
            cancelButtonColor: '#27272a', // Zinc oscuro
            confirmButtonText: 'SÍ, ABRIR AHORA',
            cancelButtonText: 'CANCELAR',
            reverseButtons: true,
            background: document.documentElement.classList.contains('dark') ? '#0d0d0d' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#09090b',
            customClass: {
                popup: 'rounded-[2rem] border-2 border-zinc-200 dark:border-white/10 shadow-2xl',
                title: 'font-black italic uppercase tracking-tighter text-2xl',
                confirmButton: 'rounded-xl font-black italic uppercase text-xs px-8 py-4 transition-transform hover:scale-105',
                cancelButton: 'rounded-xl font-black italic uppercase text-xs px-8 py-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Ejecutamos la apertura mediante fetch para evitar ventanas emergentes molestas
                fetch("{{ route('admin.cajon.abrir') }}")
                    .then(() => {
                        Swal.fire({
                            title: 'SEÑAL ENVIADA',
                            text: 'El cajón ha sido desbloqueado.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: document.documentElement.classList.contains('dark') ? '#0d0d0d' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#09090b'
                        });
                    })
                    .catch(err => {
                        console.error('Error al abrir cajón:', err);
                        // Si el fetch falla, intentamos tu método anterior como respaldo
                        const win = window.open("{{ route('admin.cajon.abrir') }}", 'Cajon', 'width=100,height=100,left=0,top=0');
                        if (win) { setTimeout(() => { win.close(); }, 500); }
                    });
            }
        });
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === 'F1') {
            e.preventDefault();
            window.location.href = "{{ route('ventas.index') }}";
        }
    });

    // Registro de Service Worker para modo offline
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('SW de Abarrotes registrado'))
                .catch(err => console.log('Fallo al registrar SW', err));
        });
    }
</script>
</body>
</html>
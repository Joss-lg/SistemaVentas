<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-black text-white flex min-h-screen">

    <aside class="w-80 min-w-[320px] bg-[#0d0d0d] flex flex-col border-r border-white/5">
        
        <div class="p-10">
            <h2 class="text-white text-3xl font-black italic uppercase tracking-tighter leading-none">
                ADMIN <br> <span class="text-red-600">PANEL</span>
            </h2>
        </div>

        <nav class="flex-1 px-6 space-y-4">
            @php
                $active = "bg-red-600 text-white shadow-[0_10px_20px_rgba(220,38,38,0.3)]";
                $inactive = "text-gray-400 hover:text-white hover:bg-white/5";
                // Estilo base para los links
                $base = "flex items-center space-x-4 p-5 rounded-xl font-black italic uppercase text-sm tracking-wide transition-all";
            @endphp

            <button onclick="abrirCajonManual()" 
                class="w-full flex items-center space-x-4 p-5 rounded-xl font-black italic uppercase text-sm tracking-wide transition-all border-2 border-yellow-600/20 text-yellow-500 hover:bg-yellow-600 hover:text-white group mb-6">
                <i class="fas fa-cash-register text-lg group-hover:scale-110 transition-transform"></i>
                <span>Abrir Cajón</span>
            </button>

            <div class="separator border-t border-white/5 my-4"></div>

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
                <i class="fas fa-history text-lg"></i>
                <span>Flujo de caja</span>
            </a>

            <div class="pt-6">
                <a href="{{ route('ventas.index') }}" class="flex items-center space-x-4 p-5 bg-[#1a1a1a] border-l-4 border-green-500 rounded-r-xl group">
                    <i class="fas fa-shopping-cart text-green-500 text-lg"></i>
                    <span class="text-green-500 font-black uppercase text-xs tracking-tighter">Ir a Punto de Venta (F1)</span>
                </a>
            </div>

            <a href="{{ route('admin.usuarios.index') }}" class="{{ $base }} {{ request()->routeIs('admin.usuarios.*') ? $active : $inactive }}">
                <i class="fas fa-user-plus text-lg"></i>
                <span>Gestionar Cajeros</span>
            </a>
            
        </nav>


        <div class="p-10 border-t border-white/5">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-red-500 text-xs font-black uppercase tracking-[0.2em] flex items-center space-x-3 transition-colors">
                    <i class="fas fa-power-off"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 bg-black p-12 overflow-y-auto">
        @yield('content')
    </main>

    <script>
        function abrirCajonManual() {
            if(confirm('¿Desea enviar la señal para abrir el cajón de dinero?')) {
                // Se abre una ventana pequeña a la ruta que dispara la impresión
                // Asegúrate de tener esta ruta definida en web.php
                const win = window.open("{{ route('admin.cajon.abrir') }}", 'Cajon', 'width=100,height=100,left=0,top=0');
                
                // Cerramos la ventanita después de medio segundo
                if (win) {
                    setTimeout(() => {
                        win.close();
                    }, 500);
                }
            }
        }
        if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('Service Worker registrado con éxito', reg))
                .catch(err => console.warn('Error al registrar el Service Worker', err));
        });
    }
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

</body>
</html>
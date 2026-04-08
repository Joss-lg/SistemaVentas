<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | Abarrotes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at center, #1a1a1a 0%, #000000 100%);
        }
        .login-card {
            background: rgba(24, 24, 27, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }
        .input-dark {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid #3f3f46;
            color: white;
            transition: all 0.3s;
        }
        .input-dark:focus {
            border-color: #dc2626;
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.2);
            outline: none;
        }
        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.6);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="login-card w-full max-auto max-w-md rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-600 rounded-full blur-[80px] opacity-20"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-red-900 rounded-full blur-[80px] opacity-20"></div>

        <div class="relative z-10 text-center mb-10">
            <h2 class="text-5xl font-black text-white italic tracking-tighter uppercase">
                <span class="text-red-600">Aba</span>rrotes
            </h2>
            <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-[0.2em] mt-2">Panel de Control de Ventas</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-600/10 border-l-4 border-red-600 text-red-500 text-sm font-bold animate-pulse">
                Usuario o contraseña incorrectos.
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-zinc-400 font-black uppercase text-[10px] mb-2 ml-1">Usuario</label>
                <input type="text" name="username" required 
                    class="input-dark w-full p-4 rounded-xl font-bold tracking-wide" 
                    placeholder="Escribe tu usuario...">
            </div>

            <div>
                <label class="block text-zinc-400 font-black uppercase text-[10px] mb-2 ml-1">Contraseña</label>
                <input type="password" name="password" required 
                    class="input-dark w-full p-4 rounded-xl font-bold tracking-wide" 
                    placeholder="••••••••">
            </div>

            <div class="pt-4">
                <button type="submit" 
                    class="btn-glow w-full bg-red-600 hover:bg-red-500 text-white font-black py-4 rounded-xl uppercase tracking-widest italic transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg">
                    Iniciar Sesión
                </button>
            </div>
        </form>

        <footer class="mt-10 text-center">
            <p class="text-zinc-600 font-bold text-[9px] uppercase tracking-tighter">
                &copy; 2026 Sistema de Gestión de Abarrotes - F1 Tech
            </p>
        </footer>
    </div>

</body>
</html>
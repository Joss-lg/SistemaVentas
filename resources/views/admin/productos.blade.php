@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    [x-cloak] { display: none !important; }
    /* Forzamos que el texto sea negro sólido en modo claro */
    .modo-hibrido-texto {
        color: #09090b !important;
    }
    .dark .modo-hibrido-texto {
        color: #ffffff !important;
    }
</style>

<div x-data="{ openCreate: false }" class="w-full px-6">
    
    {{-- Header --}}
    <div class="flex justify-between items-end mb-12 border-b border-zinc-200 dark:border-zinc-800 pb-8">
        <div>
            <h1 class="modo-hibrido-texto text-5xl font-black italic uppercase tracking-tighter leading-none">
                GESTIÓN DE <br> <span class="text-red-600">PERSONAL</span>
            </h1>
        </div>
        <button @click="openCreate = true" class="bg-red-600 hover:bg-red-700 text-white font-black italic uppercase px-10 py-5 rounded-2xl shadow-xl border-b-4 border-red-800 transition-all active:scale-95">
            + NUEVO USUARIO
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-[#0d0d0d] rounded-3xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50">
                        <th class="p-8 text-zinc-500 text-[10px] font-black uppercase tracking-widest">Nombre del Operativo</th>
                        <th class="p-8 text-zinc-500 text-[10px] font-black uppercase tracking-widest text-center">Usuario</th>
                        <th class="p-8 text-zinc-500 text-[10px] font-black uppercase tracking-widest text-center">Rango</th>
                        <th class="p-8 text-zinc-500 text-[10px] font-black uppercase tracking-widest text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($usuarios as $user)
                    <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition-all cursor-pointer"
                        onclick="showUserOptions('{{ $user->id }}', '{{ $user->nombre }}', '{{ $user->username }}')">
                        <td class="p-8">
                            <span class="modo-hibrido-texto font-black italic uppercase text-xl group-hover:text-red-600 transition-colors">
                                {{ $user->nombre }}
                            </span>
                        </td>
                        <td class="p-8 text-center">
                            <span class="text-blue-600 dark:text-blue-400 font-mono font-bold">{{ $user->username }}</span>
                        </td>
                        <td class="p-8 text-center">
                            <span class="text-zinc-600 dark:text-zinc-400 font-black text-[10px] uppercase italic bg-zinc-100 dark:bg-zinc-800 px-3 py-1 rounded">
                                {{ $user->rol }}
                            </span>
                        </td>
                        <td class="p-8 text-right">
                            <div class="inline-block px-4 py-2 bg-red-600/10 text-red-600 rounded-lg font-black text-[10px] uppercase italic border border-red-600/20 group-hover:bg-red-600 group-hover:text-white transition-all">
                                GESTIONAR
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de Registro (Alpine) --}}
    <div x-show="openCreate" x-cloak class="fixed inset-0 z-[500] flex items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#0d0d0d] w-full max-w-md rounded-[2rem] p-10 shadow-2xl border border-zinc-200 dark:border-zinc-800" @click.away="openCreate = false">
            <h2 class="modo-hibrido-texto text-2xl font-black italic uppercase mb-8">NUEVO <span class="text-red-600">USUARIO</span></h2>
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="nombre" placeholder="NOMBRE COMPLETO" required class="w-full bg-zinc-100 dark:bg-black p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 modo-hibrido-texto font-bold">
                <input type="text" name="username" placeholder="USERNAME" required class="w-full bg-zinc-100 dark:bg-black p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 modo-hibrido-texto font-bold">
                <select name="rol" class="w-full bg-zinc-100 dark:bg-black p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 modo-hibrido-texto font-bold">
                    <option value="cajero">CAJERO</option>
                    <option value="admin">ADMINISTRADOR</option>
                </select>
                <input type="password" name="password" placeholder="CONTRASEÑA" required class="w-full bg-zinc-100 dark:bg-black p-4 rounded-xl border border-red-900/30 modo-hibrido-texto font-bold">
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="openCreate = false" class="flex-1 p-4 text-zinc-500 font-black uppercase text-xs">CANCELAR</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white p-4 rounded-xl font-black uppercase text-xs shadow-lg">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showUserOptions(id, nombre, username) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'GESTIÓN DE OPERATIVO',
            text: `¿Qué deseas hacer con ${nombre}?`,
            background: isDark ? '#0d0d0d' : '#ffffff',
            color: isDark ? '#ffffff' : '#09090b',
            showConfirmButton: false,
            showCloseButton: true,
            footer: `
                <div class="flex flex-col w-full gap-3 p-2">
                    <button onclick="Swal.close(); openEditModal('${id}', '${nombre}', '${username}')" 
                            class="w-full bg-blue-600 text-white font-black py-4 rounded-xl text-xs uppercase italic">
                        EDITAR PERFIL
                    </button>
                    <button onclick="Swal.close(); confirmarEliminar('${id}')" 
                            class="w-full border-2 border-red-600 text-red-600 font-black py-4 rounded-xl text-xs uppercase italic">
                        ELIMINAR ACCESO
                    </button>
                </div>
            `
        });
    }

    function openEditModal(id, nombre, username) {
        // Aquí puedes usar la misma lógica de antes para mostrar el modal de edición
        // o redirigir a una ruta de edición si lo prefieres
        Swal.fire({
            title: 'REDIRIGIENDO...',
            timer: 500,
            showConfirmButton: false,
            willClose: () => {
                window.location.href = `/admin/usuarios/${id}/edit`;
            }
        });
    }

    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿ESTÁS SEGURO?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'SÍ, BORRAR',
            cancelButtonText: 'CANCELAR',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/usuarios/${id}`;
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
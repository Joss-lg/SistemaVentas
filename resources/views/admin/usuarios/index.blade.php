@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    [x-cloak] { display: none !important; }
    .text-hibrido { color: #09090b !important; }
    .dark .text-hibrido { color: #ffffff !important; }
    .text-hibrido-muted { color: #52525b !important; }
    .dark .text-hibrido-muted { color: #a1a1aa !important; }
</style>

{{-- Escuchamos el evento 'abrir-modal-editar' que mandaremos desde el JS --}}
<div x-data="{ 
    openCreate: false, 
    openEdit: false,
    userEdit: { id: '', nombre: '', username: '', rol: '' }
}" 
@abrir-modal-editar.window="userEdit = $event.detail; openEdit = true"
class="w-full p-8 lg:p-12">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
        <div>
            <h1 class="text-hibrido text-5xl font-black italic uppercase tracking-tighter leading-none transition-colors">
                GESTIÓN DE <br> <span class="text-red-600">PERSONAL</span>
            </h1>
        </div>
        <button @click="openCreate = true" 
            class="bg-red-600 hover:bg-red-700 text-white font-black italic uppercase px-12 py-6 rounded-2xl shadow-xl active:scale-95 border-b-4 border-red-900 transition-all">
            + NUEVO USUARIO
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-[#0d0d0d] rounded-[2.5rem] border border-zinc-200 dark:border-white/5 overflow-hidden shadow-2xl">
        <div class="h-2 w-full bg-gradient-to-r from-red-600 via-red-900 to-black"></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em] bg-zinc-50 dark:bg-white/[0.02]">
                        <th class="p-8 italic">Operativo</th>
                        <th class="p-8 text-center italic">Username</th>
                        <th class="p-8 text-center italic">Rango</th>
                        <th class="p-8 text-right italic">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @foreach($usuarios as $user)
                    <tr class="group hover:bg-zinc-50 dark:hover:bg-white/[0.03] cursor-pointer transition-all"
                        onclick="abrirPanelGestion('{{ $user->id }}', '{{ $user->nombre }}', '{{ $user->username }}', '{{ $user->rol }}')">
                        <td class="p-8 text-hibrido font-black italic uppercase text-2xl group-hover:text-red-600">{{ $user->nombre }}</td>
                        <td class="p-8 text-center text-blue-600 dark:text-blue-400 font-mono font-bold">{{ $user->username }}</td>
                        <td class="p-8 text-center">
                            <span class="px-4 py-1.5 rounded-full font-black text-[10px] uppercase {{ $user->rol == 'admin' ? 'bg-red-600 text-white' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600' }}">
                                {{ $user->rol }}
                            </span>
                        </td>
                        <td class="p-8 text-right">
                            <div class="inline-block px-6 py-3 bg-zinc-100 dark:bg-zinc-900 text-zinc-500 rounded-xl font-black text-[10px] uppercase italic border border-zinc-200 dark:border-white/5 group-hover:border-red-600 group-hover:text-red-600">
                                GESTIONAR
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREAR --}}
    <div x-show="openCreate" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-md">
        <div class="bg-white dark:bg-[#0d0d0d] w-full max-w-md rounded-[3rem] p-10 shadow-2xl border border-zinc-200 dark:border-white/10" @click.away="openCreate = false">
            <h2 class="text-hibrido text-2xl font-black italic uppercase mb-8">NUEVO <span class="text-red-600">USUARIO</span></h2>
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="nombre" placeholder="NOMBRE COMPLETO" required class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="username" placeholder="USER" required class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                    <select name="rol" class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                        <option value="cajero">CAJERO</option>
                        <option value="admin">ADMIN</option>
                    </select>
                </div>
                <input type="password" name="password" placeholder="PASSWORD" required class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="openCreate = false" class="flex-1 text-zinc-500 font-black uppercase text-xs">Cerrar</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white p-5 rounded-2xl font-black uppercase shadow-lg border-b-4 border-red-900">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-md">
        <div class="bg-white dark:bg-[#0d0d0d] w-full max-w-md rounded-[3rem] p-10 shadow-2xl border border-zinc-200 dark:border-white/10" @click.away="openEdit = false">
            <h2 class="text-hibrido text-2xl font-black italic uppercase mb-8">EDITAR <span class="text-blue-600">OPERATIVO</span></h2>
            <form :action="'/admin/usuarios/' + userEdit.id" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <input type="text" name="nombre" x-model="userEdit.nombre" required class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="username" x-model="userEdit.username" required class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                    <select name="rol" x-model="userEdit.rol" class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                        <option value="cajero">CAJERO</option>
                        <option value="admin">ADMIN</option>
                    </select>
                </div>
                <input type="password" name="password" placeholder="NUEVA PASSWORD (OPCIONAL)" class="w-full bg-zinc-100 dark:bg-black p-5 rounded-2xl text-hibrido font-black border border-zinc-200 dark:border-white/10 outline-none">
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="openEdit = false" class="flex-1 text-zinc-500 font-black uppercase text-xs">Cancelar</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white p-5 rounded-2xl font-black uppercase shadow-lg border-b-4 border-blue-900">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function abrirPanelGestion(id, nombre, username, rol) {
        const isDark = document.documentElement.classList.contains('dark');
        
        Swal.fire({
            title: 'GESTIÓN DE OPERATIVO',
            html: `<p class="text-red-600 font-black text-xl italic uppercase">${nombre}</p>`,
            background: isDark ? '#0d0d0d' : '#ffffff',
            color: isDark ? '#ffffff' : '#09090b',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: { popup: 'rounded-[2.5rem] border-2 border-zinc-200 dark:border-white/10 shadow-2xl p-8' },
            footer: `
                <div class="flex flex-col w-full gap-3 p-4">
                    <button onclick="dispararEdicion('${id}', '${nombre}', '${username}', '${rol}')" 
                            class="w-full bg-blue-600 text-white font-black py-5 rounded-2xl text-[10px] uppercase italic border-b-4 border-blue-800">
                        EDITAR PERFIL
                    </button>
                    ${id != '{{ auth()->id() }}' ? `
                    <button onclick="confirmarBaja('${id}')" 
                            class="w-full bg-transparent border-2 border-red-600 text-red-600 font-black py-5 rounded-2xl text-[10px] uppercase italic">
                        DAR DE BAJA
                    </button>` : ''}
                </div>
            `
        });
    }

    function dispararEdicion(id, nombre, username, rol) {
        Swal.close();
        // Mandamos el evento que Alpine está escuchando
        window.dispatchEvent(new CustomEvent('abrir-modal-editar', { 
            detail: { id, nombre, username, rol } 
        }));
    }

    function confirmarBaja(id) {
        Swal.fire({
            title: '¿BORRAR ACCESO?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'SÍ, BORRAR',
            background: document.documentElement.classList.contains('dark') ? '#0d0d0d' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#09090b'
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
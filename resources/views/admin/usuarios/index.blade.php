@extends('layouts.admin')

@section('title', 'Gestión de Personal')

@section('content')
<div class="w-full transition-colors duration-300">
    <header class="mb-10">
        <h1 class="text-zinc-900 dark:text-white text-5xl font-black italic uppercase tracking-tighter">
            GESTIÓN DE <span class="text-red-600">PERSONAL</span>
        </h1>
        <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-[0.5em] mt-2 ml-1">Panel de Control de Usuarios y Accesos</p>
    </header>

    <div class="grid grid-cols-12 gap-8">
        {{-- Formulario de Registro --}}
        <div class="col-span-12 lg:col-span-4 bg-white dark:bg-[#0d0d0d] p-8 rounded-3xl border border-zinc-200 dark:border-white/5 shadow-2xl h-fit transition-colors">
            <h3 class="text-zinc-400 dark:text-gray-400 font-black uppercase text-[11px] tracking-widest mb-8 flex items-center gap-2">
                <i class="fas fa-user-plus text-red-600"></i> Registrar Nuevo Usuario
            </h3>
            
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-zinc-500 text-[10px] font-black uppercase mb-2 ml-1 tracking-widest">Nombre del Empleado</label>
                    <input type="text" name="nombre" placeholder="Nombre completo" required
                           class="w-full bg-zinc-50 dark:bg-black border border-zinc-200 dark:border-white/10 p-5 rounded-2xl text-zinc-900 dark:text-white font-bold outline-none focus:border-red-600 transition-all text-sm uppercase">
                </div>
                <div>
                    <label class="block text-zinc-500 text-[10px] font-black uppercase mb-2 ml-1 tracking-widest">Usuario Acceso</label>
                    <input type="text" name="username" placeholder="Username" required
                           class="w-full bg-zinc-50 dark:bg-black border border-zinc-200 dark:border-white/10 p-5 rounded-2xl text-zinc-900 dark:text-white font-bold outline-none focus:border-red-600 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-zinc-500 text-[10px] font-black uppercase mb-2 ml-1 tracking-widest">Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full bg-zinc-50 dark:bg-black border border-zinc-200 dark:border-white/10 p-5 rounded-2xl text-zinc-900 dark:text-white font-bold outline-none focus:border-red-600 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-zinc-500 text-[10px] font-black uppercase mb-2 ml-1 tracking-widest">Privilegios</label>
                    <div class="relative">
                        <select name="rol" class="w-full bg-zinc-50 dark:bg-black border border-zinc-200 dark:border-white/10 p-5 rounded-2xl text-zinc-900 dark:text-white font-bold outline-none cursor-pointer appearance-none transition-all focus:border-red-600">
                            <option value="cajero">Cajero</option>
                            <option value="admin">Administrador Total</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"></i>
                    </div>
                </div>

                <button type="submit" class="w-full bg-red-600 p-6 rounded-2xl text-white font-black italic uppercase tracking-[0.2em] hover:bg-red-700 shadow-xl shadow-red-900/20 transition-all text-xs transform hover:-translate-y-1">
                    Crear Cuenta de Acceso
                </button>
            </form>
        </div>

        {{-- Tabla de Personal --}}
        <div class="col-span-12 lg:col-span-8 bg-white dark:bg-[#0d0d0d] p-8 rounded-3xl border border-zinc-200 dark:border-white/5 shadow-2xl relative overflow-hidden transition-colors">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-600 to-blue-600"></div>
            <h3 class="text-zinc-400 dark:text-gray-400 font-black uppercase text-[11px] tracking-widest mb-10">Lista de Personal Activo</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-zinc-400 dark:text-gray-600 text-[10px] font-black uppercase tracking-widest border-b border-zinc-100 dark:border-white/5">
                            <th class="pb-5 text-left">Empleado</th>
                            <th class="pb-5 text-center">Usuario</th>
                            <th class="pb-5 text-center">Rol</th>
                            <th class="pb-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                        @foreach($usuarios as $user)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-white/[0.02] transition-all">
                            <td class="py-6">
                                <p class="text-zinc-900 dark:text-white font-black italic uppercase text-lg group-hover:text-red-600 transition-colors leading-none tracking-tighter">{{ $user->nombre }}</p>
                                <span class="text-[9px] text-zinc-400 dark:text-zinc-600 font-bold uppercase tracking-widest mt-1 inline-block">ID #{{ $user->id }} • En línea</span>
                            </td>
                            <td class="py-6 text-center">
                                <span class="text-blue-600 dark:text-blue-500 font-mono font-bold text-xs bg-blue-50 dark:bg-blue-900/10 px-3 py-1 rounded-lg">
                                    {{ $user->username }}
                                </span>
                            </td>
                            <td class="py-6 text-center">
                                <span class="px-4 py-2 rounded-xl {{ $user->rol == 'admin' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-500 border-red-200 dark:border-red-600/30' : 'bg-zinc-100 dark:bg-white/5 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-white/10' }} border text-[10px] font-black uppercase italic tracking-widest">
                                    {{ $user->rol }}
                                </span>
                            </td>
                            <td class="py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" 
                                            onclick="openEditModal('{{ $user->id }}', '{{ $user->nombre }}', '{{ $user->username }}', '{{ $user->rol }}')"
                                            class="bg-zinc-100 dark:bg-zinc-800 hover:bg-blue-600 text-zinc-500 dark:text-zinc-400 hover:text-white p-3 rounded-xl transition-all shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    @if(auth()->user()->id != $user->id)
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmarEliminar({{ $user->id }})" 
                                                    class="bg-zinc-100 dark:bg-zinc-800 hover:bg-red-600 text-zinc-500 dark:text-zinc-400 hover:text-white p-3 rounded-xl transition-all shadow-sm">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Edición Mejorado --}}
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-zinc-950/80 dark:bg-black/90 backdrop-blur-md transition-opacity"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/10 w-full max-w-md p-10 rounded-[2.5rem] shadow-2xl transform transition-all">
            <h2 class="text-4xl font-black italic uppercase text-zinc-900 dark:text-white mb-8 tracking-tighter">
                EDITAR <span class="text-blue-600">PERFIL</span>
            </h2>

            <form id="editForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                        <input type="text" id="edit_nombre" name="nombre" required
                               class="w-full bg-zinc-50 dark:bg-black border border-zinc-200 dark:border-white/10 rounded-2xl py-4 px-6 text-zinc-900 dark:text-white font-bold outline-none focus:border-blue-600 transition-all uppercase">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Usuario de Acceso</label>
                        <input type="text" id="edit_username" name="username" required
                               class="w-full bg-zinc-50 dark:bg-black border border-zinc-200 dark:border-white/10 rounded-2xl py-4 px-6 text-zinc-900 dark:text-white font-bold outline-none focus:border-blue-600 transition-all">
                    </div>

                    <div class="pt-4 border-t border-zinc-100 dark:border-white/5">
                        <label class="block text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2 ml-1">Cambiar Contraseña (opcional)</label>
                        <input type="password" name="password" placeholder="••••••••"
                               class="w-full bg-zinc-50 dark:bg-black border border-zinc-200 dark:border-white/10 rounded-2xl py-4 px-6 text-zinc-900 dark:text-white font-bold outline-none focus:border-orange-500 transition-all">
                    </div>
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="closeEditModal()" 
                            class="flex-1 bg-zinc-100 dark:bg-zinc-900 text-zinc-500 dark:text-gray-400 font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.2em] hover:text-zinc-900 dark:hover:text-white transition-all">
                        Cerrar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-blue-900/30 hover:bg-blue-500 transition-all transform hover:scale-105">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, nombre, username, rol) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        form.action = `/admin/usuarios/${id}`; 
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_username').value = username;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmarEliminar(id) {
        const isDark = document.documentElement.classList.contains('dark');
        
        Swal.fire({
            title:'¿BORRAR AL CAJERO?',
            text: "No podrá volver a entrar al sistema y sus ventas se quedarán sin cajero.",
            icon: 'warning',
            showCancelButton: true,
            background: isDark ? '#0d0d0d' : '#ffffff',
            color: isDark ? '#ffffff' : '#18181b',
            confirmButtonColor: '#dc2626', // Rojo más intenso
            cancelButtonColor: isDark ? '#27272a' : '#e4e4e7', // Zinc rudo
            confirmButtonText: 'SÍ',
            cancelButtonText: 'MEJOR NO',
            reverseButtons: true, // Pone el cancelar a la izquierda (estándar de UX)
            customClass: {
            popup: 'rounded-[2.5rem] border border-zinc-200 dark:border-white/10 shadow-2xl',
            title: 'font-black italic uppercase tracking-tighter text-3xl',
            confirmButton: 'rounded-xl font-black italic uppercase text-xs px-6 py-4 shadow-lg shadow-red-900/20',
            cancelButton: 'rounded-xl font-black italic uppercase text-xs px-6 py-4 text-zinc-500 dark:text-zinc-400'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeEditModal(); });
</script>
@endsection
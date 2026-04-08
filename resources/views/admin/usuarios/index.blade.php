@extends('layouts.admin')

@section('content')
<div class="w-full">
    <header class="mb-10">
        <h1 class="text-white text-5xl font-black italic uppercase tracking-tighter">
            GESTIÓN DE <span class="text-red-600">PERSONAL</span>
        </h1>
        <p class="text-gray-500 font-bold uppercase text-[10px] tracking-[0.5em] mt-2">Panel de Control de Usuarios</p>
    </header>

    <div class="grid grid-cols-12 gap-10">
        <div class="col-span-4 bg-[#0d0d0d] p-10 rounded-2xl border border-white/5 shadow-2xl h-fit">
            <h3 class="text-gray-400 font-black uppercase text-[11px] tracking-widest mb-10">Registrar Nuevo Cajero</h3>
            
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-8">
                @csrf
                <div>
                    <label class="block text-gray-600 text-[10px] font-black uppercase mb-3">Nombre del Empleado</label>
                    <input type="text" name="nombre" placeholder="Nombre completo" required
                           class="w-full bg-black border border-white/10 p-5 rounded-xl text-white font-bold outline-none focus:border-red-600 transition-all text-sm uppercase">
                </div>
                <div>
                    <label class="block text-gray-600 text-[10px] font-black uppercase mb-3">Usuario Acceso</label>
                    <input type="text" name="username" placeholder="username" required
                           class="w-full bg-black border border-white/10 p-5 rounded-xl text-white font-bold outline-none focus:border-red-600 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-gray-600 text-[10px] font-black uppercase mb-3">Contraseña</label>
                    <input type="password" name="password" required
                           class="w-full bg-black border border-white/10 p-5 rounded-xl text-white font-bold outline-none focus:border-red-600 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-gray-600 text-[10px] font-black uppercase mb-3">Privilegios</label>
                    <select name="rol" class="w-full bg-black border border-white/10 p-5 rounded-xl text-white font-bold outline-none cursor-pointer appearance-none">
                        <option value="cajero">Cajero</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-red-600 p-6 rounded-xl text-white font-black italic uppercase tracking-widest hover:bg-red-700 shadow-[0_10px_30px_rgba(220,38,38,0.3)] transition-all text-sm">
                    Crear Usuario
                </button>
            </form>
        </div>

        <div class="col-span-8 bg-[#0d0d0d] p-10 rounded-2xl border border-white/5 shadow-2xl relative overflow-hidden">
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-600/40"></div>
            <h3 class="text-gray-400 font-black uppercase text-[11px] tracking-widest mb-10">Lista de Personal</h3>
            
            <table class="w-full">
                <thead>
                    <tr class="text-gray-600 text-[10px] font-black uppercase tracking-widest border-b border-white/5">
                        <th class="pb-5 text-left">Empleado</th>
                        <th class="pb-5 text-center">Usuario</th>
                        <th class="pb-5 text-center">Rol</th>
                        <th class="pb-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($usuarios as $user)
                    <tr class="group hover:bg-white/[0.02] transition-all">
                        <td class="py-6">
                            <p class="text-white font-black italic uppercase text-lg group-hover:text-red-500 transition-colors leading-none">{{ $user->nombre }}</p>
                            <span class="text-[9px] text-gray-600 font-bold uppercase tracking-widest">Activo</span>
                        </td>
                        <td class="py-6 text-center text-blue-500 font-mono font-bold text-sm">
                            {{ $user->username }}
                        </td>
                        <td class="py-6 text-center">
                            <span class="px-5 py-2 rounded-full {{ $user->rol == 'admin' ? 'bg-red-900/20 border-red-600/30 text-red-500' : 'bg-blue-900/20 border-blue-600/30 text-blue-500' }} border text-[10px] font-black uppercase">
                                {{ $user->rol }}
                            </span>
                        </td>
                        <td class="py-6 text-right">
                            <div class="flex justify-end gap-3">
                                <button type="button" 
                                        onclick="openEditModal('{{ $user->id }}', '{{ $user->nombre }}', '{{ $user->username }}', '{{ $user->rol }}')"
                                        class="bg-zinc-800 hover:bg-white text-white hover:text-black p-3 rounded-lg transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                @if(auth()->user()->id != $user->id)
                            <form id="delete-form-{{ $user->id }}" action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmarEliminar({{ $user->id }})" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg transition-all">
                                    <i class="fas fa-trash"></i> Eliminar
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

<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/90 backdrop-blur-sm"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-[#0d0d0d] border border-white/10 w-full max-w-md p-10 rounded-3xl shadow-2xl">
            <h2 class="text-3xl font-black italic uppercase text-white mb-8 tracking-tighter">
                Editar <span class="text-blue-500">Perfil</span>
            </h2>

            <form id="editForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Nombre Completo</label>
                    <input type="text" id="edit_nombre" name="nombre" required
                           class="w-full bg-black border border-white/10 rounded-xl py-4 px-5 text-white font-bold outline-none focus:border-blue-500 transition-all uppercase">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Usuario de Acceso</label>
                    <input type="text" id="edit_username" name="username" required
                           class="w-full bg-black border border-white/10 rounded-xl py-4 px-5 text-white font-bold outline-none focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-orange-500 uppercase tracking-widest mb-3">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" placeholder="••••••••"
                           class="w-full bg-black border border-white/10 rounded-xl py-4 px-5 text-white font-bold outline-none focus:border-orange-500 transition-all">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditModal()" 
                            class="flex-1 bg-zinc-900 text-gray-400 font-black py-5 rounded-xl uppercase text-xs tracking-widest hover:text-white transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white font-black py-5 rounded-xl uppercase text-xs tracking-widest shadow-lg shadow-blue-900/20 hover:bg-blue-500 transition-all">
                        Guardar Cambios
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
    Swal.fire({
        title: '¿ESTÁS SEGURO DE ELIMINAR AL USUARIO?',
        text: "¡Si borras a este usuario ya no podrá entrar al sistema!",
        icon: 'warning',
        showCancelButton: true,
        background: '#111827',
        color: '#fff',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'SÍ,',
        cancelButtonText: 'CANCELAR'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeEditModal();
    });
</script>
@endsection
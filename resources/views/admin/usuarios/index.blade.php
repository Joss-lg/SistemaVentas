@extends('layouts.cajero')
@section('title', 'Gestión de Usuarios')
@section('content')

<style>
    [x-cloak] { display: none !important; }
    .text-hibrido { color: #09090b !important; }
    .dark .text-hibrido { color: #ffffff !important; }
    .text-hibrido-muted { color: #52525b !important; }
    .dark .text-hibrido-muted { color: #a1a1aa !important; }
</style>

<div id="usuarios-app"
    x-data="{ openCreate: false, openEdit: false, userEdit: { id: '', nombre: '', username: '', rol: '', permisos: [] } }"
    @abrir-modal-editar.window="userEdit = $event.detail; openEdit = true"
    class="w-full space-y-8 p-4 md:p-8 transition-colors duration-300"
    data-csrf="{{ csrf_token() }}"
    data-user-id-actual="{{ auth()->id() }}"
>
    {{-- Header Corregido --}}
    <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-zinc-200 dark:border-white/5 pb-6">
        <div>
            <h2 class="text-4xl md:text-5xl font-black italic tracking-tighter uppercase text-zinc-900 dark:text-white">
                GESTIÓN DE <span class="text-red-600">PERSONAL</span>
            </h2>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mt-1">
                CONTROL DE USUARIOS Y PERMISOS DEL SISTEMA
            </p>
        </div>
        <button @click="openCreate = true" class="px-6 py-3.5 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-black italic uppercase tracking-wider text-xs rounded-2xl shadow-lg transition-all inline-flex items-center gap-2 border-b-2 border-red-900 shrink-0">
            <i class="fas fa-plus text-xs"></i> NUEVO USUARIO
        </button>
    </div>

    {{-- Tabla Full Width --}}
    <div class="w-full bg-white dark:bg-[#0d0d0d] rounded-3xl border border-zinc-200 dark:border-white/5 overflow-hidden shadow-2xl">
        <div class="h-1.5 w-full bg-gradient-to-r from-red-600 via-red-900 to-black"></div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-[#0d0d0d] text-zinc-400 dark:text-zinc-500 uppercase text-[9px] tracking-[0.2em] border-b border-zinc-100 dark:border-white/5">
                        <th class="p-5 pl-8 font-black">Operativo</th>
                        <th class="p-5 text-center font-black">Username</th>
                        <th class="p-5 text-center font-black">Rango</th>
                        <th class="p-5 pr-8 text-right font-black">Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios" class="divide-y divide-zinc-100 dark:divide-white/5">
                    @foreach($usuarios as $user)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-white/5 cursor-pointer transition-colors"
                            data-usuario
                            data-id="{{ $user->id }}"
                            data-nombre="{{ $user->nombre }}"
                            data-username="{{ $user->username }}"
                            data-rol="{{ $user->rol }}"
                            data-permisos="{{ json_encode($user->permissions->pluck('slug')) }}"
                        >
                            <td class="p-5 pl-8 font-black italic text-xl uppercase text-zinc-900 dark:text-white group-hover:text-red-600 transition-colors">
                                {{ $user->nombre }}
                            </td>
                            <td class="p-5 text-center text-xs font-mono font-bold tracking-wider text-zinc-500 dark:text-zinc-400 uppercase">
                                {{ $user->username }}
                            </td>
                            <td class="p-5 text-center">
                                <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border border-zinc-200 dark:border-white/5 {{ strtolower($user->rol) == 'admin' ? 'bg-red-600/10 text-red-600 border-red-500/20' : 'bg-zinc-100 dark:bg-white/5 text-zinc-600 dark:text-zinc-400' }}">
                                    {{ $user->rol }}
                                </span>
                            </td>
                            <td class="p-5 pr-8 text-right">
                                <div class="inline-block px-5 py-2 bg-zinc-100 dark:bg-zinc-800/80 text-zinc-400 dark:text-zinc-300 rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors group-hover:bg-red-600 group-hover:text-white">
                                    GESTIONAR
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('components.usuarios.modal-crear')
    @include('components.usuarios.modal-editar')
</div>

@endsection
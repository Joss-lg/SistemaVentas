function getConfig() {
    const app = document.getElementById('usuarios-app');
    return app ? app.dataset : {};
}

function abrirPanelGestion(id, nombre, username, rol, permisos) {
    const isDark = document.documentElement.classList.contains('dark');
    const permisosJson = JSON.stringify(permisos).replace(/"/g, '&quot;');

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
                <button data-editar-usuario='${id}|${nombre}|${username}|${rol}|${permisosJson}'
                    class="w-full bg-blue-600 text-white font-black py-5 rounded-2xl text-[10px] uppercase italic border-b-4 border-blue-800">
                    EDITAR PERFIL
                </button>
                ${id != getConfig().userIdActual ? `
                <button data-baja-usuario="${id}"
                    class="w-full bg-transparent border-2 border-red-600 text-red-600 font-black py-5 rounded-2xl text-[10px] uppercase italic">
                    DAR DE BAJA
                </button>` : ''}
            </div>
        `
    });
}

function dispararEdicion(id, nombre, username, rol, permisosJson) {
    Swal.close();

    let listaPermisos = [];
    try {
        listaPermisos = JSON.parse(permisosJson.replace(/&quot;/g, '"'));
    } catch (e) {
        console.error('Error al parsear permisos:', e);
    }

    window.dispatchEvent(new CustomEvent('abrir-modal-editar', {
        detail: { id, nombre, username, rol, permisos: listaPermisos }
    }));
}

function confirmarBaja(id) {
    const isDark = document.documentElement.classList.contains('dark');

    Swal.fire({
        title: '¿BORRAR ACCESO?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'SÍ, BORRAR',
        background: isDark ? '#0d0d0d' : '#ffffff',
        color: isDark ? '#ffffff' : '#09090b'
    }).then((result) => {
        if (!result.isConfirmed) return;

        const { csrf } = getConfig();
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/usuarios/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrf}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    });
}

export function initGestionUsuarios() {
    const tabla = document.getElementById('tabla-usuarios');
    if (!tabla) return; // solo corre en esta vista

    // Delegación: click en una fila abre el panel de gestión
    tabla.addEventListener('click', (e) => {
        const fila = e.target.closest('[data-usuario]');
        if (!fila) return;

        const { id, nombre, username, rol, permisos } = fila.dataset;
        abrirPanelGestion(id, nombre, username, rol, JSON.parse(permisos));
    });

    // Delegación global: los botones de SweetAlert se inyectan fuera de #tabla-usuarios
    document.addEventListener('click', (e) => {
        const btnEditar = e.target.closest('[data-editar-usuario]');
        if (btnEditar) {
            const [id, nombre, username, rol, permisosJson] = btnEditar.dataset.editarUsuario.split('|');
            dispararEdicion(id, nombre, username, rol, permisosJson);
            return;
        }

        const btnBaja = e.target.closest('[data-baja-usuario]');
        if (btnBaja) {
            confirmarBaja(btnBaja.dataset.bajaUsuario);
        }
    });
}
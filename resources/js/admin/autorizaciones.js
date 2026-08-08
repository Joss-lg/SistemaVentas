export function initAutorizaciones() {
    const modal = document.getElementById('modalSolicitudes');

    window.abrirModalSolicitudes = function () {
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    };

    window.cerrarModalSolicitudes = function () {
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    };

    const rutaJson = document.body.dataset.rutaAutorizacionesJson;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!rutaJson) return; // esta vista no tiene el bloque de autorizaciones

    async function refrescar() {
        try {
            const res = await fetch(rutaJson);
            const data = await res.json();
            renderBadge(data.count);
            renderModal(data.solicitudes);
        } catch (e) {
            console.error('Error consultando autorizaciones pendientes:', e);
        }
    }

    function renderBadge(count) {
        let btn = document.getElementById('btnSolicitudesPendientes');

        if (count === 0) {
            btn?.remove();
            return;
        }

        if (!btn) {
            // Crea el botón si no existía (primera solicitud que llega)
            const contenedor = document.querySelector('.flex.flex-col.sm\\:flex-row.sm\\:items-center.justify-between');
            if (!contenedor) return;
            btn = document.createElement('button');
            btn.id = 'btnSolicitudesPendientes';
            btn.onclick = window.abrirModalSolicitudes;
            btn.className = 'relative inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-500 text-white font-black italic px-5 py-3 rounded-2xl uppercase text-xs tracking-wider transition-all shadow-lg shadow-red-600/30 cursor-pointer animate-pulse';
            contenedor.appendChild(btn);
        }

        btn.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <span>Solicitudes Pendientes</span>
            <span class="bg-white text-red-600 font-extrabold text-[10px] px-2 py-0.5 rounded-full ml-1">${count}</span>
        `;
    }

    function renderModal(solicitudes) {
        const cuerpo = modal?.querySelector('.grid.grid-cols-1.md\\:grid-cols-2');
        if (!cuerpo) return;

        if (solicitudes.length === 0) {
            cuerpo.innerHTML = `<p class="col-span-2 text-center text-zinc-400 text-xs font-bold uppercase p-8">Sin solicitudes pendientes.</p>`;
            return;
        }

        cuerpo.innerHTML = solicitudes.map(s => `
            <div class="bg-zinc-50 dark:bg-black/60 border border-zinc-200 dark:border-white/10 p-5 rounded-2xl flex flex-col justify-between space-y-4 shadow-md">
                <div class="space-y-3">
                    <div class="border-b border-zinc-200 dark:border-white/5 pb-2">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest block">Solicitante</span>
                        <span class="text-xs font-black text-zinc-900 dark:text-white uppercase">${s.solicitante}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white dark:bg-black/40 p-2.5 rounded-xl border border-zinc-200 dark:border-white/5">
                            <p class="text-[8px] font-black text-zinc-400 uppercase">Esperado</p>
                            <p class="font-black italic text-zinc-800 dark:text-zinc-200">$${s.esperado}</p>
                        </div>
                        <div class="bg-white dark:bg-black/40 p-2.5 rounded-xl border border-zinc-200 dark:border-white/5">
                            <p class="text-[8px] font-black text-zinc-400 uppercase">Declarado</p>
                            <p class="font-black italic text-emerald-600 dark:text-emerald-500">$${s.declarado}</p>
                        </div>
                    </div>
                    <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center justify-between text-red-600 dark:text-red-500">
                        <span class="text-[10px] font-black uppercase">Faltante:</span>
                        <span class="text-sm font-black italic">-$${s.faltante}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <form action="${s.ruta_aprobar}" method="POST" class="flex-1">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black italic py-2.5 rounded-xl uppercase text-[10px] tracking-wider transition-all shadow-md shadow-emerald-600/20 cursor-pointer flex items-center justify-center gap-1">
                            <i class="fas fa-check"></i> Autorizar
                        </button>
                    </form>
                    <form action="${s.ruta_rechazar}" method="POST" class="flex-1">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="w-full bg-zinc-800 hover:bg-zinc-700 text-red-400 font-black italic py-2.5 rounded-xl uppercase text-[10px] tracking-wider transition-all cursor-pointer flex items-center justify-center gap-1">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </form>
                </div>
            </div>
        `).join('');
    }

    refrescar(); // primera carga inmediata
    setInterval(refrescar, 5000);
}
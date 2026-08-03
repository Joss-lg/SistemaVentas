import { state } from './state.js';
import { notify } from '../shared/notify.js';
import { renderizarTabla } from './carrito.js';
import { cerrarModalRecuperar } from './modales.js';

export function pausarVenta() {
    if (state.carrito.length === 0) return;
    const pausadas = JSON.parse(localStorage.getItem('ventas_pausadas_local')) || [];
    pausadas.push({ id: Date.now(), fecha: new Date(), productos: state.carrito });
    localStorage.setItem('ventas_pausadas_local', JSON.stringify(pausadas));
    notify('success', 'EN ESPERA (LOCAL)');
    state.carrito = [];
    renderizarTabla();
}

export function abrirModalRecuperar() {
    const modal = document.getElementById('modalVentasEspera');
    const tbody = document.getElementById('listaVentasEspera');
    if (!modal || !tbody) return;

    modal.classList.remove('hidden');
    tbody.innerHTML = '';

    const pausadas = JSON.parse(localStorage.getItem('ventas_pausadas_local')) || [];
    if (pausadas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="2" class="p-10 text-center text-zinc-400 font-bold uppercase tracking-widest text-xs">No hay ventas en espera en este equipo.</td></tr>`;
        return;
    }

    pausadas.forEach(v => {
        tbody.innerHTML += `<tr class="border-b border-zinc-100 dark:border-white/5 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
            <td class="p-5">
                <div class="flex flex-col">
                    <span class="text-zinc-900 dark:text-white font-black italic uppercase text-lg leading-tight">${new Date(v.fecha).toLocaleString()}</span>
                    <span class="text-zinc-400 font-bold text-[10px] uppercase tracking-widest mt-1">ID LOCAL: #${v.id}</span>
                </div>
            </td>
            <td class="p-5 text-right">
                <button data-recuperar-local="${v.id}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-black uppercase text-xs italic transition-all active:scale-95 shadow-lg shadow-green-900/20">Seleccionar</button>
            </td>
        </tr>`;
    });
}

function recuperarLocal(id) {
    const pausadas = JSON.parse(localStorage.getItem('ventas_pausadas_local')) || [];
    const venta = pausadas.find(v => v.id === id);
    if (!venta) return;

    state.carrito = venta.productos;
    renderizarTabla();

    const nuevasPausadas = pausadas.filter(v => v.id !== id);
    localStorage.setItem('ventas_pausadas_local', JSON.stringify(nuevasPausadas));
    cerrarModalRecuperar();
}

export function initPausas() {
    document.getElementById('listaVentasEspera')?.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-recuperar-local]');
        if (btn) recuperarLocal(parseInt(btn.dataset.recuperarLocal));
    });

    document.getElementById('btn-pausar-venta')?.addEventListener('click', pausarVenta);
    document.getElementById('btn-abrir-recuperar')?.addEventListener('click', abrirModalRecuperar);
}
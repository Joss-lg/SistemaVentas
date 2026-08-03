import { getConfig } from './state.js';
import { buscarLocal } from './sync.js';
import { agregarAlCarrito } from './carrito.js';
import { cerrarModalBusqueda } from './modales.js';
import { notify } from '../shared/notify.js';

export function initBusqueda() {
    const scanner = document.getElementById('scanner');
    const inputNombre = document.getElementById('input-busqueda-nombre');

    scanner?.addEventListener('keypress', (e) => {
        if (e.key !== 'Enter') return;
        e.preventDefault();

        const codigo = scanner.value;
        if (!codigo) return;

        const { rutaBuscarProducto } = getConfig();

        if (!navigator.onLine) {
            const p = buscarLocal(codigo)[0];
            if (p) { agregarAlCarrito(p); scanner.value = ''; }
            else { notify('error', 'No encontrado (Offline)'); scanner.value = ''; }
            return;
        }

        fetch(`${rutaBuscarProducto}?codigo=${codigo}`)
            .then(res => res.json())
            .then(p => { agregarAlCarrito(p); scanner.value = ''; })
            .catch(() => {
                const p = buscarLocal(codigo)[0];
                if (p) agregarAlCarrito(p);
                else notify('error', 'Producto no encontrado');
                scanner.value = '';
            });
    });

    inputNombre?.addEventListener('input', function () {
        const q = this.value;
        if (q.length < 2) return;

        const render = (productos) => {
            let html = '';
            productos.forEach(p => {
                const pData = btoa(JSON.stringify(p));
                html += `<tr class="border-b border-white/5">
                    <td class="p-4 uppercase italic font-black">${p.descripcion}</td>
                    <td class="p-4 text-center text-green-500 font-black">$${parseFloat(p.precio_venta).toFixed(2)}</td>
                    <td class="p-4 text-right">
                        <button data-seleccionar-venta="${pData}" class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-black italic">Seleccionar</button>
                    </td>
                </tr>`;
            });
            document.getElementById('resultados-busqueda').innerHTML = html;
        };

        const { rutaBuscarNombre } = getConfig();
        if (!navigator.onLine) render(buscarLocal(q));
        else fetch(`${rutaBuscarNombre}?q=${q}`).then(res => res.json()).then(render).catch(() => render(buscarLocal(q)));
    });

    // Delegación: click en "Seleccionar" de resultados de búsqueda por nombre
    document.getElementById('resultados-busqueda')?.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-seleccionar-venta]');
        if (!btn) return;
        agregarAlCarrito(JSON.parse(atob(btn.dataset.seleccionarVenta)));
        cerrarModalBusqueda();
    });
}
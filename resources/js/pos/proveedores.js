import { state, getConfig } from './state.js';
import { notify } from '../shared/notify.js';
import { buscarLocal } from './sync.js';
import { cerrarModalProveedor } from './modales.js';

export function buscarProductoNombreProveedor(query) {
    const contenedor = document.getElementById('sugerencias-prov');
    if (query.length < 2) { contenedor.classList.add('hidden'); return; }

    const renderProv = (productos) => {
        let html = '';
        productos.forEach(p => {
            const pData = btoa(JSON.stringify(p));
            html += `<div data-seleccionar-prov="${pData}" class="p-4 border-b border-zinc-100 dark:border-white/5 hover:bg-red-600/10 dark:hover:bg-red-600/20 cursor-pointer transition-all">
                <p class="text-zinc-900 dark:text-white font-black text-xs uppercase italic">${p.descripcion}</p>
            </div>`;
        });
        contenedor.innerHTML = html;
        contenedor.classList.remove('hidden');
    };

    const { rutaBuscarNombre } = getConfig();
    if (!navigator.onLine) renderProv(buscarLocal(query));
    else fetch(`${rutaBuscarNombre}?q=${query}`).then(res => res.json()).then(renderProv).catch(() => renderProv(buscarLocal(query)));
}

function seleccionarProductoProv(dataBase64) {
    const p = JSON.parse(atob(dataBase64));
    state.productoProvSeleccionado = p;
    document.getElementById('prov-nombre-display').innerText = p.descripcion;
    document.getElementById('info-producto-prov').classList.remove('hidden');
    document.getElementById('sugerencias-prov').classList.add('hidden');
    document.getElementById('busqueda-prod-prov').value = '';
    document.getElementById('prov-cantidad').focus();
}

function agregarAListaTemporalProv() {
    const cant = parseFloat(document.getElementById('prov-cantidad').value);
    const costo = parseFloat(document.getElementById('prov-costo-total').value) || 0;

    if (!state.productoProvSeleccionado || isNaN(cant) || cant <= 0) {
        notify('warning', 'DATOS INVÁLIDOS');
        return;
    }

    state.listaTemporalProveedor.push({
        id: state.productoProvSeleccionado.id,
        descripcion: state.productoProvSeleccionado.descripcion,
        cantidad: cant,
        costo_unitario: costo,
        subtotal: cant * costo,
        unidad: state.productoProvSeleccionado.unidad_medida
    });

    renderListaProv();
    state.productoProvSeleccionado = null;
    document.getElementById('info-producto-prov').classList.add('hidden');
    document.getElementById('busqueda-prod-prov').focus();
}

function renderListaProv() {
    const tbody = document.getElementById('lista-items-prov');
    if (!tbody) return;

    tbody.innerHTML = '';
    state.listaTemporalProveedor.forEach((item, index) => {
        tbody.innerHTML += `<tr class="border-b border-zinc-100 dark:border-white/5">
            <td class="p-4 font-black text-zinc-900 dark:text-white">${item.cantidad}</td>
            <td class="p-4 uppercase italic font-bold text-zinc-700 dark:text-zinc-300">${item.descripcion}</td>
            <td class="p-4 text-right text-zinc-900 dark:text-white">$${item.costo_unitario.toFixed(2)}</td>
            <td class="p-4 text-right text-green-600 dark:text-green-400 font-black">$${item.subtotal.toFixed(2)}</td>
            <td class="p-4 text-center">
                <button data-eliminar-prov="${index}" class="text-zinc-400 hover:text-red-600 transition-colors text-xl">&times;</button>
            </td>
        </tr>`;
    });

    const contador = document.getElementById('contador-items-prov');
    if (contador) contador.innerText = `${state.listaTemporalProveedor.length} ARTÍCULOS`;
}

function eliminarItemProv(i) {
    state.listaTemporalProveedor.splice(i, 1);
    renderListaProv();
}

function guardarEntradaStock() {
    const proveedor = document.getElementById('prov-nombre').value.trim();
    if (state.listaTemporalProveedor.length === 0 || !proveedor) {
        notify('warning', 'PROVEEDOR O LISTA VACÍA');
        return;
    }

    const payload = { proveedor, productos: state.listaTemporalProveedor };
    const { rutaAgregarStock, csrf } = getConfig();

    if (!navigator.onLine) {
        const cola = JSON.parse(localStorage.getItem('cola_stock_offline')) || [];
        cola.push(payload);
        localStorage.setItem('cola_stock_offline', JSON.stringify(cola));
        notify('warning', 'STOCK GUARDADO OFFLINE');
        limpiarYSalirProv();
        return;
    }

    fetch(rutaAgregarStock, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(payload)
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                notify('success', 'MERCANCÍA REGISTRADA');
                limpiarYSalirProv();
                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(error => console.error('Error:', error));
}

function limpiarYSalirProv() {
    state.listaTemporalProveedor = [];
    const inputProv = document.getElementById('prov-nombre');
    if (inputProv) inputProv.value = '';
    renderListaProv();
    cerrarModalProveedor();
}

export function initProveedores() {
    document.getElementById('busqueda-prod-prov')?.addEventListener('input', function () {
        buscarProductoNombreProveedor(this.value);
    });

    document.getElementById('sugerencias-prov')?.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-seleccionar-prov]');
        if (btn) seleccionarProductoProv(btn.dataset.seleccionarProv);
    });

    document.getElementById('lista-items-prov')?.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-eliminar-prov]');
        if (btn) eliminarItemProv(parseInt(btn.dataset.eliminarProv));
    });

    document.getElementById('btn-agregar-lista-prov')?.addEventListener('click', agregarAListaTemporalProv);
    document.getElementById('btn-guardar-stock')?.addEventListener('click', guardarEntradaStock);
}
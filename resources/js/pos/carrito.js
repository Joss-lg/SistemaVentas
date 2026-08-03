import { state } from './state.js';

export function agregarAlCarrito(producto) {
    if (producto.unidad_medida === 'kg' || producto.unidad_medida === 'granel') {
        state.productoPendientePeso = producto;
        document.getElementById('peso-producto-nombre').innerText = producto.descripcion;
        document.getElementById('modal-peso').classList.remove('hidden');
        setTimeout(() => document.getElementById('input-peso-valor').focus(), 200);
    } else {
        ejecutarAgregado(producto, 1);
    }
}

export function confirmarPeso() {
    const peso = parseFloat(document.getElementById('input-peso-valor').value);
    if (peso > 0) {
        ejecutarAgregado(state.productoPendientePeso, peso);
        cerrarModalPeso();
    }
}

function ejecutarAgregado(p, cant) {
    const item = state.carrito.find(i => i.id === p.id);
    const precio = parseFloat(p.precio_venta);
    if (item && p.unidad_medida !== 'kg') {
        item.cantidad += cant;
        item.subtotal = item.cantidad * item.precio;
    } else {
        state.carrito.push({ id: p.id, descripcion: p.descripcion, precio, cantidad: cant, subtotal: precio * cant });
    }
    renderizarTabla();
}

export function renderizarTabla() {
    let html = '';
    state.totalVenta = 0;

    state.carrito.forEach((item, index) => {
        state.totalVenta += item.subtotal;
        html += `
        <tr class="border-b border-zinc-100 dark:border-white/5">
            <td class="p-6 font-black text-zinc-900 dark:text-white">${item.cantidad}</td>
            <td class="p-6 uppercase text-zinc-600 dark:text-gray-400 italic">${item.descripcion}</td>
            <td class="p-6 text-center text-green-600 dark:text-green-500 font-black">$${item.precio.toFixed(2)}</td>
            <td class="p-6 text-right text-red-600 dark:text-red-500 font-black">$${item.subtotal.toFixed(2)}</td>
            <td class="p-6 text-right w-20">
                <button data-eliminar-item="${index}" class="text-zinc-400 hover:text-red-600 dark:hover:text-red-500 transition-colors text-xl font-black">&times;</button>
            </td>
        </tr>`;
    });

    document.getElementById('lista-productos').innerHTML = html;
    document.getElementById('total-venta').innerText = state.totalVenta.toFixed(2);
}

export function eliminarItem(i) {
    state.carrito.splice(i, 1);
    renderizarTabla();
}

export function initCarrito() {
    // Delegación de eventos: ya no usamos onclick="" inline
    document.getElementById('lista-productos').addEventListener('click', (e) => {
        const btn = e.target.closest('[data-eliminar-item]');
        if (btn) eliminarItem(parseInt(btn.dataset.eliminarItem));
    });

    document.getElementById('confirmar-peso-btn')?.addEventListener('click', confirmarPeso);
}

function cerrarModalPeso() {
    document.getElementById('modal-peso').classList.add('hidden');
}
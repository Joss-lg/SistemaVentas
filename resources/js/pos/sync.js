import { getConfig } from './state.js';
import { notify } from '../shared/notify.js';

export async function actualizarRespaldoProductos() {
    if (!navigator.onLine) return;
    const { rutaBuscarNombre } = getConfig();
    try {
        const res = await fetch(`${rutaBuscarNombre}?q=`);
        const productos = await res.json();
        localStorage.setItem('respaldo_productos', JSON.stringify(productos));
    } catch (e) {
        console.log('Esperando conexión...');
    }
}

export function buscarLocal(query) {
    const productos = JSON.parse(localStorage.getItem('respaldo_productos')) || [];
    return productos.filter(p =>
        p.descripcion.toLowerCase().includes(query.toLowerCase()) ||
        p.codigo_barras == query
    ).slice(0, 10);
}

export async function sincronizarVentasPendientes() {
    const { rutaFinalizar, csrf } = getConfig();
    let ventas = JSON.parse(localStorage.getItem('cola_ventas_abarrotes')) || [];
    if (ventas.length === 0) return;

    for (let i = 0; i < ventas.length; i++) {
        try {
            const res = await fetch(rutaFinalizar, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(ventas[i])
            });
            if (res.ok) { ventas.splice(i, 1); i--; }
        } catch (e) { break; }
    }
    localStorage.setItem('cola_ventas_abarrotes', JSON.stringify(ventas));
    if (ventas.length === 0) notify('success', 'VENTAS SINCRONIZADAS');
}

export function initSync() {
    window.addEventListener('online', () => { sincronizarVentasPendientes(); actualizarRespaldoProductos(); });
    document.addEventListener('DOMContentLoaded', () => { sincronizarVentasPendientes(); actualizarRespaldoProductos(); });
}
import { cobrar } from './cobro.js';
import { pausarVenta, abrirModalRecuperar } from './pausas.js';
import { abrirModalProveedor, abrirModalBusqueda, cerrarTodosLosModales } from './modales.js';

export function initAtajosPos() {
    window.addEventListener('keydown', (e) => {
        if (e.key === 'F2') { e.preventDefault(); abrirModalRecuperar(); }
        if (e.key === 'F4') { e.preventDefault(); pausarVenta(); }
        if (e.key === 'F8') { e.preventDefault(); abrirModalProveedor(); }
        if (e.key === 'F9') { e.preventDefault(); cobrar(); }
        if (e.key === 'F10') { e.preventDefault(); abrirModalBusqueda(); }
        if (e.key === 'Escape') { e.preventDefault(); cerrarTodosLosModales(); }
    });
}
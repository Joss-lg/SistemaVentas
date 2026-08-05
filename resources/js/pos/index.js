import { initSync } from './sync.js';
import { initCarrito } from './carrito.js';
import { initBusqueda } from './busqueda.js';
import { initCobro } from './cobro.js';
import { initProveedores } from './proveedores.js';
import { initPausas } from './pausas.js';
import { initModales } from './modales.js';
import { initAtajosPos } from './atajos-teclado.js';
import { initBascula } from './bascula.js';

export function initPOS() {
    if (!document.getElementById('pos-app')) return; // solo corre en la vista del POS

    initSync();
    initCarrito();
    initBusqueda();
    initCobro();
    initProveedores();
    initPausas();
    initModales();
    initAtajosPos();
    initBascula();
}
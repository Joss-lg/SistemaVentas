import './bootstrap';
import { initTema } from './layout/tema.js';
import { initCajon } from './layout/cajon.js';
import { initAtajosGlobales } from './layout/atajos-teclado.js';
import { initServiceWorker } from './layout/service-worker-init.js';
import { initAlertas } from './layout/app-alerts.js';
import { initPOS } from './pos/index.js';
import { initGestionUsuarios } from './usuarios/gestion.js';
import { initCorteCaja } from './corte/corte-caja.js';

document.addEventListener('DOMContentLoaded', () => {
    initTema();
    initCajon();
    initAtajosGlobales();
    initServiceWorker();
    initAlertas(); 
    initPOS();
    initGestionUsuarios();
    initCorteCaja();
});
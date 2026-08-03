export const state = {
    carrito: [],
    totalVenta: 0,
    metodoSeleccionado: 'efectivo',
    productoPendientePeso: null,
    productoProvSeleccionado: null,
    listaTemporalProveedor: []
};

export function getConfig() {
    const app = document.getElementById('pos-app');
    return app ? app.dataset : {};
}
export function initAtajosGlobales() {
    const { rutaVentasIndex } = document.body.dataset;

    window.addEventListener('keydown', (e) => {
        if (e.key === 'F1') {
            e.preventDefault();
            window.location.href = rutaVentasIndex;
        }
    });
}
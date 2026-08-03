export function initServiceWorker() {
    if (!('serviceWorker' in navigator)) return;

    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
            .then(reg => {
                console.log('%c🚀 Service Worker registrado', 'color: #dc2626; font-weight: 900;');

                // Escuchar si hay actualizaciones del Service Worker en segundo plano
                reg.onupdatefound = () => {
                    const installingWorker = reg.installing;
                    if (installingWorker == null) return;
                    installingWorker.onstatechange = () => {
                        if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('%c🔄 Nueva versión disponible. Recarga para actualizar.', 'color: #eab308; font-weight: 800;');
                        }
                    };
                };
            })
            .catch(err => console.error('Error en Service Worker:', err));
    });
}
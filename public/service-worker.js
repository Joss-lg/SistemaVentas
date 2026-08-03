const CACHE_STATIC = 'f1-static-v1';
const CACHE_ASSETS = 'f1-assets-v1';

// 1. INSTALACIÓN: Forzar activación inmediata
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// 2. ACTIVACIÓN: Limpiar cachés viejos automáticamente cuando subas versión
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_STATIC && key !== CACHE_ASSETS) {
                        console.log('[SW] Eliminando caché antiguo:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. ESTRATEGIA DE CACHÉ (FETCH)
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Solo interceptar peticiones GET
    if (event.request.method !== 'GET') return;

    // A) Assets compilados por Vite (/build/ CSS, JS, fonts, imágenes)
    // Estrategia: Cache First (Si está en caché lo sirve rápido; si no, lo descarga y guarda)
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(
            caches.open(CACHE_ASSETS).then(async (cache) => {
                const cachedResponse = await cache.match(event.request);
                if (cachedResponse) return cachedResponse;

                try {
                    const networkResponse = await fetch(event.request);
                    if (networkResponse && networkResponse.status === 200) {
                        cache.put(event.request, networkResponse.clone());
                    }
                    return networkResponse;
                } catch (error) {
                    return cachedResponse;
                }
            })
        );
        return;
    }

    // B) Navegación y HTML (Vistas de Blade)
    // Estrategia: Network First con respaldo en caché (Para tener siempre la última versión online)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(async (networkResponse) => {
                    const cache = await caches.open(CACHE_STATIC);
                    if (networkResponse.status === 200) {
                        cache.put(event.request, networkResponse.clone());
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(event.request);
                    if (cachedResponse) return cachedResponse;
                    
                    // Si no hay red ni página guardada, intenta mostrar el index o la última vista
                    return caches.match('/');
                })
        );
        return;
    }
});
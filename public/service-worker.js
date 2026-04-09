const CACHE_NAME = 'abarrotes-v1';

// Solo guardamos lo básico para que la app arranque sin errores
const urlsToCache = [
    '/',
    '/login',
    '/manifest.json'
];

// 1. Instalación: Guarda lo básico
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.all(
                urlsToCache.map(url => {
                    return cache.add(url).catch(err => console.warn('No se guardó:', url));
                })
            );
        })
    );
    self.skipWaiting();
});

// 2. Activación: Limpia cachés viejos
self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) return caches.delete(key);
                })
            );
        })
    );
    return self.clients.claim();
});

// 3. Estrategia Network First (Primero Red, si falla, Caché)
// Esto evita que te bloquee el login o el acceso cuando estás online
self.addEventListener('fetch', e => {
    // IMPORTANTE: No tocar peticiones POST (Ventas, Cortes, Login)
    if (e.request.method !== 'GET') return;

    e.respondWith(
        fetch(e.request)
            .then(res => {
                // Si la red jala, clonamos y guardamos en caché para la próxima
                const resClone = res.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(e.request, resClone);
                });
                return res;
            })
            .catch(() => {
                // Si NO hay red (Offline), buscamos en el caché
                return caches.match(e.request).then(res => {
                    if (res) return res;
                    
                    // Si intentas entrar a una página y no hay caché ni red, mandamos al inicio
                    if (e.request.mode === 'navigate') {
                        return caches.match('/');
                    }

                    // Para todo lo demás (imágenes, scripts), mandamos una respuesta vacía
                    return new Response('Offline', { status: 404, statusText: 'Offline mode' });
                });
            })
    );
});
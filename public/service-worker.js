const CACHE_NAME = 'abarrotes-v1';

const urlsToCache = [
    '/',
    '/ventas',
    '/js/sweetalert2.js',
    '/js/tailwind.js',
    '/manifest.json'
];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.all(
                urlsToCache.map(url => {
                    return cache.add(url).catch(err => console.warn('No se guardó en caché:', url));
                })
            );
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(clients.claim());
});

// ESTE ES EL CAMBIO CLAVE PARA QUITAR LOS ERRORES DE CONSOLA
self.addEventListener('fetch', e => {
    e.respondWith(
        caches.match(e.request).then(res => {
            if (res) return res; // Si está en caché, úsalo.

            return fetch(e.request).catch(() => {
                // Si falla el fetch (offline) y es una navegación (F5), evita el dino
                if (e.request.mode === 'navigate') {
                    return caches.match('/ventas') || caches.match('/');
                }
                
                // Si es cualquier otra cosa (como el buscador), responde con un 404 limpio
                // Esto es lo que quita el error de "Failed to convert value to Response"
                return new Response('Offline', { status: 404, statusText: 'Offline mode' });
            });
        })
    );
});
const CACHE_NAME = 'abarrotes-v1';
const urlsToCache = [
    '/admin/gastos',
    '/admin/ventas',
    '/css/app.css', 
    '/js/app.js'
];

// Instalación: Guarda los archivos en la memoria del navegador
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('Cacheando archivos de Abarrotes...');
            return cache.addAll(urlsToCache);
        })
    );
});

// Activación: Limpia caches viejos si actualizas el sistema
self.addEventListener('activate', e => {
    console.log('Service Worker de Abarrotes Activo');
});

// Despachador: Si no hay internet, sirve lo que guardamos en cache
self.addEventListener('fetch', e => {
    e.respondWith(
        caches.match(e.request).then(res => {
            return res || fetch(e.request).catch(() => {
                // Si falla el fetch y es una página, podrías mandar a una ruta offline
                return caches.match('/admin/ventas'); 
            });
        })
    );
});
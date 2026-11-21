const CACHE_NAME = 'burrito-unmsm-v3';
const RECURSOS_BASE = ['/icon.svg'];

self.addEventListener('install', (evento) => {
    evento.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(RECURSOS_BASE))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (evento) => {
    evento.waitUntil(
        caches.keys()
            .then((nombres) => Promise.all(
                nombres.filter((nombre) => nombre !== CACHE_NAME).map((nombre) => caches.delete(nombre)),
            ))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (evento) => {
    const url = new URL(evento.request.url);

    if (evento.request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    evento.respondWith(
        fetch(evento.request).catch(async () => {
            const respuesta = await caches.match(evento.request);

            return respuesta ?? Response.error();
        }),
    );
});

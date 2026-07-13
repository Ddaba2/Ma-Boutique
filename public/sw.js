const CACHE_VERSION = 'gesboutique-v1';
const OFFLINE_URL = '/offline.html';
const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.webmanifest',
    '/icons/icon.svg',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_VERSION).map((key) => caches.delete(key)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    // Pages naviguées : toujours essayer le réseau (données à jour), page hors-ligne en secours.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Ne jamais mettre en cache les appels API : ce sont des données live (stock, ventes...).
    if (request.url.includes('/api/')) {
        return;
    }

    // Fichiers statiques (build Vite, icônes) : cache d'abord, réseau en secours + mise à jour du cache.
    if (request.url.includes('/build/') || request.url.includes('/icons/')) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const network = fetch(request)
                    .then((response) => {
                        caches.open(CACHE_VERSION).then((cache) => cache.put(request, response.clone()));
                        return response;
                    })
                    .catch(() => cached);
                return cached || network;
            })
        );
    }
});

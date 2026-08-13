const CACHE_VERSION = 'gesboutique-v6';
const OFFLINE_URL = '/offline.html';
const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.json',
    '/icons/icon.svg',
];

// Pages qui doivent rester utilisables même si le serveur est injoignable au
// moment même où on les ouvre (pas seulement après un premier chargement déjà
// réussi dans l'onglet courant) : /ventes/create est le cœur de la file
// d'attente hors-ligne (voir public/js/offline-ventes.js), donc doit pouvoir
// s'ouvrir sans réseau pour continuer à enregistrer des ventes. Non précachées
// à l'install (nécessitent une session connectée) : mises en cache dès la
// première navigation réussie, voir l'écouteur "fetch" ci-dessous.
const APP_SHELL_URLS = ['/dashboard', '/ventes/create', '/produits', '/stocks'];

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

    // Pages naviguées : toujours essayer le réseau en premier (données à
    // jour). Pour l'app shell, une réponse réussie rafraîchit son cache ; en
    // cas d'échec réseau, on sert la dernière copie en cache de CETTE page
    // avant de se rabattre sur l'écran générique "Pas de connexion".
    if (request.mode === 'navigate') {
        const url = new URL(request.url);
        const estAppShell = APP_SHELL_URLS.includes(url.pathname);

        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (estAppShell && response.ok) {
                        const copie = response.clone();
                        caches.open(CACHE_VERSION).then((cache) => cache.put(request, copie));
                    }
                    return response;
                })
                .catch(() =>
                    caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL))
                )
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
                        // Clone synchrone, avant tout autre usage du corps de la réponse :
                        // un cache.put() qui échoue (clone déjà consommé, quota dépassé...)
                        // ne doit jamais faire planter la requête réseau elle-même.
                        const copie = response.clone();
                        caches.open(CACHE_VERSION)
                            .then((cache) => cache.put(request, copie))
                            .catch(() => {});
                        return response;
                    })
                    .catch(() => cached);
                return cached || network;
            })
        );
    }
});

// Best-effort : au retour de connexion, on relaie aux onglets ouverts pour
// qu'ils rejouent leur file de ventes en attente (logique IndexedDB tenue
// côté page, pas dupliquée ici). Ne fonctionne que si un onglet est ouvert ;
// le vrai filet de sécurité reste l'écouteur "online" côté page (offline-ventes.js),
// qui couvre aussi les navigateurs sans Background Sync (Safari/iOS).
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-ventes') {
        event.waitUntil(
            self.clients.matchAll().then((clients) => {
                clients.forEach((client) => client.postMessage({ type: 'SYNC_VENTES' }));
            })
        );
    }
});

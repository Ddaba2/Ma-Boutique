/**
 * File d'attente des ventes créées hors-ligne (IndexedDB) + synchronisation
 * automatique au retour de connexion. Voir resources/views/ventes/create.blade.php
 * pour l'utilisation (interception du submit uniquement en cas d'échec réseau —
 * le flux en ligne normal n'est pas modifié).
 */
window.OfflineVentes = (function () {
    const DB_NAME = 'gesboutique';
    const DB_VERSION = 1;
    const STORE_NAME = 'ventes_en_attente';
    let dbPromise = null;

    function getDb() {
        if (!dbPromise) {
            dbPromise = new Promise((resolve, reject) => {
                const request = indexedDB.open(DB_NAME, DB_VERSION);
                request.onupgradeneeded = (event) => {
                    const db = event.target.result;
                    if (!db.objectStoreNames.contains(STORE_NAME)) {
                        db.createObjectStore(STORE_NAME, { keyPath: 'uuid' });
                    }
                };
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }
        return dbPromise;
    }

    function genererUuid() {
        if (window.crypto && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
            const r = (Math.random() * 16) | 0;
            const v = c === 'x' ? r : (r & 0x3) | 0x8;
            return v.toString(16);
        });
    }

    async function enqueue(vente) {
        const db = await getDb();
        const uuid = genererUuid();
        const entry = Object.assign({}, vente, {
            uuid: uuid,
            statut: 'en_attente',
            erreur: null,
            cree_le: new Date().toISOString(),
        });

        await new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            tx.objectStore(STORE_NAME).put(entry);
            tx.oncomplete = resolve;
            tx.onerror = () => reject(tx.error);
        });

        registerBackgroundSync();
        return uuid;
    }

    async function getAll() {
        const db = await getDb();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readonly');
            const req = tx.objectStore(STORE_NAME).getAll();
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async function remove(uuid) {
        const db = await getDb();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            tx.objectStore(STORE_NAME).delete(uuid);
            tx.oncomplete = resolve;
            tx.onerror = () => reject(tx.error);
        });
    }

    async function updateEntry(uuid, changes) {
        const db = await getDb();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            const getReq = store.get(uuid);
            getReq.onsuccess = () => {
                if (getReq.result) {
                    store.put(Object.assign({}, getReq.result, changes));
                }
            };
            tx.oncomplete = resolve;
            tx.onerror = () => reject(tx.error);
        });
    }

    async function count() {
        const all = await getAll();
        return all.filter((v) => v.statut === 'en_attente' || v.statut === 'echec').length;
    }

    function registerBackgroundSync() {
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            navigator.serviceWorker.ready
                .then((reg) => reg.sync.register('sync-ventes'))
                .catch(() => {
                    // Background Sync indisponible (ex. Safari) : l'écouteur "online"
                    // ci-dessous reste le mécanisme de secours principal.
                });
        }
    }

    async function rafraichirToken() {
        const resp = await fetch('/api/csrf-refresh', { credentials: 'same-origin' });
        if (!resp.ok) {
            throw new Error('Impossible de rafraîchir le jeton de sécurité.');
        }
        const data = await resp.json();
        return data.token;
    }

    async function syncAll() {
        const enAttente = (await getAll()).filter((v) => v.statut === 'en_attente' || v.statut === 'echec');
        if (enAttente.length === 0) {
            return { synchronisees: 0, conflits: 0, restantes: 0, sessionExpiree: false };
        }

        let token;
        try {
            token = await rafraichirToken();
        } catch (e) {
            return { synchronisees: 0, conflits: 0, restantes: enAttente.length, sessionExpiree: true };
        }

        let synchronisees = 0;
        let conflits = 0;

        for (const vente of enAttente) {
            try {
                const resp = await fetch('/api/ventes/sync', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({
                        uuid_client: vente.uuid,
                        client_nom: vente.client_nom,
                        client_telephone: vente.client_telephone,
                        mode_paiement: vente.mode_paiement,
                        montant_recu: vente.montant_recu,
                        lignes: vente.lignes,
                    }),
                });

                if (resp.status === 401 || resp.status === 419) {
                    await updateEntry(vente.uuid, { statut: 'echec', erreur: 'Session expirée, reconnectez-vous.' });
                    continue;
                }

                if (!resp.ok) {
                    await updateEntry(vente.uuid, { statut: 'echec', erreur: 'Erreur serveur (' + resp.status + ')' });
                    continue;
                }

                const data = await resp.json();
                await remove(vente.uuid);
                synchronisees++;
                if (data.conflit) {
                    conflits++;
                }
            } catch (networkError) {
                // Toujours hors-ligne : on arrête là, l'écouteur "online" retentera.
                break;
            }
        }

        const restantes = await count();
        return { synchronisees, conflits, restantes, sessionExpiree: false };
    }

    return { enqueue, getAll, count, syncAll, genererUuid };
})();

(function () {
    function rafraichirBadge() {
        const badge = document.getElementById('offline-ventes-badge');
        if (!badge) return;
        window.OfflineVentes.count().then((n) => {
            if (n > 0) {
                badge.textContent = n;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });
    }

    // Indicateur en ligne/hors ligne dans la barre du haut : utile en caisse
    // pour comprendre pourquoi une vente vient d'être mise en file d'attente
    // au lieu d'être enregistrée directement.
    function rafraichirConnectivite() {
        const badge = document.getElementById('connectivite-indicateur');
        const label = document.getElementById('connectivite-label');
        if (!badge || !label) return;
        const enLigne = navigator.onLine;
        badge.classList.remove('text-bg-success', 'text-bg-secondary', 'text-bg-danger');
        badge.classList.add(enLigne ? 'text-bg-success' : 'text-bg-danger');
        label.textContent = enLigne ? 'En ligne' : 'Hors ligne';
    }

    // Affiche le résultat d'une synchronisation (succès, conflits, session
    // expirée) : sans ça, une synchronisation en tâche de fond ne donne aucun
    // retour visible au caissier au-delà du badge de comptage.
    function afficherResultatSync(resultat) {
        const zone = document.getElementById('offline-sync-alerte');
        if (!zone || !resultat) return;

        let message = null;
        let type = 'success';

        if (resultat.sessionExpiree) {
            message = "Des ventes hors ligne attendent d'être synchronisées, mais votre session a expiré. Reconnectez-vous pour les envoyer.";
            type = 'warning';
        } else if (resultat.synchronisees > 0) {
            message = resultat.synchronisees + ' vente(s) hors ligne synchronisée(s) avec succès.';
            if (resultat.conflits > 0) {
                message += ' ' + resultat.conflits + ' en conflit de stock à vérifier.';
                type = 'warning';
            }
        }

        if (!message) return;

        const div = document.createElement('div');
        div.className = 'alert alert-' + type + ' alert-dismissible fade show mt-3 animate-fadeInUp';
        div.setAttribute('role', 'alert');
        div.textContent = message;

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close';
        closeBtn.setAttribute('data-bs-dismiss', 'alert');
        div.appendChild(closeBtn);

        zone.appendChild(div);
    }

    function synchroniser() {
        window.OfflineVentes.syncAll().then((resultat) => {
            rafraichirBadge();
            afficherResultatSync(resultat);
        });
    }

    window.addEventListener('online', () => {
        rafraichirConnectivite();
        synchroniser();
    });
    window.addEventListener('offline', rafraichirConnectivite);

    document.addEventListener('DOMContentLoaded', () => {
        rafraichirConnectivite();
        rafraichirBadge();
        synchroniser();
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'SYNC_VENTES') {
                synchroniser();
            }
        });
    }

    window.OfflineVentesUI = { rafraichirBadge, rafraichirConnectivite, synchroniser };
})();

self.addEventListener('install', (e) => {
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    console.log('Service Worker actif');
});

// Intercepte les requêtes réseau (obligatoire pour valider l'installation PWA)
self.addEventListener('fetch', (e) => {
    // Dans une version avancée, on mettrait en cache ici.
    // Pour l'instant on laisse passer le réseau normalement.
    e.respondWith(fetch(e.request));
});

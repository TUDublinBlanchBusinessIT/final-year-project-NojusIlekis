const CACHE_NAME = 'snugbug-v1';
const OFFLINE_URL = '/offline.html';

// Static assets to cache on install
const PRECACHE_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// Install event — cache essential assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[SW] Precaching assets');
            return cache.addAll(PRECACHE_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate event — clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch event — network first for pages, cache first for static assets
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip API and auth requests — always go to network
    if (url.pathname.startsWith('/api/') ||
        url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/register') ||
        url.pathname.startsWith('/logout') ||
        url.pathname.startsWith('/chatbot/') ||
        url.pathname.startsWith('/sanctum/')) {
        return;
    }

    // Static assets (CSS, JS, images, fonts) — cache first
    if (request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font' ||
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/storage/')) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((response) => {
                    // Cache the new response
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                }).catch(() => {
                    // Return nothing for failed asset loads
                });
            })
        );
        return;
    }

    // HTML pages — network first, fall back to cache, then offline page
    event.respondWith(
        fetch(request)
            .then((response) => {
                // Cache successful page responses
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => {
                // Try cache first
                return caches.match(request).then((cached) => {
                    if (cached) return cached;
                    // Show offline page
                    return caches.match(OFFLINE_URL);
                });
            })
    );
});

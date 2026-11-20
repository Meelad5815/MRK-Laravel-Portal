const CACHE_NAME = 'mrk-study-static-v1';

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const keys = await caches.keys();
        await Promise.all(keys.filter((key) => key.startsWith('mrk-study-static-') && key !== CACHE_NAME).map((key) => caches.delete(key)));
        await self.clients.claim();
    })());
});

// Cache only versioned public assets. Never cache HTML, authenticated pages,
// VULMS pages, API responses, form submissions, or personal study records.
self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    const isPublicAsset =
        url.pathname.startsWith('/build/assets/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname === '/favicon.svg' ||
        url.pathname === '/mrk-pwa-icon.svg';

    if (!isPublicAsset) return;

    event.respondWith((async () => {
        const cache = await caches.open(CACHE_NAME);
        const cached = await cache.match(request);
        if (cached) return cached;

        try {
            const response = await fetch(request);
            if (response.ok && response.type === 'basic') {
                await cache.put(request, response.clone());
            }
            return response;
        } catch (error) {
            return cached || Response.error();
        }
    })());
});

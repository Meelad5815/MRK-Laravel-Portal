const CACHE_NAME = 'mrk-quran-offline-v1';
const MODEL_PREFIX = '/quran-asr/';
const PAGE_PATH = '/quran/murajaah';

self.addEventListener('install', (event) => {
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE_NAME);
    try { await cache.add(PAGE_PATH); } catch (_) {}
    await self.skipWaiting();
  })());
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keys = await caches.keys();
    await Promise.all(keys.filter((key) => key.startsWith('mrk-quran-') && key !== CACHE_NAME).map((key) => caches.delete(key)));
    await self.clients.claim();
  })());
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') return;
  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  const isModel = url.pathname.startsWith(MODEL_PREFIX);
  const isMurajaahPage = url.pathname === PAGE_PATH || url.pathname === PAGE_PATH + '/';
  const isStaticAsset = /^\/(build|css|js|fonts|images)\//.test(url.pathname);
  if (!isModel && !isMurajaahPage && !isStaticAsset) return;

  event.respondWith((async () => {
    const cache = await caches.open(CACHE_NAME);
    if (isModel) {
      const cached = await cache.match(request);
      if (cached) return cached;
      const response = await fetch(request);
      if (response.ok) { try { await cache.put(request, response.clone()); } catch (_) {} }
      return response;
    }

    try {
      const response = await fetch(request);
      if (response.ok) { try { await cache.put(request, response.clone()); } catch (_) {} }
      return response;
    } catch (error) {
      const cached = await cache.match(request);
      if (cached) return cached;
      if (isMurajaahPage) {
        const page = await cache.match(PAGE_PATH);
        if (page) return page;
      }
      throw error;
    }
  })());
});

/* VTS-OPS-LOG Service Worker — v1 */
const CACHE  = 'vts-ops-v1';
const STATIC = [
    /* CDN assets — cache on first use */
    'https://cdn.tailwindcss.com',
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js',
    'https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js',
    'https://unpkg.com/tippy.js@6/dist/tippy.umd.min.js',
    'https://unpkg.com/tippy.js@6/dist/tippy.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap',
    'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@200..700,0..1&display=swap',
];

/* ── Install: pre-cache static assets ── */
self.addEventListener('install', function (e) {
    e.waitUntil(
        caches.open(CACHE).then(function (c) {
            return Promise.allSettled(
                STATIC.map(function (url) {
                    return c.add(new Request(url, { mode: 'no-cors' }));
                })
            );
        }).then(function () { return self.skipWaiting(); })
    );
});

/* ── Activate: evict old caches ── */
self.addEventListener('activate', function (e) {
    e.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(function (k) { return k !== CACHE; })
                    .map(function (k) { return caches.delete(k); })
            );
        }).then(function () { return self.clients.claim(); })
    );
});

/* ── Fetch: stale-while-revalidate for CDN, network-first for PHP pages ── */
self.addEventListener('fetch', function (e) {
    var url = e.request.url;

    /* Only handle GET requests */
    if (e.request.method !== 'GET') return;

    /* CDN + fonts → cache-first (offline capable) */
    var cdnHosts = ['cdn.tailwindcss.com', 'cdn.jsdelivr.net', 'unpkg.com',
                    'fonts.googleapis.com', 'fonts.gstatic.com'];
    var isCdn = cdnHosts.some(function (h) { return url.includes(h); });

    if (isCdn) {
        e.respondWith(
            caches.match(e.request).then(function (cached) {
                if (cached) return cached;
                return fetch(e.request).then(function (res) {
                    if (res && res.status === 200) {
                        var resClone = res.clone();
                        caches.open(CACHE).then(function (c) { c.put(e.request, resClone); });
                    }
                    return res;
                });
            })
        );
        return;
    }

    /* Local PHP pages → network-first, fall back to cache */
    if (url.includes('.php') || url.endsWith('/')) {
        e.respondWith(
            fetch(e.request).then(function (res) {
                if (res && res.status === 200) {
                    var resClone = res.clone();
                    caches.open(CACHE).then(function (c) { c.put(e.request, resClone); });
                }
                return res;
            }).catch(function () {
                return caches.match(e.request);
            })
        );
        return;
    }

    /* Local static assets (css, js, images) → cache-first */
    e.respondWith(
        caches.match(e.request).then(function (cached) {
            var netFetch = fetch(e.request).then(function (res) {
                if (res && res.status === 200) {
                    var resClone = res.clone();
                    caches.open(CACHE).then(function (c) { c.put(e.request, resClone); });
                }
                return res;
            });
            return cached || netFetch;
        })
    );
});

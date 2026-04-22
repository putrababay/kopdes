const CACHE_NAME = 'koperasi-v1';
const assetsToCache = [
    './',
    './login',
    './css/app.css', // Sesuaikan jika ada file CSS lokal
    './images/logo-ab.png',
    './manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Mulai Caching...');
            // Menggunakan pemetaan individu agar jika satu gagal, yang lain tetap masuk
            return Promise.all(
                assetsToCache.map((url) => {
                    return cache.add(url).catch((err) => {
                        console.error(`Gagal memuat file: ${url}`, err);
                    });
                })
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});

// Tahap Activate: Menghapus cache lama jika ada update
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('Clearing old cache...');
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});
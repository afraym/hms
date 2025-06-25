const CACHE_NAME = 'hms-cache-v1';
const urlsToCache = [
  '/',
  // CSS Files
  '/assets/css/custom.css',
  '/assets/css/material-dashboard.css',
  '/assets/css/nucleo-icons.css',
  '/assets/css/nucleo-svg.css',
  '/assets/css/fileinput.min.css',
  '/assets/css/fileinput-rtl.css',
  '/assets/css/jquery.toast.min.css',

  // JavaScript Files
  '/assets/js/core/popper.min.js',
  '/assets/js/core/bootstrap.min.js',
  '/assets/js/plugins/perfect-scrollbar.min.js',
  '/assets/js/plugins/smooth-scrollbar.min.js',
  '/assets/js/plugins/chartjs.min.js',
  '/assets/js/jquery-3.6.0.min.js',
  '/assets/js/fileinput.min.js',
  '/assets/js/locales/ar.js',
  '/assets/js/jquery.toast.min.js',
  '/assets/js/dexie.js',
  '/assets/js/material-dashboard.min.js',

  // Fonts
  '/assets/fonts/inter.css',
  '/assets/css/Material+Icons+Round.css',
  '/assets/css/Material+Symbols+Rounded.css',
  '/assets/css/42d5adcbca.js',

  // Images
  '/assets/img/apple-touch-icon.png',
  '/assets/img/favicon-32x32.png', 
  '/assets/img/favicon-16x16.png',
  '/assets/img/background.jpg',
  '/assets/img/uhi.png',
  '/assets/img/amanlogo.png',

  // Offline fallback
  '/offline.html'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                if (event.request.mode === 'navigate') {
                    return caches.match('/offline.blade.php')
                        .then(response => {
                            return response || new Response('أنت غير متصل بالسيرفر');
                        });
                }
                return caches.match(event.request);
            })
    );
});
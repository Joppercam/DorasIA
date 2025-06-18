// Dorasia Service Worker v1.0
// PWA + Push Notifications + Offline Cache

const CACHE_NAME = 'dorasia-v1.0';
const STATIC_CACHE = 'dorasia-static-v1.0';
const DYNAMIC_CACHE = 'dorasia-dynamic-v1.0';

// Archivos críticos que se cachean inmediatamente
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/build/assets/app-gwGkVTan.css',
  '/build/assets/app-B1_ivJOC.js',
  '/favicon.svg',
  '/offline.html' // Página offline fallback
];

// Rutas importantes para cache dinámico
const DYNAMIC_ROUTES = [
  '/explorar',
  '/series/',
  '/peliculas/',
  '/noticias/',
  '/proximamente'
];

// === EVENTOS DEL SERVICE WORKER ===

// Instalación - Cachear archivos estáticos
self.addEventListener('install', event => {
  console.log('🚀 Dorasia SW: Instalando...');
  
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('📦 Dorasia SW: Cacheando archivos estáticos');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('✅ Dorasia SW: Instalación completa');
        return self.skipWaiting(); // Activar inmediatamente
      })
      .catch(err => {
        console.error('❌ Dorasia SW: Error en instalación:', err);
      })
  );
});

// Activación - Limpiar caches antiguos
self.addEventListener('activate', event => {
  console.log('🔄 Dorasia SW: Activando...');
  
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
              console.log('🗑️ Dorasia SW: Eliminando cache antiguo:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('✅ Dorasia SW: Activación completa');
        return self.clients.claim(); // Tomar control inmediatamente
      })
  );
});

// Fetch - Estrategia de cache
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Solo manejar requests HTTP/HTTPS de nuestro dominio
  if (!request.url.startsWith('http')) return;
  if (url.origin !== location.origin) return;
  
  event.respondWith(handleFetch(request));
});

// === ESTRATEGIAS DE CACHE ===

async function handleFetch(request) {
  const url = new URL(request.url);
  
  try {
    // 1. Archivos estáticos - Cache First
    if (isStaticAsset(url.pathname)) {
      return await cacheFirst(request);
    }
    
    // 2. API y datos dinámicos - Network First
    if (isApiRequest(url.pathname)) {
      return await networkFirst(request);
    }
    
    // 3. Páginas de contenido - Stale While Revalidate
    if (isContentPage(url.pathname)) {
      return await staleWhileRevalidate(request);
    }
    
    // 4. Default - Network First con fallback
    return await networkFirst(request);
    
  } catch (error) {
    console.error('🚨 Dorasia SW: Error en fetch:', error);
    return await handleOffline(request);
  }
}

// Cache First - Para assets estáticos
async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) return cached;
  
  const response = await fetch(request);
  const cache = await caches.open(STATIC_CACHE);
  cache.put(request, response.clone());
  return response;
}

// Network First - Para datos frescos
async function networkFirst(request) {
  try {
    const response = await fetch(request);
    const cache = await caches.open(DYNAMIC_CACHE);
    cache.put(request, response.clone());
    return response;
  } catch {
    const cached = await caches.match(request);
    return cached || await handleOffline(request);
  }
}

// Stale While Revalidate - Para contenido
async function staleWhileRevalidate(request) {
  const cached = await caches.match(request);
  
  const fetchPromise = fetch(request).then(response => {
    const cache = caches.open(DYNAMIC_CACHE);
    cache.then(c => c.put(request, response.clone()));
    return response;
  });
  
  return cached || fetchPromise;
}

// === HELPERS ===

function isStaticAsset(pathname) {
  return pathname.includes('/build/') || 
         pathname.includes('/icons/') ||
         pathname.endsWith('.css') ||
         pathname.endsWith('.js') ||
         pathname.endsWith('.png') ||
         pathname.endsWith('.svg');
}

function isApiRequest(pathname) {
  return pathname.startsWith('/api/') ||
         pathname.includes('/auth/') ||
         pathname.includes('/rate') ||
         pathname.includes('/watchlist');
}

function isContentPage(pathname) {
  return DYNAMIC_ROUTES.some(route => pathname.startsWith(route)) ||
         pathname.startsWith('/series/') ||
         pathname.startsWith('/peliculas/') ||
         pathname.startsWith('/actores/');
}

async function handleOffline(request) {
  // Para navegación, mostrar página offline
  if (request.mode === 'navigate') {
    const offlinePage = await caches.match('/offline.html');
    return offlinePage || new Response('Dorasia - Sin conexión', {
      status: 200,
      headers: { 'Content-Type': 'text/html' }
    });
  }
  
  // Para assets, devolver respuesta básica
  return new Response('Recurso no disponible offline', {
    status: 503,
    statusText: 'Service Unavailable'
  });
}

// === PUSH NOTIFICATIONS ===

// Recibir notificación push
self.addEventListener('push', event => {
  console.log('📱 Dorasia SW: Push recibido');
  
  let data = {};
  if (event.data) {
    data = event.data.json();
  }
  
  const options = {
    title: data.title || '🎬 Dorasia - Nueva actualización',
    body: data.body || 'Descubre las últimas series coreanas',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-72x72.png',
    image: data.image || '/dorasia-logo-social.svg',
    tag: data.tag || 'dorasia-notification',
    data: {
      url: data.url || '/',
      timestamp: Date.now()
    },
    actions: [
      {
        action: 'view',
        title: '👀 Ver ahora',
        icon: '/icons/icon-72x72.png'
      },
      {
        action: 'dismiss',
        title: '✖️ Descartar',
        icon: '/icons/icon-72x72.png'
      }
    ],
    vibrate: [300, 100, 300],
    requireInteraction: false
  };
  
  event.waitUntil(
    self.registration.showNotification(options.title, options)
  );
});

// Manejar clicks en notificaciones
self.addEventListener('notificationclick', event => {
  console.log('🔔 Dorasia SW: Click en notificación');
  
  event.notification.close();
  
  if (event.action === 'dismiss') {
    return; // No hacer nada
  }
  
  const url = event.notification.data?.url || '/';
  
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then(clientList => {
      // Si ya hay una ventana abierta, enfocarla
      for (const client of clientList) {
        if (client.url.includes(url) && 'focus' in client) {
          return client.focus();
        }
      }
      
      // Si no, abrir nueva ventana
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
    })
  );
});

// === MENSAJES DESDE LA APP ===

self.addEventListener('message', event => {
  console.log('💬 Dorasia SW: Mensaje recibido:', event.data);
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CACHE_SERIES') {
    // Cachear series específicas para offline
    const seriesUrl = event.data.url;
    caches.open(DYNAMIC_CACHE).then(cache => {
      cache.add(seriesUrl);
    });
  }
});

console.log('🎬 Dorasia Service Worker v1.0 cargado');
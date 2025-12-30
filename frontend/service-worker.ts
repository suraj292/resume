/// <reference lib="webworker" />
import { clientsClaim } from 'workbox-core'
import { precacheAndRoute } from 'workbox-precaching'
import { registerRoute } from 'workbox-routing'
import { StaleWhileRevalidate, CacheFirst, NetworkFirst } from 'workbox-strategies'
import { ExpirationPlugin } from 'workbox-expiration'

declare let self: ServiceWorkerGlobalScope
declare interface ExtendableEvent extends Event {
    waitUntil(fn: Promise<any>): void;
}
declare interface SyncEvent extends ExtendableEvent {
    readonly tag: string;
}

self.skipWaiting()
clientsClaim()

// Precache entries injected by Vite
precacheAndRoute(self.__WB_MANIFEST)

// Cache Google Fonts & FontAwesome
registerRoute(
    ({ url }) => url.origin === 'https://fonts.googleapis.com' ||
        url.origin === 'https://fonts.gstatic.com' ||
        url.origin === 'https://cdnjs.cloudflare.com',
    new CacheFirst({
        cacheName: 'fonts-and-icons',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 30,
                maxAgeSeconds: 60 * 60 * 24 * 365, // 1 year
            }),
        ],
    })
)

// Cache API responses (Template data)
registerRoute(
    ({ url }) => url.pathname.startsWith('/api/v1/templates'),
    new StaleWhileRevalidate({
        cacheName: 'api-templates',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 50,
                maxAgeSeconds: 60 * 60 * 24, // 24 hours
            }),
        ],
    })
)

// Offline fallback for resume builder data (NetworkFirst)
// We want fresh data if possible, but fallback to cache if offline
registerRoute(
    ({ url }) => url.pathname.includes('/resume/'),
    new NetworkFirst({
        cacheName: 'resume-data',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 10,
                maxAgeSeconds: 60 * 60 * 24 * 7, // 7 days
            }),
            // Custom plugin to handle offline fallback if needed
            {
                handlerDidError: async () => {
                    return Response.error(); // Let the app handle the error or provide offline page
                }
            }
        ],
    })
)

// Background Sync for Auto-save (Stub)
// In a real app, use workbox-background-sync
self.addEventListener('sync', (event: any) => {
    if (event.tag === 'sync-resume') {
        event.waitUntil(syncResumeData())
    }
})

async function syncResumeData() {
    console.log('Background sync: Syncing resume data...')
    // Implementation would read from IndexedDB and push to API
}

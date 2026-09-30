/// <reference lib="webworker" />

import { clientsClaim } from "workbox-core";
import { precacheAndRoute, cleanupOutdatedCaches } from "workbox-precaching";

// ==================== Service Worker ====================

declare const self: ServiceWorkerGlobalScope;

// ==================== Workbox ====================

self.skipWaiting();
clientsClaim();

cleanupOutdatedCaches();

precacheAndRoute(self.__WB_MANIFEST);

// ==================== Push Notification ====================

self.addEventListener("push", (event) => {
    if (!event.data) {
        return;
    }

    const data = event.data.json();

    const title = data.title ?? "DELIVER";

    const options: NotificationOptions = {
        body: data.body ?? "",
        icon: data.icon ?? "/pwa-192x192.png",
        badge: data.badge ?? "/pwa-192x192.png",
        data: data.data ?? {
            url: "/",
        },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// ==================== Notification Click ====================

self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    // ==================== Notification URL ====================

    const notificationUrl = event.notification.data?.url ?? "/";

    const url = new URL(notificationUrl, self.location.origin).href;

    console.log("[Push] Notification clicked:", url);

    // ==================== Open Target URL ====================

    event.waitUntil(self.clients.openWindow(url));
});

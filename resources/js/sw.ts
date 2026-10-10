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

    const notificationUrl = event.notification.data?.url ?? "/";
    const url = new URL(notificationUrl, self.location.origin).href;

    event.waitUntil(
        (async () => {
            const windows = await self.clients.matchAll({
                type: "window",
                includeUncontrolled: true,
            });

            // App already open (installed PWA window or tab): reuse it
            for (const client of windows) {
                if (client.url.startsWith(self.location.origin)) {
                    await client.focus();

                    if ("navigate" in client) {
                        await (client as WindowClient).navigate(url);
                    }

                    return;
                }
            }

            // App is closed: open it (the OS routes it to the PWA if installed)
            await self.clients.openWindow(url);
        })(),
    );
});

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

    const targetUrl = new URL(
        event.notification.data?.url ?? "/",
        self.location.origin,
    );

    // Only allow navigation within this application.
    if (targetUrl.origin !== self.location.origin) {
        return;
    }

    event.waitUntil(
        (async () => {
            const windows = await self.clients.matchAll({
                type: "window",
                includeUncontrolled: true,
            });

            // Reuse an existing application window when possible.
            for (const client of windows) {
                if ("focus" in client) {
                    await client.navigate(targetUrl.href);
                    await client.focus();
                    return;
                }
            }

            // Otherwise, open the requested page.
            await self.clients.openWindow(targetUrl.href);
        })(),
    );
});

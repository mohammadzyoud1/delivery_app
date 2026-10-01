/// <reference types="vite/client" />

// ==================== Types ====================

interface PushSubscriptionData {
    endpoint: string;
    publicKey: string;
    authToken: string;
    contentEncoding?: string;
}

// ==================== Convert VAPID Key ====================

const urlBase64ToArrayBuffer = (base64String: string): ArrayBuffer => {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);

    const base64 = (base64String + padding)
        .replace(/-/g, "+")
        .replace(/_/g, "/");

    const rawData = window.atob(base64);

    const bytes = new Uint8Array(rawData.length);

    for (let index = 0; index < rawData.length; index++) {
        bytes[index] = rawData.charCodeAt(index);
    }

    return bytes.buffer;
};

// ==================== Register Push Notifications ====================

export const registerPushNotifications = async (): Promise<void> => {
    // ==================== Browser Support ====================

    if (
        !("serviceWorker" in navigator) ||
        !("PushManager" in window) ||
        !("Notification" in window)
    ) {
        return;
    }

    // ==================== VAPID Public Key ====================

    const publicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;

    if (!publicKey) {
        return;
    }

    // ==================== Notification Permission ====================

    const permission = await Notification.requestPermission();

    if (permission !== "granted") {
        return;
    }

    // ==================== Get PWA Service Worker ====================

    const registrations = await navigator.serviceWorker.getRegistrations();

    const registration = registrations.find(
        (item) =>
            item.active?.scriptURL.endsWith("/build/sw.js") ||
            item.waiting?.scriptURL.endsWith("/build/sw.js") ||
            item.installing?.scriptURL.endsWith("/build/sw.js"),
    );

    if (!registration) {
        console.error(registrations);

        return;
    }

    // ==================== Get Existing Subscription ====================

    let subscription = await registration.pushManager.getSubscription();

    // ==================== Create Subscription ====================

    if (!subscription) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToArrayBuffer(publicKey),
        });
    }

    // ==================== Subscription Data ====================

    const subscriptionJson = subscription.toJSON();

    const keys = subscriptionJson.keys;

    if (!subscriptionJson.endpoint || !keys?.p256dh || !keys?.auth) {
        return;
    }

    // ==================== Send Subscription To Laravel ====================

    const response = await fetch("/push-subscriptions", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN":
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") ?? "",
        },
        credentials: "same-origin",
        body: JSON.stringify({
            endpoint: subscriptionJson.endpoint,
            publicKey: keys.p256dh,
            authToken: keys.auth,
            contentEncoding: "aes128gcm",
        }),
    });
};

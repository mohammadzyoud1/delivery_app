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
    console.log("[Push] Started");

    // ==================== Browser Support ====================

    if (
        !("serviceWorker" in navigator) ||
        !("PushManager" in window) ||
        !("Notification" in window)
    ) {
        console.log("[Push] Browser does not support push");

        return;
    }

    console.log("[Push] Browser support OK");

    // ==================== VAPID Public Key ====================

    const publicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;

    console.log("[Push] VAPID key exists:", !!publicKey);

    if (!publicKey) {
        console.error("[Push] VITE_VAPID_PUBLIC_KEY is missing.");

        return;
    }

    // ==================== Notification Permission ====================

    console.log("[Push] Permission:", Notification.permission);

    const permission = await Notification.requestPermission();

    console.log("[Push] Permission result:", permission);

    if (permission !== "granted") {
        console.log("[Push] Permission not granted");

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
        console.error(
            "[Push] PWA service worker registration not found.",
            registrations,
        );

        return;
    }

    console.log("[Push] Service worker found:", registration.scope);

    // ==================== Get Existing Subscription ====================

    let subscription = await registration.pushManager.getSubscription();

    console.log("[Push] Existing subscription:", !!subscription);

    // ==================== Create Subscription ====================

    if (!subscription) {
        console.log("[Push] Creating subscription...");

        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToArrayBuffer(publicKey),
        });

        console.log("[Push] Subscription created");
    }

    // ==================== Subscription Data ====================

    const subscriptionJson = subscription.toJSON();

    console.log("[Push] Subscription JSON:", subscriptionJson);

    const keys = subscriptionJson.keys;

    if (!subscriptionJson.endpoint || !keys?.p256dh || !keys?.auth) {
        console.error("[Push] Invalid push subscription.");

        return;
    }

    // ==================== Send Subscription To Laravel ====================

    console.log("[Push] Sending subscription to Laravel...");

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

    console.log("[Push] Laravel response:", response.status);
};

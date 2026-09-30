import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { registerSW } from "virtual:pwa-register";

import { getInitialLanguage } from "./Utils/language";
import { registerPushNotifications } from "./Utils/pushNotifications";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", {
            eager: true,
        });

        return pages[`./Pages/${name}.vue`];
    },

    setup({ el, App, props, plugin }) {
        const language = getInitialLanguage();

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .provide("language", language)
            .mount(el);

        // ==================== Register PWA Service Worker ====================

        registerSW({
            immediate: true,
        });

        // ==================== Register Push Notifications ====================

        setTimeout(() => {
            registerPushNotifications().catch((error) => {
                console.error("Failed to register push notifications:", error);
            });
        }, 1000);
    },
});

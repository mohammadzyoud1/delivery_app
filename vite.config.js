import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";
import fs from "fs";
import tailwindcss from "@tailwindcss/vite";
import { VitePWA } from "vite-plugin-pwa";
export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
        tailwindcss({}),

        VitePWA({
            registerType: "autoUpdate",
            strategies: "injectManifest",
            srcDir: "resources/js",
            filename: "sw.ts",

            manifest: {
                id: "/",
                name: "DELIVER",
                short_name: "DELIVER",
                description: "A marketplace and delivery platform.",
                theme_color: "#2563eb",
                background_color: "#f8fafc",
                display: "standalone",
                start_url: "/",
                scope: "/",

                icons: [
                    {
                        src: "/pwa-192x192.png",
                        sizes: "192x192",
                        type: "image/png",
                    },
                    {
                        src: "/pwa-512x512.png",
                        sizes: "512x512",
                        type: "image/png",
                    },
                ],
            },
        }),
    ],

    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },

    // ==================== Local Development Server ====================

    server: {
        host: "0.0.0.0",
        port: 5173,

        ...(command === "serve"
            ? {
                  origin: "https://www.deliver-dev.com:5173",

                  https: {
                      key: fs.readFileSync("D:/laragon/etc/ssl/laragon.key"),
                      cert: fs.readFileSync("D:/laragon/etc/ssl/laragon.crt"),
                  },

                  cors: true,
              }
            : {}),
    },
}));

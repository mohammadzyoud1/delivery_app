import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";
import fs from "fs";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
        tailwindcss({}),
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

// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            publicDirectory: "public",
            buildDirectory: "build",
            refresh: false,
        }),
    ],


    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },

    build: {
        outDir: "public/build",
        manifest: true,
        rollupOptions: {
            output: {
                entryFileNames: "js/[name]-[hash].js",
                chunkFileNames: "js/[name]-[hash].js",
                assetFileNames: ({ name }) => {
                    if (/\.(css)$/.test(name ?? "")) {
                        return "css/[name]-[hash][extname]";
                    }
                    return "assets/[name]-[hash][extname]";
                },
            },
        },
    },
});

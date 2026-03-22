import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,

            // 👇 ชี้ไปโฟลเดอร์ public ของคุณ
            publicDirectory: 'public_html',
            buildDirectory: 'build',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],

    build: {
        // 👇 ตรงกับ publicDirectory + buildDirectory
        outDir: 'public_html/build',
        emptyOutDir: true,

        assetsDir: 'assets',
        manifest: true,
        chunkSizeWarningLimit: 1000,

        rollupOptions: {
            output: {
                manualChunks: {
                    vue: ['vue'],
                    inertia: ['@inertiajs/vue3'],
                    utils: ['axios'],
                    fontawesome: ['@fortawesome/fontawesome-free'],
                },
            },
        },
    },

    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
})
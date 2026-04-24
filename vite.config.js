import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        // Penting jika Anda ingin akses project dari HP lewat IP lokal (saat offline)
        host: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    // Pastikan Vite menangani aset font dengan benar
    build: {
        chunkSizeWarningLimit: 1600,
    }
});
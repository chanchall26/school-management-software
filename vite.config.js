import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '127.0.0.1',
        origin: 'http://127.0.0.1:5173',
        allowedHosts: 'all',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

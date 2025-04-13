import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/bootstrap-app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // Explicitly define environment variables to be passed to the frontend
    define: {
        'import.meta.env.VITE_PUSHER_APP_KEY': JSON.stringify(process.env.VITE_PUSHER_APP_KEY || process.env.REVERB_APP_KEY || 'app-key'),
        'import.meta.env.VITE_PUSHER_APP_CLUSTER': JSON.stringify(process.env.VITE_PUSHER_APP_CLUSTER || 'mt1'),
        'import.meta.env.VITE_PUSHER_HOST': JSON.stringify(process.env.VITE_PUSHER_HOST || process.env.REVERB_HOST || '127.0.0.1'),
        'import.meta.env.VITE_PUSHER_PORT': JSON.stringify(process.env.VITE_PUSHER_PORT || process.env.REVERB_PORT || '8080'),
        'import.meta.env.VITE_PUSHER_SCHEME': JSON.stringify(process.env.VITE_PUSHER_SCHEME || process.env.REVERB_SCHEME || 'http'),
    },
});

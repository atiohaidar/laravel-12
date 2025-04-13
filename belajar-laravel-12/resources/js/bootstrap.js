import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Get CSRF token from meta tag
 */
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Import Echo and Pusher
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

tokeni = "1|nd7t8g2r4tFlqHAdePOkiAybouWmJmzvom0Vfyve05d8e9df"


// Initialize Laravel Echo with error handling
try {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
        wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
        forceTLS: false,
        encrypted: true,
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'X-CSRF-TOKEN': token.content,
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization': 'Bearer ' + tokeni,

            }
        }
    });
    console.log('Echo initialized successfully');
} catch (error) {
    console.error('Failed to initialize Echo:', error);
}

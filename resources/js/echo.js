import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'vjjcmyeabnptlbhxa72t',         // Use your actual Reverb App Key here
    wsHost: 'vpsocial.site',             // Explicitly set your domain here
    wsPort: 8080,                        // Use 8080 as configured for Reverb
    wssPort: 8080,                       // Use 8080 for secure WebSocket (wss)
    forceTLS: true,                      // Enforce secure connection (wss)
    enabledTransports: ['wss'],    // Allow both ws and wss transports
    disableStats: true                   // Disable stats tracking
});
// import Echo from 'laravel-echo';
//
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
//
// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//     wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

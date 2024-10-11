// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
//
// export default defineConfig({
//     plugins: [
//         laravel({
//             input: [
//                 'resources/css/app.css',
//                 'resources/js/app.js',
//             ],
//             refresh: true,
//         }),
//     ],
// });
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        sourcemap: false, // Change to true if you need source maps for debugging
        outDir: 'public/build', // Explicitly define the output directory for built assets
    },
    server: {
        host: '127.0.0.1',
        port: 5173, // Define a custom port if needed
        watch: {
            usePolling: true, // Use polling to fix issues with file changes not being detected
        },
    },
});

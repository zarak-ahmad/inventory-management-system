import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // CSS font URLs must stay relative to the stylesheet. An absolute
    // /build/... path 404s when Apache serves the app from a subfolder.
    experimental: {
        renderBuiltUrl(filename, { hostType }) {
            if (hostType === 'css') {
                return { relative: true };
            }
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import vuetify from 'vite-plugin-vuetify';
import svgLoader from 'vite-svg-loader';
import { fileURLToPath } from 'node:url';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vueJsx(),
        vuetify({ autoImport: true }),
        Components({
            dirs: ['resources/js/@core/components', 'resources/js/components'],
            dts: true,
            resolvers: [
                componentName => {
                    if (componentName === 'VueApexCharts')
                        return { name: 'default', from: 'vue3-apexcharts', as: 'VueApexCharts' }
                },
            ],
        }),
        AutoImport({
            imports: ['vue', '@vueuse/core', '@vueuse/math', 'pinia'], // Removed vue-router to avoid conflict with inertia
            vueTemplate: true,
            ignore: ['useCookies', 'useStorage'],
        }),
        svgLoader(),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: '192.168.1.83', 
        },
    },
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            '@core': fileURLToPath(new URL('./resources/js/@core', import.meta.url)),
            '@layouts': fileURLToPath(new URL('./resources/js/@layouts', import.meta.url)),
            '@images': fileURLToPath(new URL('./resources/js/assets/images/', import.meta.url)),
            '@styles': fileURLToPath(new URL('./resources/js/assets/styles/', import.meta.url)),
            '@configured-variables': fileURLToPath(new URL('./resources/js/assets/styles/variables/_template.scss', import.meta.url)),
        },
    },
    optimizeDeps: {
        exclude: ['vuetify'],
        entries: [
            './resources/js/**/*.vue',
        ],
    },
});

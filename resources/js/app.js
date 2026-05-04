import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import nprogress from 'nprogress';
import { router } from '@inertiajs/vue3';
import 'nprogress/nprogress.css';

import { registerPlugins } from './@core/utils/plugins';
import '@core/scss/template/index.scss';
import '@layouts/styles/index.scss';

const appName = import.meta.env.VITE_APP_NAME || 'MCI Dashboard';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });
        vueApp.use(plugin);
        registerPlugins(vueApp);
        vueApp.mount(el);
    },
    progress: false,
});

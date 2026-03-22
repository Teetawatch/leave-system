import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';

createInertiaApp({
    title: (title) => title ? `${title} - ระบบบริหารจัดการงานกำลังพล` : 'ระบบบริหารจัดการงานกำลังพล',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.component('Head', Head);
        app.component('Link', Link);
        app.mount(el);
    },
    progress: {
        color: '#0ea5e9',
        showSpinner: true,
    },
});

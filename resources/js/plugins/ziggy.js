import { Ziggy } from '../ziggy';
import { route } from 'ziggy-js'; 

export default {
    install(app) {
        // Wrapper function untuk memanggil route dengan config Ziggy kita
        const routeHelper = (name, params, absolute, config = Ziggy) => {
            return route(name, params, absolute, config);
        };

        // 1. Daftarkan sebagai global property 'route' (bukan $route)
        // Agar konsisten dengan standar Laravel dan template yang sudah ada
        app.config.globalProperties.route = routeHelper;

        // 2. Provide agar bisa di-inject di <script setup>
        // Contoh: const route = inject('route');
        app.provide('route', routeHelper);
    },
};
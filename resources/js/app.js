import "../css/app.css";
import "./bootstrap";

//bootstrap
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

//fontawesome
import "@fortawesome/fontawesome-free/css/all.min.css";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import FloatingVue from "floating-vue";
import "floating-vue/dist/style.css";
import { Link, Head } from "@inertiajs/vue3";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("Link", Link)
            .component("Head", Head)
            .use(FloatingVue);
        // Provide a global showToast fallback
        app.provide('showToast', (msg, type) => alert(msg));
        return app.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});

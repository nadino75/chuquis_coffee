import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes';
import App from '@/App.vue';
import authState from './services/authState';

const app = createApp(App);

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});

router.beforeEach(async (to, from, next) => {
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const requiresGuest = to.matched.some(record => record.meta.requiresGuest);

    if (requiresAuth && !authState.isAuthenticated()) {
        next({ name: 'login', query: { redirect: to.fullPath } });
    } else if (requiresGuest && authState.isAuthenticated()) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

router.afterEach((to) => {
    if (to.meta.title) {
        document.title = to.meta.title + ' - Chuquis Coffee';
    }
});

app.use(router);
app.mount('#app');

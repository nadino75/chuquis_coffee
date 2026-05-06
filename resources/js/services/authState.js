import authService from './auth';

const state = {
    user: window.__APP_DATA?.user || null,
};

export default {
    get user() {
        return state.user;
    },

    set user(val) {
        state.user = val;
    },

    // Primary role name (first role)
    get role() {
        return state.user?.roles?.[0]?.name || null;
    },

    hasRole(roleName) {
        return state.user?.roles?.some(r => r.name === roleName) ?? false;
    },

    hasAnyRole(roleNames) {
        return roleNames.some(name => this.hasRole(name));
    },

    async login(credentials) {
        const res = await authService.login(credentials);
        state.user = res.data.user || res.data;
        return res;
    },

    async register(data) {
        const res = await authService.register(data);
        state.user = res.data.user || res.data;
        return res;
    },

    async logout() {
        try {
            await authService.logout();
        } catch (e) {}
        state.user = null;
    },

    async refreshUser() {
        try {
            const res = await authService.getUser();
            state.user = res.data;
        } catch (e) {
            state.user = null;
            throw e;
        }
        return state.user;
    },

    isAuthenticated() {
        return state.user !== null;
    },
};

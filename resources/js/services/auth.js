import http from './http';

export default {
    login(credentials) {
        return http.post('/login', credentials);
    },
    register(data) {
        return http.post('/register', data);
    },
    forgotPassword(email) {
        return http.post('/forgot-password', { email });
    },
    resetPassword(data) {
        return http.post('/reset-password', data);
    },
    logout() {
        return http.post('/logout');
    },
    getUser() {
        return http.get('/api/user');
    },
};

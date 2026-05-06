import http from './http';

export default {
    get() {
        return http.get('/api/profile');
    },
    update(data) {
        return http.patch('/api/profile', data);
    },
    updatePassword(data) {
        return http.put('/password', {
            current_password: data.current_password,
            password: data.password,
            password_confirmation: data.password_confirmation,
        });
    },
    destroy(data) {
        return http.delete('/api/profile', { data });
    },
};

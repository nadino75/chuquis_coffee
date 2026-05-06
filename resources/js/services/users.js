import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/users', params);
    },
    show(id) {
        return http.get(`/api/users/${id}`);
    },
    store(data) {
        return http.post('/api/users', data);
    },
    update(id, data) {
        return http.put(`/api/users/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/users/${id}`);
    },
};

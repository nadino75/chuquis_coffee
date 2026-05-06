import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/roles', params);
    },
    show(id) {
        return http.get(`/api/roles/${id}`);
    },
    store(data) {
        return http.post('/api/roles', data);
    },
    update(id, data) {
        return http.put(`/api/roles/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/roles/${id}`);
    },
};

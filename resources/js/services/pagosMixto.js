import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/pagos-mixto', params);
    },
    show(id) {
        return http.get(`/api/pagos-mixto/${id}`);
    },
    store(data) {
        return http.post('/api/pagos-mixto', data);
    },
    update(id, data) {
        return http.put(`/api/pagos-mixto/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/pagos-mixto/${id}`);
    },
};

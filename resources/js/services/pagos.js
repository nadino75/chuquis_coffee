import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/pagos', params);
    },
    show(id) {
        return http.get(`/api/pagos/${id}`);
    },
    store(data) {
        return http.post('/api/pagos', data);
    },
    update(id, data) {
        return http.put(`/api/pagos/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/pagos/${id}`);
    },
};

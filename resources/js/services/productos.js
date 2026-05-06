import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/productos', params);
    },
    show(id) {
        return http.get(`/api/productos/${id}`);
    },
    store(data) {
        return http.post('/api/productos', data);
    },
    update(id, data) {
        return http.put(`/api/productos/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/productos/${id}`);
    },
};

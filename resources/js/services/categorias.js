import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/categorias', params);
    },
    show(id) {
        return http.get(`/api/categorias/${id}`);
    },
    store(data) {
        return http.post('/api/categorias', data);
    },
    update(id, data) {
        return http.put(`/api/categorias/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/categorias/${id}`);
    },
};

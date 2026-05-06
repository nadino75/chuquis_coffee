import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/marcas', params);
    },
    show(id) {
        return http.get(`/api/marcas/${id}`);
    },
    store(data) {
        return http.post('/api/marcas', data);
    },
    update(id, data) {
        return http.put(`/api/marcas/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/marcas/${id}`);
    },
};

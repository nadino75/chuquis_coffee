import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/tipos', params);
    },
    show(id) {
        return http.get(`/api/tipos/${id}`);
    },
    store(data) {
        return http.post('/api/tipos', data);
    },
    update(id, data) {
        return http.put(`/api/tipos/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/tipos/${id}`);
    },
};

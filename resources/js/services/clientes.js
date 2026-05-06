import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/clientes', params);
    },
    show(ci) {
        return http.get(`/api/clientes/${ci}`);
    },
    store(data) {
        return http.post('/api/clientes', data);
    },
    update(ci, data) {
        return http.put(`/api/clientes/${ci}`, data);
    },
    destroy(ci) {
        return http.delete(`/api/clientes/${ci}`);
    },
};

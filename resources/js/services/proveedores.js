import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/proveedores', params);
    },
    show(id) {
        return http.get(`/api/proveedores/${id}`);
    },
    store(data) {
        return http.post('/api/proveedores', data);
    },
    update(id, data) {
        return http.put(`/api/proveedores/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/proveedores/${id}`);
    },
};

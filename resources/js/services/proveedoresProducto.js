import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/proveedores-productos', params);
    },
    show(id) {
        return http.get(`/api/proveedores-productos/${id}`);
    },
    store(data) {
        return http.post('/api/proveedores-productos', data);
    },
    update(id, data) {
        return http.put(`/api/proveedores-productos/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/proveedores-productos/${id}`);
    },
};

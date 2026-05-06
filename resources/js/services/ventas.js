import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/ventas', params);
    },
    show(id) {
        return http.get(`/api/ventas/${id}`);
    },
    store(data) {
        return http.post('/api/ventas', data);
    },
    update(id, data) {
        return http.put(`/api/ventas/${id}`, data);
    },
    destroy(id) {
        return http.delete(`/api/ventas/${id}`);
    },
    porCliente(clienteCi) {
        return http.get(`/api/ventas/cliente/${clienteCi}`);
    },
    porProducto(productoId) {
        return http.get(`/api/ventas/producto/${productoId}`);
    },
};

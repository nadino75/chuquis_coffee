import http from './http';

export default {
    index(params = {}) {
        return http.get('/api/reportes', params);
    },
    datos(params = {}) {
        return http.get('/api/reportes/datos', params);
    },
    descargarPdf(params = {}) {
        return http.get('/api/reportes/descargar-pdf', { ...params, responseType: 'blob' });
    },
};

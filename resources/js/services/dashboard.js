import http from './http';

export default {
    getDatosDashboard() {
        return http.get('/api/dashboard/datos');
    },
};

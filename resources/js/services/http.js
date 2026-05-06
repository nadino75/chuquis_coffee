import api from '@/services/api';

export default {
    get(endpoint, params = {}) {
        return api.get(endpoint, { params });
    },
    post(endpoint, data) {
        return api.post(endpoint, data);
    },
    put(endpoint, data) {
        return api.put(endpoint, data);
    },
    patch(endpoint, data) {
        return api.patch(endpoint, data);
    },
    delete(endpoint) {
        return api.delete(endpoint);
    },
};

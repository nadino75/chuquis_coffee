import axios from 'axios';
import authState from './authState';

const api = axios.create({
    baseURL: window.location.origin,
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    api.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            authState.user = null;
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;

import axios from 'axios';

export default {
    async login(payload) {
        return await axios.post('/api/auth/login', payload);
    },

    async register(payload) {
        return await axios.post('/api/auth/register', payload)
    },

    async logout() {
        return await axios.get('/api/auth/logout');
    },

    async fetchCurrentUser() {
        return await axios.get('/api/users/me');
    }
}

import axios from 'axios';

export default {
    async fetchUsers() {
        return await axios.get('/api/users');
    },

    async storeUser(payload) {
        return await axios.post('/api/users', payload);
    },

    async updateUser(payload) {
        return await axios.put(`/api/users/${payload.id}`, {
            first_name: payload.firstName,
            last_name: payload.lastName,
            email: payload.email,
            password: payload.password,
            password_confirmation: payload.password_confirmation
        });
    },

    async deleteUser(userId) {
        return await axios.delete(`/api/users/${userId}`);
    },

    async fetchMe() {
        return await axios.get('/api/users/me');
    }
}

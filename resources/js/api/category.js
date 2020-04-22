import axios from 'axios';

export default {
    async fetchCategories(payload) {
        return await axios.get('/api/categories', {
            params: payload
        });
    },

    async fetchCategory(id) {
        return await axios.get(`/api/categories/${id}`);
    },

    async storeCategory(payload) {
        return await axios.post('/api/categories', payload);
    },

    async updateCategory(id, payload) {
        return await axios.put(`/api/categories/${id}`, payload);
    },

    async deleteCategory(id) {
        return await axios.delete(`/api/categories/${id}`);
    }
}

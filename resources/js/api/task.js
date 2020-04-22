import axios from 'axios';

export default {
    async fetchTasks(payload) {
        return await axios.get('/api/tasks', {
            params: payload
        });
    },

    async showTask(taskId) {
        return await axios.get(`/api/tasks/${taskId}`);
    },

    async storeTask(payload) {
        return await axios.post('/api/tasks', payload);
    },

    async updateTask(taskId, payload) {
        return await axios.put(`/api/tasks/${taskId}`, payload);
    }
}

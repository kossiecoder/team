import axios from 'axios';

export default {
    async fetchPriorityLevels() {
        return await axios.get('/api/priority-levels');
    }
}

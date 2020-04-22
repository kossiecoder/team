import axios from 'axios';

export default {
    async storeComment(payload) {
        return await axios.post('/api/comments', payload);
    }
}

import axios from 'axios';

export default {
    // channel message
    async updateChannelMessagesToRead(payload) {
        return await axios.put('/api/channel-messages/read', payload);
    },

    async fetchChannelMessages(payload) {
        return await axios.get('/api/channel-messages/' + payload.channelId, {params: {loadMoreNumber: payload.loadMoreNumber}});
    },

    async storeChannelMessage(payload){
        return await axios.post('/api/channel-messages', payload);
    },

    // message
    async updateMessagesToRead(payload) {
        return axios.put('/api/messages/read', payload);
    },

    async fetchMessages(payload) {
        return await axios.get('/api/messages/' + payload.userId, {params: { loadMoreNumber: payload.loadMoreNumber }});
    },

    async storePrivateMessage(payload){
        return await axios.post('/api/messages', payload);
    },

    // channel
    async fetchMyChannels() {
        return await axios.get('/api/channels/my-channels');
    },

    async addPeopleToChannel({ selectedUsers, channelId }) {
        return await axios.post('/api/channels/add-people', {
            selectedUsers: selectedUsers.map(user => user.id),
            channelId: channelId
        });
    },

    async showChannel(channelId) {
        return await axios.get(`/api/channels/${channelId}`);
    },

    async channelCreatedEvent({ channelId }){
        return await axios.post('/api/channels/created', {
            channelId: channelId
        });
    },

    async updateChannel(channelId, payload) {
        return await axios.put(`/api/channels/${channelId}`, payload);
    },

    async storeChannel({ name, description, selectedUsers }) {
        return await axios.post('/api/channels', {
            name,
            description,
            selectedUsers
        })
    },

    async deleteChannel(channelId) {
        return await axios.delete(`/api/channels/${channelId}`)
    },

    async checkChannelAuth(channelId) {
        return await axios.get(`/api/channels/auth/${channelId}`);
    },

     async checkIfChannelExists(payload) {
         return await axios.get('/api/channels/check', {
             params: payload
         });
     }
}

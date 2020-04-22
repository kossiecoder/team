import axios from 'axios';

export default {
    async fetchMyPermissions() {
        return await axios.get('/api/permissions/me');
    },

    async fetchUserPermissions(userId){
        return await axios.get(`/api/permissions/${userId}`);
    },

    async updateUserPermission(userId, payload) {
        return await axios.put(`/api/permissions/${userId}`, payload);
    },

    async checkIfUserHasPermission(permissionCode) {
        return await axios.get('/api/permissions/check', {
            params: {
                code: permissionCode
            }
        })
    }
}

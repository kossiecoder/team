import userApi from '@/api/user';

export default {
    async fetchUsers({ commit }) {
        try {
            const res = await userApi.fetchUsers();
            commit('update_users', res.data.users);
            commit('notification/updateNewMessage', res.data.users.some(user => user.from_messages_count > 0), { root: true });
            return res;
        } catch (err) {
            throw err;
        }
    },
}

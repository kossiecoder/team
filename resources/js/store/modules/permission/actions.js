import permissionApi from '@/api/permission';

export default {
    async fetchMyPermissions({ commit }) {
        try {
            const res = await permissionApi.fetchMyPermissions();
            commit('update_my_permissions', res.data.permissions);
            return res;
        } catch (err) {
            throw err;
        }
    },
}

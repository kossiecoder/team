import authApi from '@/api/auth';

export default {
    async login({ commit }, payload) {
        commit('auth_request');
        try {
            const res = await authApi.login(payload);
            const token = res.data.token.access_token;
            const user = res.data.user;

            localStorage.setItem('token', token);
            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

            commit('auth_success', {token, user});

            return res;
        } catch (err) {
            commit('auth_error');
            localStorage.removeItem('token');
            throw err;
        }
    },

    async register({ commit }, payload) {
        commit('auth_request');
        try {
            const res = await authApi.register(payload);
            const token = res.data.token.access_token;
            const user = res.data.user;

            localStorage.setItem('token', token);
            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
            commit('auth_success', token, user);

            return res;
        } catch (err) {
            commit('auth_error', err);
            localStorage.removeItem('token');
            throw err;
        }
    },
    async logout({ commit }) {
        commit('logout');
        try {
            await authApi.logout();
            localStorage.removeItem('token');
            delete window.axios.defaults.headers.common['Authorization'];
        } catch (err) {
            throw err;
        }
    },
    async fetchCurrentUser({ commit }) {
        try {
            const res = await authApi.fetchCurrentUser();
            commit('update_current_user', res.data.me);
            return res.data.me;
        } catch (err) {
            throw err;
        }
    },
}

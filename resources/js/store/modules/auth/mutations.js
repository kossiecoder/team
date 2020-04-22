export default {
    auth_request(state) {
        state.status = 'loading';
    },

    auth_success(state, data) {
        state.me = data.user;
        state.status = 'success';
        state.token = data.token;
    },

    auth_error(state) {
        state.status = 'error';
    },

    update_current_user(state, user){
        state.me = user;
    },

    logout(state) {
        state.status = '';
        state.token = '';
    },
}

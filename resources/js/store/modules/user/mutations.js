export default {
    update_users(state, users) {
        state.users = users;
    },

    reset_private_chat_messages_count(state, chatUserId) {
        const index = state.users.findIndex(user => user.id === parseInt(chatUserId));

        state.users[index].from_messages_count = 0;
    },

    update_new_private_message_count(state, chatUserId) {
        const index = state.users.findIndex(user => user.id === parseInt(chatUserId));

        state.users[index].from_messages_count++;
    }
}

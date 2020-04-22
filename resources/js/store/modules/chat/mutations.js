export default {
    reset_private_chat_messages_count(state, chatUserId) {
        const index = state.users.findIndex(user => user.id === parseInt(chatUserId));

        state.users[index].from_messages_count = 0;

        state.notification.newMessage = state.users.some(user => user.from_messages_count > 0);
    },
    update_create_channel_dialog_status(state, status) {
        state.createChannelDialog = status;
    },

    update_add_people_dialog_status(state, status) {
        state.addPeopleDialog = status;
    },

    update_my_channels(state, myChannels) {
        state.myChannels = myChannels;
    },

    add_to_my_channels(state, newChannel) {
        state.myChannels.push(newChannel);
    },

    update_chat_data(state, {moreMessageExists, messages}) {
        state.chat.loadMoreNumber++;
        state.chat.moreMessageExists = moreMessageExists;
        state.chat.messages.unshift(...messages)
    },
    add_new_message(state, message) {
        state.chat.messages.push(message);
    },

    update_new_private_message_count(state, chatUserId) {
        const index = state.users.findIndex(user => user.id === parseInt(chatUserId));

        state.users[index].from_messages_count++;

        state.notification.newMessage = true;
    },

    reset_chat_data(state) {
        state.chat.loadMoreNumber = 0;
        state.chat.moreMessageExists = false;
        state.chat.messages = [];
    },
    update_channel_read_status(state, {channelId, status}) {
        const index = state.myChannels.findIndex(channel => parseInt(channel.id) === parseInt(channelId));
        state.myChannels[index].pivot.is_read = status;
    },

    // mini chat
    update_mini_chat_page(state, { page, id=''}) {
        state.miniChat.page = page;
        state.miniChat.id = id;
    },
}

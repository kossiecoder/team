import chatApi from '@/api/chat';

export default {
    async updateChannelMessagesToRead({ commit, state }, payload) {
        try {
            const res = await chatApi.updateChannelMessagesToRead(payload);

            commit('update_channel_read_status', {
                channelId: res.data.channelId,
                status: 1
            });

            commit('notification/updateNewChannelMessage', state.myChannels.some(channel => channel.pivot.is_read === 0), { root: true });
            return res;
        } catch (err) {
            throw err;
        }
    },

    async updateMessagesToRead({ commit, rootState }, payload) {
        try {
            const res = await chatApi.updateMessagesToRead(payload);
            commit('user/reset_private_chat_messages_count', res.data.chatUserId, { root: true});
            commit('notification/updateNewMessage', rootState.user.users.some(user => user.from_messages_count > 0), { root: true });
            return res;
        } catch (err) {
            throw err;
        }
    },
    toggleCreateChannelDialog({commit}, status) {
        commit('update_create_channel_dialog_status', status);
    },

    toggleAddPeopleDialog({commit}, status) {
        commit('update_add_people_dialog_status', status);
    },

    async fetchMyChannels({commit}) {
        try {
            const res = await chatApi.fetchMyChannels();
            commit('update_my_channels', res.data.myChannels);
            return res;
        } catch (err) {
            throw err;
        }
    },

    async fetchMessages({commit}, payload) {
        try {
            const res = await chatApi.fetchMessages(payload);

            commit('update_chat_data', {
                moreMessageExists: !(res.data.messages.length < res.data.noOfMessages),
                messages: _.orderBy(res.data.messages, 'id')
            });
            return res;
        } catch (err) {
            throw err;
        }
    },

    async fetchChannelMessages({commit}, payload) {
        try {
            const res = await chatApi.fetchChannelMessages(payload);
            commit('update_chat_data', {
                moreMessageExists: !(res.data.messages.length < res.data.noOfMessages),
                messages: _.orderBy(res.data.messages, 'id')
            });
            return res;
        } catch (err) {
            throw err;
        }
    },
}

import Echo from "laravel-echo";
import snackBarMixin from "@/mixins/snackBarMixin";

export default {
    mixins: [snackBarMixin],

    computed: {
        isLoggedIn() {
            return this.$store.getters['auth/isLoggedIn'];
        }
    },

    watch: {
        isLoggedIn (val) {
            if(val) {
                this.initSetup()
            }
        }
    },

    created() {
        if(this.isLoggedIn) {
            this.initSetup();
        }
    },

    methods: {
        initSetup() {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                encrypted: true,
                auth: {
                    headers: {
                        Authorization: 'Bearer ' + this.$store.state.auth.token
                    }
                }
            });

            if(this.$store.state.user.users.length === 0) {
                this.$store.dispatch('user/fetchUsers');
            }

            // listen to channelCreated event - when added to channel
            window.Echo.channel('channels').listen('ChannelCreated', e => {
                if(e.user_ids.some(userId => userId === this.currentUser.id)) {
                    const channel = e.channel;
                    this.openSnackBar('You have been added to a channel #' + channel.name);
                    this.$store.dispatch('chat/fetchMyChannels');
                    window.Echo.private('channelChat_' + channel.id).listen('ChatChannelSent', e =>{
                        const message = e.message;
                        this.actionChannelNewMessage(channel, message);
                    });
                }
            });

            this.$store.dispatch('auth/fetchCurrentUser').then(res => {
                const currentUserId = res.id;
                this.$store.commit('notification/set_initial_notification_count', res.unread_notifications_count);
                this.$store.commit('notification/set_initial_notifications', res.notifications);
                window.Echo.private('privateChat_' + currentUserId).listen('ChatPrivateSent', e => {
                    const message = e.message;
                    this.actionPrivateNewMessage(message, currentUserId);
                });

                // listen to user broadcast
                window.Echo.private('App.Models.User.' + currentUserId).notification(notification => {
                    this.$store.commit('notification/add_notification_count', 1);
                    this.$store.commit('notification/update_notifications', notification.notification);
                });
            });
            this.$store.dispatch('chat/fetchMyChannels').then(res => {
                res.data.myChannels.forEach(channel => {
                    window.Echo.private('channelChat_' + channel.id).listen('ChatChannelSent', e =>{
                        const message = e.message;
                        this.actionChannelNewMessage(channel, message);
                    });
                });
            }).catch(err => {
                console.log(err);
            });

            this.$store.dispatch('permission/fetchMyPermissions');
        },

        actionChannelNewMessage(channel, message) {
            if(this.isOnChannelPage(message)  || this.isOnMiniChatChannel(message)) {
                this.$store.commit('chat/add_new_message', message);
            }
            if(this.isOnOtherChannelPage(message) || this.isNotOnChannelPageAndNotOnMiniChatChannel(message)) {
                this.$store.commit('chat/update_channel_read_status', {
                    channelId: channel.id,
                    status: 0
                });
                this.$store.commit('notification/updateNewChannelMessage', true);
            }
        },

        isOnChannelPage(message) {
            return this.$route.name === 'chat-main-channel' &&
                parseInt(this.$route.params.id) === parseInt(message.channel_id);
        },

        isOnOtherChannelPage(message) {
            return this.$route.name === 'chat-main-channel' &&
                parseInt(this.$route.params.id) !== parseInt(message.channel_id);
        },

        isNotOnChannelPageAndNotOnMiniChatChannel(message) {
            return this.$route.name !== 'chat-main-channel' &&
                ((this.$store.state.chat.miniChat.page === 'channel' && parseInt(this.$store.state.chat.miniChat.id) !== parseInt(message.channel_id)) ||
                    this.$store.state.chat.miniChat.page !== 'channel' );
        },

        isOnMiniChatChannel(message) {
            return this.$route.name !== 'chat-main-channel' &&
                this.$store.state.chat.miniChat.page === 'channel' &&
                parseInt(this.$store.state.chat.miniChat.id) === parseInt(message.channel_id);
        },

        actionPrivateNewMessage(message, currentUserId) {
            if(this.isOnPrivatePage(message) || this.isOnMiniChatPrivate(message)) {
                this.$store.commit('chat/add_new_message', message);
            }
            if(this.isOnOtherPrivatePage(message, currentUserId) || this.isNotOnPrivatePageAndNotOnMiniChatPrivate(message)) {
                this.$store.commit('user/update_new_private_message_count', message.from.id);
                this.$store.commit('notification/updateNewMessage', true);
            }
        },

        isOnPrivatePage(message) {
            return this.$route.name === 'chat-main-private' &&
                parseInt(this.$route.params.id) === parseInt(message.from.id);
        },

        isOnMiniChatPrivate(message) {
            return this.$route.name !== 'chat-main-private' &&
                this.$store.state.chat.miniChat.page === 'private' &&
                parseInt(this.$store.state.chat.miniChat.id) === parseInt(message.from.id);
        },

        isOnOtherPrivatePage(message, currentUserId) {
            return this.$route.name === 'chat-main-private' && parseInt(this.$route.params.id) !== parseInt(message.from.id) && parseInt(this.$route.params.id) !== currentUserId;
        },

        isNotOnPrivatePageAndNotOnMiniChatPrivate(message) {
            return this.$route.name !== 'chat-main-private' &&
                ((this.$store.state.chat.miniChat.page === 'private' && parseInt(this.$store.state.chat.miniChat.id) !== parseInt(message.from.id)) ||
                    this.$store.state.chat.miniChat.page !== 'private');
        }
    },
}

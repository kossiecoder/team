import chatApi from '@/api/chat';

export default {
    data() {
        return {
            message: {},
        }
    },

    async created() {
        try {
            const res = await chatApi.checkChannelAuth(this.channelId);
            if (!res.data.auth) {
                await this.$router.push({name: 'home'});
            } else {
                this.auth = true;
                await this.$store.dispatch('chat/updateChannelMessagesToRead', {
                    channelId: this.channelId
                });
            }
        } catch (err) {
            console.log(err);
        }
    },

    methods: {
        updateMessage(message) {
            this.message = message;
        }
    },
}

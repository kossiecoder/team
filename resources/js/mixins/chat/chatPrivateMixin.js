export default {
    data() {
        return {
            message: {},
        }
    },

    created() {
        this.$store.dispatch('chat/updateMessagesToRead', {
            chatUserId: this.chatUserId
        });
    },

    methods: {
        updateMessage(message) {
            this.message = message;
        }
    },
}

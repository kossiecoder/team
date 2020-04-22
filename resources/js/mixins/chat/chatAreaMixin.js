export default {

    props: {
        message: {
            type: Object
        }
    },

    updated() {
        this.loadMoreNumber === 1 ? this.scrollToBottom() : this.scrollToTop();
    },
    methods: {
        scrollToBottom() {
            this.$refs.chatScroll.scrollTop = this.$refs.chatScroll.scrollHeight;
        },

        scrollToTop() {
            this.$refs.chatScroll.scrollTop = 0;
        },

        sortById(data) {
            return _.orderBy(data, 'id');
        }
    },

    computed: {
        userId() {
            return this.$route.params.id;
        },
        messages() {
            return this.$store.state.chat.chat.messages;
        },
        loadMoreNumber() {
            return this.$store.state.chat.chat.loadMoreNumber;
        },
        moreMessageExists() {
            return this.$store.state.chat.chat.moreMessageExists;
        }
    },

    watch: {
        '$route' (to, from) {
            this.getMessages();
        },
        message(newVal) {
            this.scrollToBottom();
            this.messages.push(newVal)
        }
    }
}

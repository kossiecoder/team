export default {
    computed: {
        currentUser() {
            return this.$store.state.auth.me;
        },
    },
}

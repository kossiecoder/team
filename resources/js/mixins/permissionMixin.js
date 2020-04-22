export default {
    computed: {
        myPermissions() {
            return this.$store.state.permission.myPermissions;
        },
    },

    methods: {
        hasPermission(code) {
            return this.$store.state.permission.myPermissions.some(permission => permission === code);
        }
    }
}

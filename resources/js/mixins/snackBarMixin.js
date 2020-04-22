export default {
    methods: {
        openSnackBar(message) {
            this.$store.commit('snackbar/updateSnackBarMessage', message);
            this.$store.commit('snackbar/toggleSnackBarStatus', true);
        },
        closeSnackBar() {
            this.$store.commit('snackbar/updateSnackBarMessage', '');
        }
    }
}

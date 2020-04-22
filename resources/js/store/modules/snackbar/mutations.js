export default {
    toggleSnackBarStatus(state, status) {
        state.snackBar.status = status;
    },
    updateSnackBarMessage(state, message) {
        state.snackBar.message = message;
    },
}

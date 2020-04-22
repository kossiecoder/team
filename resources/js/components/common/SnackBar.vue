<template>
    <v-snackbar
        v-model="show"
        multi-line
        top
    >
        {{ message }}
        <v-btn
            color="pink"
            text
            @click="close"
        >
            Close
        </v-btn>
    </v-snackbar>
</template>

<script>
    import snackBarMixin from "@/mixins/snackBarMixin";

    export default {
        mixins: [snackBarMixin],

        data() {
            return {
                show: false,
                message: '',
                colour: ''
            }
        },

        created() {
            this.$store.watch(state => state.snackbar.snackBar.message, () => {
                const msg = this.$store.state.snackbar.snackBar.message;
                if(msg !== '') {
                    this.message = this.$store.state.snackbar.snackBar.message;
                    this.show = true;
                    this.closeSnackBar();
                }
            });
        },

        methods: {
            close() {
                this.show = false;
                this.closeSnackBar();
            }
        },
    }
</script>

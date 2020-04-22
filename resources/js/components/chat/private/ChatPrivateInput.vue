<template>
    <v-text-field
        v-model="message"
        outlined
        single-line
        hide-details
        placeholder="Type message"
        height="1"
        @keyup.enter="sendMessage"
    ></v-text-field>
</template>
<script>
    import chatApi from "@/api/chat";

    export default {
        data() {
            return {
                message: ''
            }
        },

        computed: {
            userId() {
                return this.$route.params.id;
            }
        },

        methods: {
            async sendMessage() {
                if(this.message) {
                    try {
                        const res = await chatApi.storePrivateMessage({
                            message: this.message,
                            to: this.userId
                        });
                        const message = res.data.message;
                        this.$emit('message', message);
                        this.message = '';
                    } catch (err) {
                        console.log(err.response.data.message[0])
                    }
                }
            }
        },

    }
</script>

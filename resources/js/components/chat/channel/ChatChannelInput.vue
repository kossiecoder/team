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
            channelId() {
                return this.$route.params.id;
            }
        },

        methods: {
            async sendMessage() {
                if(this.message) {
                    try {
                        const res = await chatApi.storeChannelMessage({
                            message: this.message,
                            channel_id: this.channelId
                        });
                        const message = res.data.message;
                        this.$emit('message', message);
                        this.message = '';
                    } catch (err) {
                        console.log(err.response);
                    }
                }
            }
        },
    }
</script>

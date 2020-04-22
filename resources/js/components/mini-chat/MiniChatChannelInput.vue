<template>
    <div>
        <v-text-field
            v-model="message"
            placeholder="Type message.."
            solo
            hide-details
            @keyup.enter="sendMessage"
        ></v-text-field>
    </div>
</template>
<script>
    import chatApi from "@/api/chat";

    export default {
        props: ['channelId'],

        data() {
            return {
                message: ''
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
        }
    }
</script>

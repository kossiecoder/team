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
        props: {
            chatUserId: {
                type: Number,
                required: true
            }
        },

        data() {
            return {
                message: ''
            }
        },
        methods: {
            async sendMessage() {
                if(this.message) {
                    try {
                        const res = await chatApi.storePrivateMessage({
                            message: this.message,
                            to: this.chatUserId
                        });
                        const message = res.data.message;
                        this.$emit('message', message);
                        this.message = '';
                    } catch (err) {
                        console.log(err.response.data.message[0])
                    }
                }
            }
        }
    }
</script>

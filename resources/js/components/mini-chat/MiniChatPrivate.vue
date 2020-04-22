<template>
    <div class="d-flex flex-column flex-grow-1 scroll">
        <MiniChatPrivateArea
            :chat-user-id="chatUserId"
            :message="message"
        />
        <MiniChatPrivateInput
            :chat-user-id="chatUserId"
            @message="updateMessage"
        />
    </div>
</template>
<script>
    import ChatPrivateMixin from '@/mixins/chat/chatPrivateMixin';
    import MiniChatPrivateArea from '@/components/mini-chat/MiniChatPrivateArea';
    import MiniChatPrivateInput from '@/components/mini-chat/MiniChatPrivateInput';

    export default {
        components: {
            MiniChatPrivateArea,
            MiniChatPrivateInput
        },

        mixins: [ChatPrivateMixin],

        props: {
            chatUserId: {
                type: Number,
                required: true
            }
        },
    }
</script>

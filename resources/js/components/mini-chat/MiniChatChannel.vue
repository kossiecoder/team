<template>
    <div class="d-flex flex-column flex-grow-1 scroll">
        <MiniChatChannelArea
            :channel-id="channelId"
            :message="message"
        />
        <MiniChatChannelInput
            :channel-id="channelId"
            @message="updateMessage"
        />
    </div>
</template>
<script>
    import MiniChatChannelArea from '@/components/mini-chat/MiniChatChannelArea';
    import MiniChatChannelInput from '@/components/mini-chat/MiniChatChannelInput';
    import ChatChannelMixin from '@/mixins/chat/chatChannelMixin';

    export default {
        components: {
            MiniChatChannelArea,
            MiniChatChannelInput
        },

        mixins: [ChatChannelMixin],

        props: {
            channelId: {
                type: Number,
                required: true
            }
        },
    }
</script>

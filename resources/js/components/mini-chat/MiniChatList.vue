<template>
    <div class="flex-grow-1 d-flex flex-column scroll">
        <MiniChatListChannel />
        <MiniChatListPrivate />
    </div>
</template>

<script>
    import MiniChatListChannel from '@/components/mini-chat/MiniChatListChannel'
    import MiniChatListPrivate from '@/components/mini-chat/MiniChatListPrivate'
    export default {
        components: {
            MiniChatListChannel,
            MiniChatListPrivate,
        }
    }
</script>

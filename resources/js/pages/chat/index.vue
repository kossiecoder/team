<template>
    <ChatLayout>
        <ChatSideMenu />
    </ChatLayout>
</template>
<script>
    import ChatLayout from "@/components/ui/chat/ChatLayout";
    import ChatSideMenu from '@/components/chat/ChatSideMenu'

    export default {
        components: {
            ChatSideMenu,
            ChatLayout
        },
    }
</script>

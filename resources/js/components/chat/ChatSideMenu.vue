<template>
    <v-card height="100">
        <ChatSideMenuChannels :added-channel="channel" />
        <v-divider></v-divider>
        <ChatSideMenuDirect />
        <ChatSideMenuCreateChannel @addedChannel="addedChannel" />
    </v-card>
</template>
<script>
    import ChatSideMenuChannels from '@/components/chat/ChatSideMenuChannels';
    import ChatSideMenuDirect from '@/components/chat/ChatSideMenuDirect';
    import ChatSideMenuCreateChannel from '@/components/chat/ChatSideMenuCreateChannel';

    export default {
        components: {
            ChatSideMenuChannels,
            ChatSideMenuDirect,
            ChatSideMenuCreateChannel
        },

        data() {
            return {
                dialog: false,
                notifications: false,
                sound: true,
                widgets: false,
                channel: {}
            }
        },

        methods: {
            addedChannel(value) {
                this.channel = value;
            }
        }
    }
</script>

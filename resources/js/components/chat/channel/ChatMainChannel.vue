<template>
    <div class="d-flex fill-height">
        <ChatAreaLayout>
            <template v-slot:content>
                <ChatMainChannelArea :message="message" />
            </template>
            <template v-slot:input>
                <ChatChannelInput @message="updateMessage" />
            </template>
        </ChatAreaLayout>
        <div class="scroll flex xs2 fill-height">
            <ChatChannelSideMenu />
        </div>
    </div>
</template>
<script>
    import ChatChannelInput from '@/components/chat/channel/ChatChannelInput';
    import ChatMainChannelArea from '@/components/chat/channel/ChatMainChannelArea';
    import ChatChannelSideMenu from '@/components/chat/channel/ChatChannelSideMenu';
    import store from '@/store';
    import ChatChannelMixin from '@/mixins/chat/chatChannelMixin';
    import ChatAreaLayout from "@/components/ui/chat/ChatAreaLayout";

    export default {
        components: {
            ChatChannelInput,
            ChatMainChannelArea,
            ChatChannelSideMenu,
            ChatAreaLayout
        },

        mixins: [ChatChannelMixin],

        data() {
            return {
                auth: false
            }
        },

        computed: {
            channelId() {
                return this.$route.params.id;
            }
        },

        beforeRouteEnter (to, from, next) {
            if (store.state.chat.myChannels.length === 0) {
                store.dispatch('chat/fetchMyChannels')
                    .then(next);
            } else {
                next()
            }
        },

    }
</script>

<template>
    <ChatAreaLayout>
        <template v-slot:content>
            <ChatMainPrivateArea :message="message" />
        </template>
        <template v-slot:input>
            <ChatPrivateInput @message="updateMessage" />
        </template>
    </ChatAreaLayout>
</template>
<script>
    import ChatAreaLayout from "@/components/ui/chat/ChatAreaLayout";
    import ChatPrivateInput from '@/components/chat/private/ChatPrivateInput';
    import ChatMainPrivateArea from '@/components/chat/private/ChatMainPrivateArea';
    import ChatPrivateMixin from '@/mixins/chat/chatPrivateMixin';
    import store from '@/store';

    export default {
        components: {
            ChatPrivateInput,
            ChatMainPrivateArea,
            ChatAreaLayout
        },

        mixins: [ChatPrivateMixin],



        computed: {
            chatUserId() {
                return this.$route.params.id;
            }
        },

        beforeRouteEnter (to, from, next) {
            if (store.state.user.users.length === 0) {
                store.dispatch('user/fetchUsers')
                    .then(next);
            } else {
                next()
            }
        },
    }
</script>

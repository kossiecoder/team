<template>
    <div
        ref="chatScroll"
        class="scroll d-flex flex-column pa-1 flex-grow-1"
    >
        <p
            v-if="moreMessageExists"
            class="text-sm-center blue--text"
        >
            <a @click="getMessages()">Load more messages..</a>
        </p>
        <p v-else-if="parseInt(currentUser.id) === parseInt(userId)">
            <strong>This is your space.</strong> Draft messages, list your to-dos or keep links.
        </p>
        <p v-else>
            This is the very beginning of your direct message history with this person.
        </p>
        <ChatPrivateMessage
            v-for="(message, index) in messages"
            :key="index"
            :message="message"
            :previous="messages[index-1]"
            :index="index"
        />
    </div>
</template>

<script>
    import ChatPrivateMessage from '@/components/chat/private/ChatPrivateMessage';
    import ChatAreaMixin from '@/mixins/chat/chatAreaMixin';
    import userMixin from "@/mixins/userMixin";

    export default {
        components: {
            ChatPrivateMessage
        },

        mixins: [
            ChatAreaMixin,
            userMixin
        ],

        props: {
            chatUserId: {
                type: Number,
                required: true
            }
        },

        created() {
            this.$store.commit('chat/reset_chat_data');
            this.getMessages();
        },

        methods: {
            getMessages() {
                this.$store.dispatch('chat/fetchMessages', {
                    userId: this.chatUserId,
                    loadMoreNumber: this.loadMoreNumber
                });
            }
        }
    }
</script>

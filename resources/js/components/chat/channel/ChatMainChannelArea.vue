<template>
    <div
        ref="chatScroll"
        v-chat-scroll="{always: false, smooth: true}"
        class="flex-grow-1 scroll pa-2"
    >
        <p
            v-if="moreMessageExists"
            class="text-sm-center blue--text"
        >
            <a @click="getMessages()">Load more messages..</a>
        </p>
        <p v-else>
            This is the very beginning of this channel.
        </p>
        <ChatChannelMessage
            v-for="(message, index) in messages"
            :key="index"
            :message="message"
            :previous="messages[index-1]"
            :index="index"
        />
    </div>
</template>

<script>
	import ChatChannelMessage from '@/components/chat/channel/ChatChannelMessage';
	import ChatAreaMixin from '@/mixins/chat/chatAreaMixin';

    export default {
        components: {
            ChatChannelMessage
        },

		mixins: [ChatAreaMixin],

        computed: {
            channelId () {
                return this.$route.params.id;
            }
        },

		created() {
			this.$store.commit('chat/reset_chat_data');
			this.getMessages();
		},

		methods: {
            getMessages() {
				this.$store.dispatch('chat/fetchChannelMessages', {
					channelId: this.channelId,
					loadMoreNumber: this.loadMoreNumber
				});
			}
		},
    }
</script>
